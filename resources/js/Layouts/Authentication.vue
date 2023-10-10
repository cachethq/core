<template>
  <div class="flex flex-col h-screen justify-between gap-12">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm mt-10">
      <CachetLogo class="mx-auto h-14 w-auto" />
    </div>

    <div class="justify-self-start flex-1">
      <div class="px-8 py-12 lg:px-8 sm:mx-auto sm:w-full sm:max-w-sm bg-white dark:bg-zinc-950 rounded-lg shadow-lg border dark:border-gray-700">
        <slot />
      </div>
    </div>

    <div class="border-t py-12 dark:border-gray-700">
      <div class="flex items-center justify-center gap-2">
        <div class="text-sm tracking-tight text-gray-400">Powered by</div>
        <a href="https://cachethq.io" :title="`Cachet ${$page.props.cachet_version}`" rel="noreferrer nofollow">
          <CachetLogo class="h-8 w-auto" />
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import CachetLogo from '@/Components/CachetLogo.vue'
</script>
