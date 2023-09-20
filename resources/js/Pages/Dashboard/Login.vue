<template>
  <Head title="Log In" />

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <CachetLogo class="mx-auto h-14 w-auto" />
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-gray-400">Log in</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" action="#" method="POST">
      <div>
        <Label for="email">Email Address</Label>
        <div class="mt-2">
          <Input id="email" name="email" type="email" autocomplete="email" required />
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <Label for="password">Password</Label>
          <div class="text-sm">
            <a href="#" class="font-semibold text-primary-600 hover:text-primary-500">Forgot your password?</a>
          </div>
        </div>
        <div class="mt-2">
          <Input id="password" name="password" type="password" autocomplete="current-password" required />
        </div>
      </div>

      <div>
        <Button type="submit">Log In</Button>
      </div>
    </form>

    <p class="mt-4 text-center text-sm text-gray-400 tracking-tight">
      Cachet {{ $page.props.cachet_version }}
    </p>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Authentication from '@/Layouts/Authentication.vue'
import CachetLogo from '@/Components/CachetLogo.vue'
import Input from '@/Components/Input.vue'
import Button from '@/Components/Button.vue'
import Label from '@/Components/Label.vue'

export default {
  components: {
    Label,
    Button,
    CachetLogo,
    Head,
    Input,
  },
  layout: [Authentication],
}
</script>
