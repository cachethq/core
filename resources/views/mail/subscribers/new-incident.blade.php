@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $incident->name }}

<p class="sub"><strong>{{ $incident->status->getLabel() }}</strong> &middot; {{ $incident->timestamp->toDayDateTimeString() }}</p>

{{ $incident->message }}

@component('mail::button', ['url' => route('cachet.status-page.incident', ['incident' => $incident])])
{{ __('cachet::subscriber.mail.new_incident.button') }}
@endcomponent
@endcomponent
