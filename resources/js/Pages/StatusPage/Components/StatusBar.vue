<template>
  <div class="flex flex-col gap-4">
    <div class="rounded-md bg-red-100 p-4">
      <div class="flex sm:items-center">
        <ExclamationTriangleIcon class="w-5 h-5 text-red-400" aria-hidden="true" />
        <div class="ml-3 flex-1 md:items-center md:flex md:justify-between">
          <p class="text-sm text-red-800">We're experiencing major issues.</p>
          <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0 text-red-800">
            Updated <time datetime="2008-02-14 20:00" title="2008-02-14 20:00" class="underline decoration-dotted underline-offset-2 cursor-help">10 minutes</time> ago
          </p>
        </div>
      </div>
    </div>

    <div class="rounded-md bg-amber-100 p-4">
      <div class="flex sm:items-center">
        <ExclamationCircleIcon class="w-5 h-5 text-amber-400" aria-hidden="true" />
        <div class="ml-3 flex-1 md:items-center md:flex md:justify-between">
          <p class="text-sm text-amber-800">This is a warning.</p>
          <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0 text-amber-800">
            Updated <time datetime="2008-02-14 20:00" title="2008-02-14 20:00" class="underline decoration-dotted underline-offset-2 cursor-help">10 minutes</time> ago
          </p>
        </div>
      </div>
    </div>

    <div class="rounded-md bg-blue-100 p-4">
      <div class="flex sm:items-center">
        <InformationCircleIcon class="w-5 h-5 text-blue-400" aria-hidden="true" />
        <div class="ml-3 flex-1 md:items-center md:flex md:justify-between">
          <p class="text-sm text-blue-800">Here is some information about something.</p>
          <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0 text-blue-800">
            Updated <time datetime="2008-02-14 20:00" title="2008-02-14 20:00" class="underline decoration-dotted underline-offset-2 cursor-help">10 minutes</time> ago
          </p>
        </div>
      </div>
    </div>

    <div class="rounded-md bg-green-100 p-4">
      <div class="flex sm:items-center">
        <CheckCircleIcon class="w-5 h-5 text-green-400" aria-hidden="true" />
        <div class="ml-3 flex-1 md:items-center md:flex md:justify-between">
          <p class="text-sm text-green-800">Something successful happened.</p>
          <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0 text-green-800">
            Updated <time datetime="2008-02-14 20:00" title="2008-02-14 20:00" class="underline decoration-dotted underline-offset-2 cursor-help">10 minutes</time> ago
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ExclamationTriangleIcon, ExclamationCircleIcon, InformationCircleIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    ExclamationTriangleIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
    CheckCircleIcon,
  },
}
</script>
