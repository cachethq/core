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
      <div class="flex items-center gap-3">
        <input id="remember-me" v-model="form.remember" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
        <Label for="remember-me">Remember Me</Label>
      </div>

      <div class="text-sm leading-6">
        <Link href="/status/forgot-password" class="font-semibold text-primary-600 hover:text-primary-500">Forgot password?</Link>
      </div>
    </div>

    <div>
      <Button type="submit">Log In</Button>
    </div>
  </form>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
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
    Link,
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
