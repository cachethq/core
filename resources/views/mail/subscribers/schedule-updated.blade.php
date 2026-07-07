@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
@php($window = $schedule->scheduled_at->toDayDateTimeString().($schedule->completed_at ? ' – '.$schedule->completed_at->toDayDateTimeString() : ''))
# {{ $schedule->name }}

<p class="sub"><strong>{{ __('cachet::subscriber.mail.schedule_updated.updated_at') }}</strong> {{ $update->created_at->toDayDateTimeString() }} &middot; <strong>{{ __('cachet::subscriber.mail.new_schedule.scheduled_for') }}</strong> {{ $window }}</p>

{{ $update->message }}

@component('mail::button', ['url' => route('cachet.status-page.schedule', ['schedule' => $schedule])])
{{ __('cachet::subscriber.mail.schedule_updated.button') }}
@endcomponent
@endcomponent
