@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $incident->name }}

**{{ $update->status->getLabel() }}** &middot; {{ $update->created_at->toDayDateTimeString() }}

{{ $update->message }}

@component('mail::button', ['url' => route('cachet.status-page.incident', ['incident' => $incident])])
{{ __('cachet::subscriber.mail.incident_updated.button') }}
@endcomponent
@endcomponent
