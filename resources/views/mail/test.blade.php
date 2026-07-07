@component('cachet::mail.message')
# {{ __('cachet::settings.manage_notifications.test_email_heading') }}

{{ __('cachet::settings.manage_notifications.test_email_body', ['app' => $appName]) }}

@component('mail::button', ['url' => $statusPageUrl])
{{ __('cachet::settings.manage_notifications.test_email_button') }}
@endcomponent
@endcomponent
