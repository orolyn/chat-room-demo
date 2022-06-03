import { Controller } from '@hotwired/stimulus';

import { createApp } from 'vue'
import Application from '../components/Application'
import Chat from "../data/Chat";
import User from "../data/User";
import { TinyEmitter } from "tiny-emitter";

export default class extends Controller {
    connect() {
        const emitter = new TinyEmitter();
        const chat = new Chat(emitter);

        chat.connect(
            () => {
                const app = createApp(Application);

                app.provide('chat', chat);
                app.provide('emitter', emitter);

                app.mount(this.element);
            }
        );
    }
}
