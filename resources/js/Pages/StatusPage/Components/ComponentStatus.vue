<template>
  <div class="p-4">
    <div class="flex sm:items-center">
      <div>
        <CheckCircleIcon v-if="component.status === 1" class="w-6 h-6 sm:h-8 sm:w-8 text-green-500" />
        <InformationCircleIcon v-if="component.status === 2" class="w-6 h-6 sm:h-8 sm:w-8 text-blue-500" />
        <ExclamationCircleIcon v-if="component.status === 3" class="w-6 h-6 sm:h-8 sm:w-8 text-amber-500" />
        <ExclamationTriangleIcon v-if="component.status === 4" class="w-6 h-6 sm:h-8 sm:w-8 text-red-500" />
      </div>

      <div class="ml-3 flex-1 md:items-center md:flex md:justify-between">
        <p class="font-semibold">{{ component.name }}</p>
        <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0" :class="badgeColors">
          {{ component.human_readable_status }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { CheckCircleIcon } from '@heroicons/vue/24/outline'
import { InformationCircleIcon} from '@heroicons/vue/24/outline'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    CheckCircleIcon,
    InformationCircleIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
  },
  props: {
    component: Object,
  },
  computed: {
    badgeColors() {
      return {
        'text-green-800': this.component.status === 1,
        'text-blue-800': this.component.status === 2,
        'text-amber-800': this.component.status === 3,
        'text-red-800': this.component.status === 4,
      }
    }
  }
}
</script>
