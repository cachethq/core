<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('theme.dark_mode', 'system'));
        rescue(fn () => $this->migrator->add('theme.light_background', 'rgba(249, 250, 251, 1)'));
        rescue(fn () => $this->migrator->add('theme.light_text', 'rgba(63, 63, 70, 1)'));
        rescue(fn () => $this->migrator->add('theme.dark_background', 'rgba(24, 24, 27, 1)'));
        rescue(fn () => $this->migrator->add('theme.dark_text', 'rgba(212, 212, 216, 1)'));
        rescue(fn () => $this->migrator->add('theme.font_family_sans', 'sans-serif'));
        rescue(fn () => $this->migrator->add('theme.zinc_50', 'rgba(250, 250, 250, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_100', 'rgba(244, 244, 245, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_200', 'rgba(228, 228, 231, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_300', 'rgba(212, 212, 216, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_400', 'rgba(161, 161, 170, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_500', 'rgba(113, 113, 122, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_600', 'rgba(82, 82, 91, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_700', 'rgba(63, 63, 70, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_800', 'rgba(39, 39, 42, 1)'));
        rescue(fn () => $this->migrator->add('theme.zinc_900', 'rgba(24, 24, 27, 1)'));
        rescue(fn () => $this->migrator->add('theme.white', 'rgba(255, 255, 255, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_50', 'rgba(230, 247, 237, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_100', 'rgba(193, 237, 206, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_200', 'rgba(155, 227, 177, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_300', 'rgba(116, 217, 148, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_400', 'rgba(77, 206, 118, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_500', 'rgba(4, 193, 71, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_600', 'rgba(3, 173, 64, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_700', 'rgba(3, 143, 52, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_800', 'rgba(2, 114, 39, 1)'));
        rescue(fn () => $this->migrator->add('theme.primary_900', 'rgba(1, 86, 26, 1)'));
    }
};
