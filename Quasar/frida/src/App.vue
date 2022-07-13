<template>
  <router-view />
</template>
<script>
import { Dark } from 'quasar'
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'App',
  setup () {
    Dark.set(true)
  }
})
</script>
