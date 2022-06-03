import { reactive } from 'vue';

export default class {
    details = reactive({});
    history = reactive([]);

    constructor(name, online = true) {
        this.details.name = name;
        this.details.online = online;
    }
}
