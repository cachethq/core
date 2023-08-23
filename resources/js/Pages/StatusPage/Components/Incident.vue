<template>
  <div v-if="incident" class="flex flex-col gap-y-4">
    <h2 class="font-semibold text-lg">{{ incident.name }}</h2>

    <div class="prose" v-html="incident.description" />

    <div v-if="incident.updates.length" class="flow-root">
      <ul role="list" class="-mb-8">
        <li v-for="(update, updateIdx) in incident.updates" :key="update.id">
          <div class="relative pb-8">
            <span v-if="updateIdx !== incident.updates.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true" />
            <div class="relative flex space-x-3">
              <div>
                <span :class="[updateBackground(update), 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
                  <component :is="updateIcon(update)" class="h-5 w-5 text-white" aria-hidden="true" />
                </span>
              </div>
              <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                <div>
                  <p class="text-sm text-slate-500 dark:text-slate-300">
                    {{ update.description }}
                  </p>
                </div>
                <div class="whitespace-nowrap text-right text-sm text-slate-500 dark:text-slate-400">
                  <time :datetime="update.created_at">{{ update.created_at }}</time>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { XCircleIcon, ExclamationCircleIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    XCircleIcon,
    ExclamationCircleIcon,
    CheckCircleIcon,
  },
  props: {
    incident: Object,
  },
  methods: {
    updateBackground(update) {
      switch (update.status) {
      case 1:
        return 'bg-red-400'
      case 2:
        return 'bg-orange-400'
      case 3:
        return 'bg-blue-400'
      default:
        return 'bg-green-400'
      }
    },
    updateIcon(update) {
      switch (update.status) {
      case 1:
        return 'XCircleIcon'
      case 2:
        return 'ExclamationCircleIcon'
      case 3:
        return 'IconInformationCircle'
      case 4:
        return 'CheckCircleIcon'
      }
    },
  },
}
</script>
