<script setup>
import Dashboard from '@/Layouts/Dashboard.vue'
import Button from '@/Components/Button.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
</script>

<template>
  <Dashboard title="Kitchen Sink - Demo Components">
    <Button>Primary Button</Button>
    <SecondaryButton>Secondary Button</SecondaryButton>
    <DangerButton>Danger Button</DangerButton>
  </Dashboard>
</template>
