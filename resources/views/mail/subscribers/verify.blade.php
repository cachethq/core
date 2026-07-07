@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ __('cachet::subscriber.mail.verify.heading') }}

{{ __('cachet::subscriber.mail.verify.body', ['app' => $appName]) }}

@component('mail::button', ['url' => $verificationUrl])
{{ __('cachet::subscriber.mail.verify.button') }}
@endcomponent

{{ __('cachet::subscriber.mail.verify.ignore', ['app' => $appName]) }}
@endcomponent
