<template>
  <Head :title="title" />

  <div class="flex h-screen bg-zinc-50">
    <div class="flex flex-col p-4 gap-4 bg-zinc-200 dark:bg-zinc-900">
      <CachetLogo class="h-auto w-48" />

      <div class="flex flex-col justify-between flex-1">
        <div>
          Navigation Bar.
        </div>
        <div>
          Bottom Navigation Bar.
        </div>
      </div>
    </div>
    <div class="flex flex-col flex-1 divide-y-2">
      <div class="p-4 flex justify-end">
        James Brooks
      </div>

      <div class="p-8">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import CachetLogo from '@/Components/CachetLogo.vue'

defineProps({
  title: String,
})
</script>
