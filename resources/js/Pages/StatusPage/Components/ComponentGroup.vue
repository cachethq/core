<template>
  <div class="rounded-md shadow overflow-hidden">
    <div class="flex items-center justify-between p-4 bg-zinc-100" :class="[! userCollapsed && 'border-b border-gray-200']">
      <h3 class="text-base font-semibold leading-6 text-gray-900">{{ name }}</h3>

      <button v-if="collapsable" @click="toggleCollapsedGroup" class="text-zinc-500">
        <PlusCircleIcon v-if="userCollapsed" class="h-6 w-6" />
        <MinusCircleIcon v-else class="h-6 w-6" />
      </button>
    </div>

    <div v-if="! userCollapsed" class="flex flex-col divide-y bg-white">
      <ComponentStatus v-for="component in components" :key="component.id" :component="component" />
    </div>
  </div>
</template>

<script>
import ComponentStatus from './ComponentStatus.vue'
import { PlusCircleIcon, MinusCircleIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    ComponentStatus,
    PlusCircleIcon,
    MinusCircleIcon,
  },
  props: {
    name: String,
    collapsable: {
      type: Boolean,
      default: true,
    },
    collapsed: {
      type: Boolean,
      default: false,
    },
    components: Array,
  },
  data() {
    return {
      userCollapsed: this.collapsed,
    }
  },
  methods: {
    toggleCollapsedGroup() {
      this.userCollapsed = !this.userCollapsed
    },
  },
}
</script>
