@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $incident->name }}

**{{ $incident->status->getLabel() }}** &middot; {{ $incident->timestamp->toDayDateTimeString() }}

{{ $incident->message }}

@component('mail::button', ['url' => route('cachet.status-page.incident', ['incident' => $incident])])
{{ __('cachet::subscriber.mail.new_incident.button') }}
@endcomponent
@endcomponent
