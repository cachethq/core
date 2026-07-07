@component('cachet::mail.layout')
{{-- Header --}}
@slot('header')
<tr>
<td class="header">
<a href="{{ route('cachet.status-page') }}" style="display: inline-block;">
@if ($appBanner)
<img src="{{ \Illuminate\Support\Facades\Storage::url($appBanner) }}" class="logo-banner" alt="{{ $appName }}">
@else
<img src="{{ asset('vendor/cachethq/cachet/logo.png') }}" class="logo" alt="{{ $appName }}">
<span class="header-name">{{ $appName }}</span>
@endif
</a>
</td>
</tr>
@endslot

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{!! $subcopy !!}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
<tr>
<td>
<table class="footer" align="center" width="640" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
<p>
@isset($unsubscribeUrl)
<a href="{{ $unsubscribeUrl }}">{{ __('cachet::subscriber.mail.unsubscribe') }}</a> &middot;
@endisset
<a href="https://cachethq.io" rel="noopener">{{ __('cachet::cachet.mail.powered_by') }}</a>
</p>
</td>
</tr>
</table>
</td>
</tr>
@endslot
@endcomponent
