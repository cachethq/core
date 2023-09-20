<template>
  <Head :title="title" />

  <div class="bg-red-300">
    <slot />
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  title: String,
})
</script>
