<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Branding settings for customizing the status page header, footer, and overall look.
 *
 * These settings are managed via the admin dashboard GUI and stored in the database.
 */
class BrandingSettings extends Settings
{

    public ?string $header_bg_color = null;

    public ?string $header_text_color = null;

    public ?string $header_logo = null;

    public int $header_logo_height = 32;

    public ?string $header_links = null;

    public bool $show_subscribe_button = true;

    public bool $show_dashboard_link = true;


    public ?string $footer_bg_color = null;

    public ?string $footer_text_color = null;

    public ?string $footer_copyright = null;

    public bool $show_cachet_branding = true;

    public ?string $footer_links = null;


    public ?string $page_bg_color = null;

    public ?string $favicon_url = null;

    public ?string $custom_css = null;

    public static function group(): string
    {
        return 'branding';
    }


    public function getHeaderLinks(): array
    {
        if (empty($this->header_links)) {
            return [];
        }

        return json_decode($this->header_links, true) ?: [];
    }

    public function getFooterLinks(): array
    {
        if (empty($this->footer_links)) {
            return [];
        }

        return json_decode($this->footer_links, true) ?: [];
    }
}
