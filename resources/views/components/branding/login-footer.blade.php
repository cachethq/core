{{-- Custom branding: login page footer --}}
@if($brandingFooterCopyright || count($brandingFooterLinks) > 0)
<div class="branding-login-footer mt-4 text-center text-sm"
     style="{{ $brandingFooterTextColor ? 'color: '.$brandingFooterTextColor : 'color: rgb(113 113 122)' }}">

    @if(count($brandingFooterLinks) > 0)
    <nav class="flex flex-wrap items-center justify-center gap-3 mb-2">
        @foreach($brandingFooterLinks as $link)
        <a href="{{ $link['url'] }}"
           class="transition hover:opacity-80 underline-offset-2 hover:underline"
           @if(str_starts_with($link['url'], 'http')) target="_blank" rel="noopener" @endif
           style="{{ $brandingFooterTextColor ? 'color: '.$brandingFooterTextColor : '' }}">
            {{ $link['label'] }}
        </a>
        @endforeach
    </nav>
    @endif

    @if($brandingFooterCopyright)
    <p class="opacity-80">{{ $brandingFooterCopyright }}</p>
    @endif
</div>
@endif
