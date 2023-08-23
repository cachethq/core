<template>
  <div class="rounded-md border">
    <div class="bg-blue-600 px-4 py-5 font-semibold flex items-center rounded-t-md text-white">
      <div>Past Incidents</div>
    </div>

    <div class="flex flex-col divide-y">
      <slot />
    </div>
  </div>
</template>

<script>
export default {

}
</script>
