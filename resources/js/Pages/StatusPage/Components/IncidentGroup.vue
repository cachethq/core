<template>
  <div class="px-4 py-5 flex flex-col bg-white dark:bg-slate-700">
    <div class="font-semibold text-sm text-slate-500 dark:text-slate-400">{{ timestamp }}</div>

    <div class="flex flex-col">
      <Incident v-for="incident in incidents" v-if="incidents.length" :incident="incident" />
      <div v-else>Nothing to see here.</div>
    </div>
  </div>
</template>

<script>
import Incident from './Incident.vue'

export default {
  components: {Incident},
  props: {
    incidents: {
      type: Array,
      default: () => [],
    },
    timestamp: String,
  },
}
</script>
