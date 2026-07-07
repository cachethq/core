@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $schedule->name }}

<p class="sub"><strong>{{ __('cachet::subscriber.mail.new_schedule.scheduled_for') }}</strong> {{ $schedule->scheduled_at->toDayDateTimeString() }}@if ($schedule->completed_at) &ndash; {{ $schedule->completed_at->toDayDateTimeString() }}@endif</p>

{{ $schedule->message }}

@component('mail::button', ['url' => route('cachet.status-page.schedule', ['schedule' => $schedule])])
{{ __('cachet::subscriber.mail.new_schedule.button') }}
@endcomponent
@endcomponent
