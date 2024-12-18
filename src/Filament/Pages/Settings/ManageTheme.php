<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\ThemeSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageTheme extends SettingsPage
{
    protected static string $settings = ThemeSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\FileUpload::make('app_banner')
                        ->image()
                        ->imageEditor()
                        ->label(__('Banner Image'))
                        ->disk('public')
                        ->columnSpanFull(),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Select::make('accent')
                        ->label(__('Accent Color'))
                        ->options([
                            'cachet' => '<div class="theme-swatch" style="--swatch: var(--theme-primary-400)"></div> Cachet',
                            'red' => '<div class="theme-swatch" style="--swatch: var(--theme-red-400)"></div> Red',
                            'orange' => '<div class="theme-swatch" style="--swatch: var(--theme-orange-400)"></div> Orange',
                            'amber' => '<div class="theme-swatch" style="--swatch: var(--theme-amber-400)"></div> Amber',
                            'yellow' => '<div class="theme-swatch" style="--swatch: var(--theme-yellow-400)"></div> Yellow',
                            'lime' => '<div class="theme-swatch" style="--swatch: var(--theme-lime-400)"></div> Lime',
                            'green' => '<div class="theme-swatch" style="--swatch: var(--theme-green-400)"></div> Green',
                            'emerald' => '<div class="theme-swatch" style="--swatch: var(--theme-emerald-400)"></div> Emerald',
                            'teal' => '<div class="theme-swatch" style="--swatch: var(--theme-teal-400)"></div> Teal',
                            'cyan' => '<div class="theme-swatch" style="--swatch: var(--theme-cyan-400)"></div> Cyan',
                            'sky' => '<div class="theme-swatch" style="--swatch: var(--theme-sky-400)"></div> Sky',
                            'blue' => '<div class="theme-swatch" style="--swatch: var(--theme-blue-400)"></div> Blue',
                            'indigo' => '<div class="theme-swatch" style="--swatch: var(--theme-indigo-400)"></div> Indigo',
                            'violet' => '<div class="theme-swatch" style="--swatch: var(--theme-violet-400)"></div> Violet',
                            'purple' => '<div class="theme-swatch" style="--swatch: var(--theme-purple-400)"></div> Purple',
                            'fuchsia' => '<div class="theme-swatch" style="--swatch: var(--theme-fuchsia-400)"></div> Fuchsia',
                            'pink' => '<div class="theme-swatch" style="--swatch: var(--theme-pink-400)"></div> Pink',
                            'rose' => '<div class="theme-swatch" style="--swatch: var(--theme-rose-400)"></div> Rose',
                        ])
                        ->native(false)
                        ->allowHtml()
                        ->reactive()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $old, ?string $state) {
                            $accentPairing = $get('accent_pairing');

                            if ($accentPairing) {
                                $set('accent_content', ThemeData::matchPairing($state));
                            }
                        }),

                    Forms\Components\Select::make('accent_content')
                        ->label(__('Base Color'))
                        ->options([
                            'slate' => '<div class="theme-swatch" style="--swatch: var(--theme-slate-400)"></div> Slate',
                            'gray' => '<div class="theme-swatch" style="--swatch: var(--theme-gray-400)"></div> Gray',
                            'zinc' => '<div class="theme-swatch" style="--swatch: var(--theme-zinc-400)"></div> Zinc',
                            'neutral' => '<div class="theme-swatch" style="--swatch: var(--theme-neutral-400)"></div> Neutral',
                            'stone' => '<div class="theme-swatch" style="--swatch: var(--theme-stone-400)"></div> Stone',
                        ])
                        ->native(false)
                        ->disabled(fn (Forms\Get $get) => $get('accent_pairing') === true)
                        ->allowHtml(),

                    Forms\Components\Toggle::make('accent_pairing')
                        ->label(__('Accent Pairing'))
                        ->reactive(),
                ]),
            ]);
    }
}
