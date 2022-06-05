<script>
import UserWidget from './UserWidget';

export default {
    inject: ['chat', 'emitter'],
    data() {
        return {
            users: this.chat.users
        }
    },
    components: {
        UserWidget
    },
    methods: {
        openRoom(user) {
            this.emitter.emit('openRoom', user);
        }
    }
}
</script>

<template>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-search"></i></span>
        </div>
        <input type="text" class="form-control" placeholder="Search...">
    </div>
    <ul class="list-unstyled chat-list mt-2 mb-0">
        <li v-for="user in users" class="clearfix" @click="openRoom(user)">
            <UserWidget :user="user" />
        </li>
    </ul>
</template>
