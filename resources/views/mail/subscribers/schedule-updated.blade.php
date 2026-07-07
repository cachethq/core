@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $schedule->name }}

<p class="sub"><strong>{{ __('cachet::subscriber.mail.schedule_updated.updated_at') }}</strong> {{ $update->created_at->toDayDateTimeString() }}</p>

{{ $update->message }}

@component('mail::button', ['url' => route('cachet.status-page.schedule', ['schedule' => $schedule])])
{{ __('cachet::subscriber.mail.schedule_updated.button') }}
@endcomponent
@endcomponent
