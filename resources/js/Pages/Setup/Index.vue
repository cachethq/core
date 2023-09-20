<template>
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <CachetLogo class="mx-auto h-14 w-auto" />
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-gray-400">Set Up</h2>
  </div>
</template>

<script>
import Authentication from '@/Layouts/Authentication.vue'
import CachetLogo from '@/Components/CachetLogo.vue'

export default {
  components: {CachetLogo},
  layout: [Authentication],
}
</script>
