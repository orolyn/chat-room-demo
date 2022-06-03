Orolyn Chatroom Demo Application
================================

Work in progress.

This is a demo application for the Orolyn Library.

Purpose
=======

To put the Orolyn library's Concurrency and Net tools to the test, and find all the bugs.

Installation
============

Build the docker environment

    docker-compose up -d --build

Build the assets (I haven't figured out how to make npm install the correct node version in the container yet).

    nvm use 18
    yarn install
