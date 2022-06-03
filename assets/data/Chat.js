import User from './User';
import { reactive } from 'vue';
import Message from "./Message";

export default class {
    currentUser = null;
    socket = null;
    users = reactive({});
    connected = false;
    onConnectSuccess = null;

    constructor(emitter) {
        this.emitter = emitter;
    }

    connect(onConnectSuccess) {
        this.socket = new WebSocket('ws://localhost:8085');

        const handleConnection = (exists = false) => {
            const name = prompt(exists ? 'That name is in use, please choose another.' : 'What is your name?');
            this.socket.send(name);

            this.socket.addEventListener(
                'message',
                (event) => {
                    console.log(event.data);
                    if ('OK' === event.data) {
                        this.currentUser = new User(name, true);
                        this.users[name] = this.currentUser;

                        this.socket.addEventListener('message', (event) => {
                            const data = JSON.parse(event.data);

                            switch (data.type) {
                                case 'user-message':
                                    this.users[data.user].history.push(new Message(data.text, false));
                                    break;
                                case 'user-connect':
                                    this.users[data.user] = new User(data.user, true);
                                    break;
                                case 'user-disconnect':
                                    delete this.users[data.user];
                                    break;
                            }
                        });

                        onConnectSuccess();
                    } else if ('EXISTS' === event.data) {
                        handleConnection(true);
                    }
                },
                {
                    once: true
                }
            );
        };

        this.socket.addEventListener('open', () => {
            handleConnection();
        });
    }

    sendMessage(user, text) {
        const message = new Message(text, true);
        user.history.push(message);

        this.socket.send(JSON.stringify({
            user: user.details.name,
            text: text
        }));
    }
}
