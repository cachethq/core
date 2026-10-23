@inject('captcha', 'Cachet\Captcha\CaptchaManager')

@if ($captcha->driver()->enabled())
    <div data-slot="captcha" class="flex flex-col gap-1.5">
        {{ $captcha->driver()->render() }}

        @error('captcha')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
@endif
