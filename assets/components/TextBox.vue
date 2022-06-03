<template>
  <v-alert
      type="success"
  >{{ message }}</v-alert>
</template>

<script>
export default {
  name: 'Home',
  props: {},
  data: () => ({
    message: 'Hello Symfony + Vue.js!',
  }),
}
</script>
