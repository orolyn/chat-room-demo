<?php

namespace App;

use Orolyn\Collection\Dictionary;
use Orolyn\Concurrency\Application;
use Orolyn\Net\Http\FailedHttpRequestException;
use Orolyn\Net\Http\HttpRequestContext;
use Orolyn\Net\Http\HttpServer;
use Orolyn\Net\Http\WebSocket\InvalidWebSocketContextException;
use Orolyn\Net\Http\WebSocket\WebSocket;
use Orolyn\Net\Http\WebSocket\WebSocketClosedException;
use Orolyn\Net\Http\WebSocket\WebSocketMessage;
use Orolyn\Net\IPAddress;
use Orolyn\Net\IPEndPoint;
use Psr\Log\LoggerInterface;
use function Orolyn\Lang\Async;
use function Orolyn\Lang\Suspend;

class ApplicationServer extends Application
{
    private HttpServer $httpServer;
    private Dictionary $users;

    public function __construct(
        private LoggerInterface $logger
    ) {
        $this->users = new Dictionary();
    }

    public function main(): void
    {
        $this->httpServer = new HttpServer();
        $this->httpServer->listen(new IPEndPoint(IPAddress::parse('0.0.0.0'), 8085));

        while ($this->httpServer->isListening()) {
            try {
                $context = $this->httpServer->accept();
                Async(fn () => $this->handleRequest($context));
            } catch (FailedHttpRequestException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }

    /**
     * Handle the HTTP request and convert it into a Websocket.
     *
     * @param HttpRequestContext $context
     * @return void
     */
    private function handleRequest(HttpRequestContext $context): void
    {
        $name = null;

        try {
            try {
                $socket = WebSocket::create($context);
            } catch (InvalidWebSocketContextException $exception) {
                $this->logger->error($exception->getMessage());

                return;
            }

            // Perform initial handshake
            for (;;) {
                $name = $socket->receive()->getData();

                if ($this->users->contains($name)) {
                    $socket->send(new WebSocketMessage('EXISTS'));

                    continue;
                }


                $socket->send(new WebSocketMessage('OK'));
                $this->connectUser($name, $socket);

                $this->users->add($name, $socket);

                break;
            }

            // Process incoming messages.
            for (;;) {
                $message = json_decode($socket->receive()->getData(), true);

                /** @var WebSocket $otherSocket */
                if ($this->users->try($message['user'], $otherSocket)) {
                    try {
                        $otherSocket->send(
                            new WebSocketMessage(
                                json_encode(
                                    [
                                        'type' => 'user-message',
                                        'user' => $name,
                                        'text' => $message['text']
                                    ]
                                )
                            )
                        );
                    } catch (WebSocketClosedException $exception) {
                        $this->disconnectUser($message['user']);
                    }
                }
            }
        } catch (WebSocketClosedException $exception) {
            // This user a gone, stop listening.
            $this->disconnectUser($name);
            return;
        }
    }

    /**
     * Inform all other users that this user has connected.
     *
     * @param string $connectedUser
     * @return void
     */
    private function connectUser(string $connectedUser, WebSocket $connectedSocket): void
    {
        foreach ($this->users as $user => $socket) {
            try {
                // Inform the other user about this user.
                $socket->send(
                    new WebSocketMessage(
                        json_encode(
                            [
                                'type' => 'user-connect',
                                'user' => $connectedUser
                            ]
                        )
                    )
                );

                // Inform this user about the other user.
                $connectedSocket->send(
                    new WebSocketMessage(
                        json_encode(
                            [
                                'type' => 'user-connect',
                                'user' => $user
                            ]
                        )
                    )
                );
            } catch (WebSocketClosedException $exception) {
                // Looks like that other user is disconnected, this could take a while :P
                $this->disconnectUser($user);
            }
        }
    }

    /**
     * Remove the user from the list and inform all other users this user has disconnected.
     *
     * @param string|null $name
     * @return void
     */
    private function disconnectUser(?string $disconnectedUser): void
    {
        if ($disconnectedUser) {
            $this->users->remove($disconnectedUser);
        }

        foreach ($this->users as $user => $socket) {
            try {
                $socket->send(
                    new WebSocketMessage(
                        json_encode(
                            [
                                'type' => 'user-disconnect',
                                'user' => $disconnectedUser
                            ]
                        )
                    )
                );
            } catch (WebSocketClosedException $exception) {
                // Looks like that other user is disconnected, like an endless cycle :/
                $this->disconnectUser($user);
            }
        }
    }
}
