@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
@php($previousWindow = $previousScheduledAt?->toDayDateTimeString().($previousCompletedAt ? ' – '.$previousCompletedAt->toDayDateTimeString() : ''))
@php($newWindow = $schedule->scheduled_at->toDayDateTimeString().($schedule->completed_at ? ' – '.$schedule->completed_at->toDayDateTimeString() : ''))
# {{ $schedule->name }}

<p class="sub"><strong>{{ __('cachet::subscriber.mail.schedule_rescheduled.previously') }}</strong> <s>{{ $previousWindow }}</s></p>

@component('mail::panel')
**{{ __('cachet::subscriber.mail.schedule_rescheduled.now') }}**
{{ $newWindow }}
@endcomponent

{{ $schedule->message }}

@component('mail::button', ['url' => route('cachet.status-page.schedule', ['schedule' => $schedule])])
{{ __('cachet::subscriber.mail.schedule_rescheduled.button') }}
@endcomponent
@endcomponent
