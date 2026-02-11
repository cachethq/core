@php
    $customLogo = null;
    $siteName = null;
    $logoHeight = 32;
    try {
        $branding = app(\Cachet\Settings\BrandingSettings::class);
        $customLogo = $branding->header_logo ?: null;
        $logoHeight = $branding->header_logo_height ?? 32;
        $appSettings = app(\Cachet\Settings\AppSettings::class);
        $siteName = $appSettings->name ?: null;
    } catch (\Throwable) {
    }
@endphp

@if($customLogo)
    <img src="{{ Storage::url($customLogo) }}"
         alt="{{ $siteName ?: 'Status Page' }}"
         style="height: {{ $logoHeight }}px; width: auto;" />
@elseif($siteName)
    <div style="font-size:1.25rem;font-weight:700;line-height:2rem;white-space:nowrap;"
         class="text-gray-950 dark:text-white">{{ $siteName }}</div>
@else
    <x-cachet::logo class="h-8" />
@endif
