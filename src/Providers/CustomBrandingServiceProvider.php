<?php

namespace Cachet\Providers;

use Cachet\Facades\CachetView;
use Cachet\Settings\BrandingSettings;
use Cachet\View\RenderHook;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * Injects custom branding (header, footer, styles) into the Cachet status page
 */
class CustomBrandingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->app->booted(function () {
            $this->registerBranding();
        });
    }

    private function registerBranding(): void
    {
        try {
            $branding = app(BrandingSettings::class);
        } catch (\Throwable) {
            return;
        }
        $headerLinks = $branding->getHeaderLinks();
        $footerLinks = $branding->getFooterLinks();

        $shared = [
            'brandingHeaderBgColor'      => $branding->header_bg_color,
            'brandingHeaderTextColor'     => $branding->header_text_color,
            'brandingHeaderLogo'          => $branding->header_logo,
            'brandingHeaderLogoHeight'    => $branding->header_logo_height,
            'brandingHeaderLinks'         => $headerLinks,
            'brandingShowSubscribe'       => $branding->show_subscribe_button,
            'brandingShowDashboardLink'   => $branding->show_dashboard_link,
            'brandingFooterBgColor'       => $branding->footer_bg_color,
            'brandingFooterTextColor'     => $branding->footer_text_color,
            'brandingFooterCopyright'     => $branding->footer_copyright,
            'brandingShowCachetBranding'  => $branding->show_cachet_branding,
            'brandingFooterLinks'         => $footerLinks,
            'brandingPageBgColor'         => $branding->page_bg_color,
            'brandingFaviconUrl'          => $branding->favicon_url,
            'brandingCustomCss'           => $branding->custom_css,
        ];

        foreach ($shared as $key => $value) {
            view()->share($key, $value);
        }
        CachetView::registerRenderHook(
            RenderHook::STATUS_PAGE_BODY_BEFORE,
            fn () => view('cachet::components.branding.styles', $shared)->render()
        );
        if ($branding->header_logo) {
            CachetView::registerRenderHook(
                RenderHook::STATUS_PAGE_NAVIGATION_BEFORE,
                function () use ($branding) {
                    $logoUrl = Storage::url($branding->header_logo);
                    $height  = $branding->header_logo_height;
                    $style   = $branding->header_text_color
                        ? 'color: '.$branding->header_text_color
                        : '';

                    return <<<HTML
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var headerLink = document.querySelector('.flex.items-center.justify-between.border-b a');
                        if (headerLink) {
                            headerLink.innerHTML = '<img src="{$logoUrl}" alt="Logo" class="branding-custom-logo" style="height:{$height}px;width:auto;" />';
                        }
                    });
                    </script>
                    HTML;
                }
            );
        }
        if (count($headerLinks) > 0) {
            CachetView::registerRenderHook(
                RenderHook::STATUS_PAGE_NAVIGATION_AFTER,
                fn () => view('cachet::components.branding.header-links', $shared)->render()
            );
        }

        if ($branding->footer_copyright || count($footerLinks) > 0) {
            CachetView::registerRenderHook(
                RenderHook::STATUS_PAGE_BODY_AFTER,
                fn () => view('cachet::components.branding.footer-content', $shared)->render()
            );
        }
        if ($branding->favicon_url) {
            CachetView::registerRenderHook(
                RenderHook::STATUS_PAGE_BODY_BEFORE,
                function () use ($branding) {
                    $url = e($branding->favicon_url);

                    return <<<HTML
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var link = document.querySelector("link[rel*='icon']");
                        if (link) { link.href = '{$url}'; }
                        else {
                            link = document.createElement('link');
                            link.rel = 'shortcut icon';
                            link.href = '{$url}';
                            document.head.appendChild(link);
                        }
                    });
                    </script>
                    HTML;
                }
            );
        }

        $this->registerDashboardBranding($branding, $shared);
    }


    private function registerDashboardBranding(BrandingSettings $branding, array $shared): void
    {
        $hasAnyStyle = $branding->header_bg_color || $branding->page_bg_color
                    || $branding->header_text_color || $branding->custom_css;

        if ($hasAnyStyle) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('cachet::components.branding.login-styles', $shared)->render()
            );
        }
        $footerLinks = $branding->getFooterLinks();
        if ($branding->footer_copyright || count($footerLinks) > 0) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('cachet::components.branding.login-footer', $shared)->render()
            );
        }
        if ($branding->favicon_url) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::HEAD_END,
                function () use ($branding) {
                    $url = e($branding->favicon_url);

                    return '<link rel="shortcut icon" href="'.$url.'" />';
                }
            );
        }
    }
}
