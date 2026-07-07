@component('cachet::mail.message', ['unsubscribeUrl' => $unsubscribeUrl])
# {{ $incident->name }}

<p class="sub"><strong>{{ $update->status->getLabel() }}</strong> &middot; {{ $update->created_at->toDayDateTimeString() }}</p>

{{ $update->message }}

@component('mail::button', ['url' => route('cachet.status-page.incident', ['incident' => $incident])])
{{ __('cachet::subscriber.mail.incident_updated.button') }}
@endcomponent
@endcomponent
