@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $schedule->name }}

<p class="sub"><strong>{{ __('cachet::subscriber.mail.schedule_completed.completed_at') }}</strong> {{ $schedule->completed_at->toDayDateTimeString() }}</p>

{{ __('cachet::subscriber.mail.schedule_completed.body') }}

@component('mail::button', ['url' => route('cachet.status-page.schedule', ['schedule' => $schedule])])
{{ __('cachet::subscriber.mail.schedule_completed.button') }}
@endcomponent
@endcomponent
