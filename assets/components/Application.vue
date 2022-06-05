<script>
import UserWidgetList from './UserWidgetList';
import ChatArea from "./ChatArea";

export default {
    inject: ['emitter'],
    components: {
        ChatArea,
        UserWidgetList
    }
}
</script>

<template>
    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card chat-app">
                    <div id="userlist" class="people-list">
                        <UserWidgetList />
                    </div>
                    <div id="chatroom" class="chat">
                        <ChatArea />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
