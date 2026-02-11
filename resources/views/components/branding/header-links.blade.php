{{-- Custom branding: extra header navigation links --}}
@if(count($brandingHeaderLinks) > 0)
<div class="branding-header-links flex items-center gap-3">
    @foreach($brandingHeaderLinks as $link)
    <a href="{{ $link['url'] }}"
       class="text-sm font-medium transition hover:opacity-80"
       @if(str_starts_with($link['url'], 'http')) target="_blank" rel="noopener" @endif
       style="{{ $brandingHeaderTextColor ? 'color: '.$brandingHeaderTextColor : '' }}">
        {{ $link['label'] }}
    </a>
    @endforeach
</div>
@endif
