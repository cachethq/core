{{-- Custom branding: dynamic CSS variables and overrides --}}
<style id="custom-branding-css">
    @if($brandingHeaderBgColor)
    /* Header background override */
    .flex.items-center.justify-between.border-b {
        background-color: {{ $brandingHeaderBgColor }} !important;
    }
    @endif

    @if($brandingHeaderTextColor)
    /* Header text color override */
    .flex.items-center.justify-between.border-b,
    .flex.items-center.justify-between.border-b a,
    .flex.items-center.justify-between.border-b span,
    .flex.items-center.justify-between.border-b button {
        color: {{ $brandingHeaderTextColor }} !important;
    }
    @endif

    @if($brandingFooterBgColor)
    /* Footer background override */
    footer.flex.flex-col {
        background-color: {{ $brandingFooterBgColor }} !important;
    }
    @endif

    @if($brandingFooterTextColor)
    /* Footer text color override */
    footer.flex.flex-col,
    footer.flex.flex-col a,
    footer.flex.flex-col span {
        color: {{ $brandingFooterTextColor }} !important;
    }
    @endif

    @if($brandingPageBgColor)
    /* Page background override */
    html {
        background-color: {{ $brandingPageBgColor }} !important;
    }
    @endif

    @if(! $brandingShowSubscribe)
    /* Hide subscribe button when disabled via branding settings */
    [x-data*="subscribe"],
    .fi-modal[id*="subscribe"] {
        display: none !important;
    }
    @endif

    @if(! $brandingShowDashboardLink)
    /* Hide dashboard link when disabled via branding settings */
    a[href*="/dashboard"] {
        display: none !important;
    }
    @endif

    @if(! $brandingShowCachetBranding)
    /* Hide Cachet branding badge */
    footer .flex.items-center.justify-center.gap-2:first-child {
        display: none !important;
    }
    @endif

    @if($brandingHeaderLogo && $brandingHeaderLogoHeight)
    /* Custom header logo sizing */
    .branding-custom-logo {
        height: {{ $brandingHeaderLogoHeight }}px;
        width: auto;
    }
    @endif

    /* Custom branding footer section styling */
    .branding-footer-extra {
        border-top: 1px solid rgb(228 228 231 / 1); /* zinc-200 */
    }
    .dark .branding-footer-extra {
        border-top-color: rgb(63 63 70 / 1); /* zinc-700 */
    }

    @if($brandingCustomCss)
    /* User-defined custom CSS */
    {!! $brandingCustomCss !!}
    @endif
</style>
