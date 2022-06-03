<script>
import {reactive, watch} from 'vue';
import UserWidgetList from './UserWidgetList';
import User from '../data/User';

export default {
    props: ['message', 'time', 'isMyself']
}
</script>

<template>
    <li class="clearfix">
        <div class="message-data" :class="{ 'text-right': !message.isMyself }">
            <img v-if="message.isMyself" :src="require('../images/user-avatar.png')" alt="avatar">
            <span class="message-data-time">10:10 AM, Today</span>
            <img v-if="!message.isMyself" :src="require('../images/user-avatar.png')" alt="avatar">
        </div>
        <div class="message"
             :class="{ 'my-message': message.isMyself, 'other-message float-right': !message.isMyself }">

            {{ message.text }}
        </div>
    </li>
</template>
