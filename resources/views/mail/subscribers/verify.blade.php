@component('cachet::mail.layout', ['title' => __('cachet::subscriber.mail.verify.subject'), 'unsubscribeUrl' => $unsubscribeUrl])
    <h1 style="margin: 0 0 12px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 24px; font-weight: 700; color: #18181b;">
        {{ __('cachet::subscriber.mail.verify.heading') }}
    </h1>
    <p style="margin: 0 0 32px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 15px; line-height: 24px; color: #52525b;">
        {{ __('cachet::subscriber.mail.verify.body', ['app' => $appName]) }}
    </p>
    <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 12px 28px; border-radius: 9999px; background-color: #16a34a; background-color: {{ $colors['accent'] }}; color: #ffffff; color: {{ $colors['accent-foreground'] }}; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 15px; font-weight: 600; text-decoration: none;">
        {{ __('cachet::subscriber.mail.verify.button') }}
    </a>
    <p style="margin: 32px 0 0; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 13px; line-height: 20px; color: #a1a1aa;">
        {{ __('cachet::subscriber.mail.verify.ignore', ['app' => $appName]) }}
    </p>
@endcomponent
