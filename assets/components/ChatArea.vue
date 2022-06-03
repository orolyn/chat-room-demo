<script>

import MessageWidget from "./MessageWidget";

export default {
    inject: ['emitter', 'chat'],
    data() {
        return {
            user: null,
            history: []
        }
    },
    mounted() {
        this.emitter.on('openRoom', (user) => {
            this.user = user;
            this.history = user.history;
        })
    },
    components: { MessageWidget },
    methods: {
        sendMessage(event) {
            const message = event.target.value;
            event.target.value = '';

            this.chat.sendMessage(this.user, message);
        }
    }
}

</script>

<template>
    <div v-if="user" class="chat-header clearfix">
        <div class="row">
            <div class="col-lg-6">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                    <img :src="require('../images/user-avatar.png')" alt="avatar">
                </a>
                <div class="chat-about">
                    <h6 class="m-b-0">{{ user.details.name }}</h6>
                </div>
            </div>
        </div>
    </div>
    <div v-if="user" class="chat-history">
        <ul class="m-b-0">
            <li class="clearfix" v-for="message in history">
                <MessageWidget :message="message" />
            </li>
        </ul>
    </div>
    <div v-if="user" class="chat-message clearfix">
        <div class="input-group mb-0">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-send"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Enter text here..." @keyup.enter="sendMessage">
        </div>
    </div>
</template>
