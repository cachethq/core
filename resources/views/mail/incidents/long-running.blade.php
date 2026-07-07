@component('cachet::mail.message')
# {{ __('cachet::incident.mail.long_running.heading') }}

<p class="sub"><strong>{{ $incident->name }}</strong> &middot; {{ $incident->status->getLabel() }}</p>

{{ __('cachet::incident.mail.long_running.body', [
    'since' => ($incident->updates->max('created_at') ?? $incident->created_at)->diffForHumans(),
]) }}

@component('mail::button', ['url' => $manageUrl])
{{ __('cachet::incident.mail.long_running.button') }}
@endcomponent
@endcomponent
