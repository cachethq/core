{{-- Custom branding: login/auth page styles --}}
<style id="custom-branding-login-css">
    @if($brandingHeaderBgColor)
    /* Login page header/top bar area */
    .fi-simple-layout {
        --branding-header-bg: {{ $brandingHeaderBgColor }};
    }
    @endif

    @if($brandingPageBgColor)
    /* Login page background */
    body, .fi-simple-layout {
        background-color: {{ $brandingPageBgColor }} !important;
    }
    @endif

    @if($brandingHeaderTextColor)
    /* Login page heading text color */
    .fi-simple-header-heading,
    .fi-simple-header-subheading {
        color: {{ $brandingHeaderTextColor }} !important;
    }
    @endif

    @if($brandingCustomCss)
    /* User-defined custom CSS (also applies to login) */
    {!! $brandingCustomCss !!}
    @endif
</style>
