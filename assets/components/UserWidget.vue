<template>
    <img :src="require('../images/user-avatar.png')" alt="avatar">
    <div class="about">
        <div class="name">{{ user.details.name }}</div>
        <div class="status">
            <i class="fa fa-circle" :class="{ online: user.details.online, offline: !user.details.online }"></i>
            <span v-if="user.details.online">online</span>
            <span v-else>offline</span>
        </div>
    </div>
</template>

<script>
export default {
    props: ['user']
}
</script>
