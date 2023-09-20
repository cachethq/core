<template>
  <Dashboard title="Cachet">
    Hey there.

    {{ components }}
  </Dashboard>
</template>

<script setup>
import Dashboard from '@/Layouts/Dashboard.vue'

defineProps({
  components: Array,
})
</script>
