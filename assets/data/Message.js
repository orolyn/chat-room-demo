import { reactive } from 'vue';

export default class {
    constructor(text, isMyself) {
        this.text = text;
        this.isMyself = !!isMyself;
    }
}
