<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Header settings
        $this->migrator->add('branding.header_bg_color', null);
        $this->migrator->add('branding.header_text_color', null);
        $this->migrator->add('branding.header_logo', null);
        $this->migrator->add('branding.header_logo_height', 32);
        $this->migrator->add('branding.header_links', null);
        $this->migrator->add('branding.show_subscribe_button', true);
        $this->migrator->add('branding.show_dashboard_link', true);

        // Footer settings
        $this->migrator->add('branding.footer_bg_color', null);
        $this->migrator->add('branding.footer_text_color', null);
        $this->migrator->add('branding.footer_copyright', null);
        $this->migrator->add('branding.show_cachet_branding', true);
        $this->migrator->add('branding.footer_links', null);

        // General branding
        $this->migrator->add('branding.page_bg_color', null);
        $this->migrator->add('branding.favicon_url', null);
        $this->migrator->add('branding.custom_css', null);
    }

    public function down(): void
    {
        $this->migrator->delete('branding.header_bg_color');
        $this->migrator->delete('branding.header_text_color');
        $this->migrator->delete('branding.header_logo');
        $this->migrator->delete('branding.header_logo_height');
        $this->migrator->delete('branding.header_links');
        $this->migrator->delete('branding.show_subscribe_button');
        $this->migrator->delete('branding.show_dashboard_link');
        $this->migrator->delete('branding.footer_bg_color');
        $this->migrator->delete('branding.footer_text_color');
        $this->migrator->delete('branding.footer_copyright');
        $this->migrator->delete('branding.show_cachet_branding');
        $this->migrator->delete('branding.footer_links');
        $this->migrator->delete('branding.page_bg_color');
        $this->migrator->delete('branding.favicon_url');
        $this->migrator->delete('branding.custom_css');
    }
};
