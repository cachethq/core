<template>
  <Head title="Reset Your Password" />

  <form class="space-y-6" @submit.prevent="reset">
    <div>
      <Label for="email">Email Address</Label>
      <div class="mt-2">
        <Input id="email" v-model="form.email" name="email" type="email" autocomplete="email" required />
        <InputError :message="form.errors.email" />
      </div>
    </div>

    <div>
      <Button type="submit">Reset Password</Button>
    </div>
  </form>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Authentication from '@/Layouts/Authentication.vue'
import Input from '@/Components/Input.vue'
import Button from '@/Components/Button.vue'
import Label from '@/Components/Label.vue'
import InputError from '@/Components/InputError.vue'

export default {
  components: {
    InputError,
    Label,
    Button,
    Head,
    Input,
  },
  layout: [Authentication],
  data() {
    return {
      form: this.$inertia.form({
        email: '',
      }),
    }
  },
  methods: {
    reset() {
      this.form.post('/status/forgot-password', {
        preserveState: true,
      })
    },
  },
}
</script>
