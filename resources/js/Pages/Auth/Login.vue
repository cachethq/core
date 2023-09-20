<template>
  <Head title="Log In" />

  <form class="space-y-6" @submit.prevent="login">
    <div>
      <Label for="email">Email Address</Label>
      <div class="mt-2">
        <Input id="email" v-model="form.email" name="email" type="email" autocomplete="email" required />
        <InputError :message="form.errors.email" />
      </div>
    </div>

    <div>
      <Label for="password">Password</Label>
      <div class="mt-2">
        <Input id="password" v-model="form.password" name="password" type="password" autocomplete="current-password" required />
        <InputError :message="form.errors.password" />
      </div>
    </div>

    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <input id="remember-me" v-model="form.remember" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
        <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">Remember me</label>
      </div>

      <div class="text-sm leading-6">
        <a href="#" class="font-semibold text-primary-600 hover:text-primary-500">Forgot password?</a>
      </div>
    </div>

    <div>
      <Button type="submit">Log In</Button>
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
        password: '',
        remember: true,
      }),
    }
  },
  methods: {
    login() {
      this.form.post('/status/login', {
        preserveState: true,
      })
    },
  },
}
</script>
