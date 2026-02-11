{{-- Custom branding: footer content injected via render hooks --}}
@if($brandingFooterCopyright || count($brandingFooterLinks) > 0)
<div class="branding-footer-extra px-8 py-4 text-center text-sm"
     style="{{ $brandingFooterBgColor ? 'background-color: '.$brandingFooterBgColor : '' }}; {{ $brandingFooterTextColor ? 'color: '.$brandingFooterTextColor : '' }}">

    @if(count($brandingFooterLinks) > 0)
    <nav class="branding-footer-links flex flex-wrap items-center justify-center gap-4 mb-2">
        @foreach($brandingFooterLinks as $link)
        <a href="{{ $link['url'] }}"
           class="font-medium transition hover:opacity-80 underline-offset-2 hover:underline"
           @if(str_starts_with($link['url'], 'http')) target="_blank" rel="noopener" @endif
           style="{{ $brandingFooterTextColor ? 'color: '.$brandingFooterTextColor : '' }}">
            {{ $link['label'] }}
        </a>
        @endforeach
    </nav>
    @endif

    @if($brandingFooterCopyright)
    <p class="branding-footer-copyright opacity-80">{{ $brandingFooterCopyright }}</p>
    @endif
</div>
@endif
