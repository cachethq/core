<?php

namespace Cachet\Data\Cachet;

use Cachet\Data\BaseData;
use Cachet\Settings\ThemeSettings;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\Validation\Required;

final class ThemeData extends BaseData
{
    public const GRAYS = ['slate', 'gray', 'zinc', 'neutral', 'stone'];

    private const THEME_PAIRINGS = [
        'cachet' => 'zinc',
        'red' => 'zinc',
        'orange' => 'neutral',
        'amber' => 'neutral',
        'yellow' => 'stone',
        'lime' => 'zinc',
        'green' => 'zinc',
        'emerald' => 'zinc',
        'teal' => 'gray',
        'cyan' => 'gray',
        'sky' => 'gray',
        'blue' => 'slate',
        'indigo' => 'slate',
        'violet' => 'gray',
        'purple' => 'gray',
        'fuchsia' => 'zinc',
        'pink' => 'zinc',
        'rose' => 'zinc',
    ];

    private const THEMES = [
        'gray' => [
            'light' => [
                'accent' => Color::Gray[800],
                'accent-content' => Color::Gray[800],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => 'oklch(1 0 0)',
                'accent-content' => 'oklch(1 0 0)',
                'accent-foreground' => Color::Gray[800],
            ],
        ],
        'zinc' => [
            'light' => [
                'accent' => Color::Zinc[800],
                'accent-content' => Color::Zinc[800],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => 'oklch(1 0 0)',
                'accent-content' => 'oklch(1 0 0)',
                'accent-foreground' => Color::Zinc[800],
            ],
        ],
        'neutral' => [
            'light' => [
                'accent' => Color::Neutral[800],
                'accent-content' => Color::Neutral[800],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => 'oklch(1 0 0)',
                'accent-content' => 'oklch(1 0 0)',
                'accent-foreground' => Color::Neutral[800],
            ],
        ],
        'stone' => [
            'light' => [
                'accent' => Color::Stone[800],
                'accent-content' => Color::Stone[800],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => 'oklch(1 0 0)',
                'accent-content' => 'oklch(1 0 0)',
                'accent-foreground' => Color::Stone[800],
            ],
        ],
        'red' => [
            'light' => [
                'accent' => Color::Red[500],
                'accent-content' => Color::Red[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Red[500],
                'accent-content' => Color::Red[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'orange' => [
            'light' => [
                'accent' => Color::Orange[500],
                'accent-content' => Color::Orange[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Orange[400],
                'accent-content' => Color::Orange[400],
                'accent-foreground' => Color::Orange[950],
            ],
        ],
        'amber' => [
            'light' => [
                'accent' => Color::Amber[400],
                'accent-content' => Color::Amber[600],
                'accent-foreground' => Color::Amber[950],
            ],
            'dark' => [
                'accent' => Color::Amber[400],
                'accent-content' => Color::Amber[400],
                'accent-foreground' => Color::Amber[950],
            ],
        ],
        'yellow' => [
            'light' => [
                'accent' => Color::Yellow[400],
                'accent-content' => Color::Yellow[600],
                'accent-foreground' => Color::Yellow[950],
            ],
            'dark' => [
                'accent' => Color::Yellow[400],
                'accent-content' => Color::Yellow[400],
                'accent-foreground' => Color::Yellow[950],
            ],
        ],
        'lime' => [
            'light' => [
                'accent' => Color::Lime[400],
                'accent-content' => Color::Lime[600],
                'accent-foreground' => Color::Lime[900],
            ],
            'dark' => [
                'accent' => Color::Lime[400],
                'accent-content' => Color::Lime[400],
                'accent-foreground' => Color::Lime[950],
            ],
        ],
        'green' => [
            'light' => [
                'accent' => Color::Green[600],
                'accent-content' => Color::Green[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Green[600],
                'accent-content' => Color::Green[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'emerald' => [
            'light' => [
                'accent' => Color::Emerald[600],
                'accent-content' => Color::Emerald[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Emerald[600],
                'accent-content' => Color::Emerald[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'teal' => [
            'light' => [
                'accent' => Color::Teal[600],
                'accent-content' => Color::Teal[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Teal[600],
                'accent-content' => Color::Teal[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'cyan' => [
            'light' => [
                'accent' => Color::Cyan[600],
                'accent-content' => Color::Cyan[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Cyan[600],
                'accent-content' => Color::Cyan[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'sky' => [
            'light' => [
                'accent' => Color::Sky[600],
                'accent-content' => Color::Sky[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Sky[600],
                'accent-content' => Color::Sky[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'blue' => [
            'light' => [
                'accent' => Color::Blue[500],
                'accent-content' => Color::Blue[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Blue[500],
                'accent-content' => Color::Blue[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'indigo' => [
            'light' => [
                'accent' => Color::Indigo[500],
                'accent-content' => Color::Indigo[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Indigo[500],
                'accent-content' => Color::Indigo[300],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'violet' => [
            'light' => [
                'accent' => Color::Violet[500],
                'accent-content' => Color::Violet[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Violet[500],
                'accent-content' => Color::Violet[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'purple' => [
            'light' => [
                'accent' => Color::Purple[500],
                'accent-content' => Color::Purple[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Purple[500],
                'accent-content' => Color::Purple[300],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'fuchsia' => [
            'light' => [
                'accent' => Color::Fuchsia[600],
                'accent-content' => Color::Fuchsia[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Fuchsia[600],
                'accent-content' => Color::Fuchsia[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'pink' => [
            'light' => [
                'accent' => Color::Pink[600],
                'accent-content' => Color::Pink[600],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Pink[600],
                'accent-content' => Color::Pink[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
        'rose' => [
            'light' => [
                'accent' => Color::Rose[500],
                'accent-content' => Color::Rose[500],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
            'dark' => [
                'accent' => Color::Rose[500],
                'accent-content' => Color::Rose[400],
                'accent-foreground' => 'oklch(1 0 0)',
            ],
        ],
    ];

    #[Computed]
    public string $styles;

    public function __construct(
        #[Required]
        public readonly ThemeSettings $themeSettings,
    ) {
        $this->generate();
    }

    /**
     * Generate the theme from the given settings.
     */
    protected function generate(): void
    {
        $accent = $this->themeSettings->accent;
        if ($accent === 'cachet') {
            $primaryColor = FilamentColor::getColors()['cachet'];

            $theme = [
                'light' => [
                    'accent' => $primaryColor[500],
                    'accent-content' => $primaryColor[500],
                    'accent-foreground' => 'oklch(1 0 0)',
                ],
                'dark' => [
                    'accent' => $primaryColor[500],
                    'accent-content' => $primaryColor[400],
                    'accent-foreground' => $primaryColor[950],
                ],
            ];
        } else {
            $theme = self::THEMES[$accent];
        }

        if ($this->themeSettings->accent_pairing) {
            $pairing = self::THEME_PAIRINGS[$accent];
        } else {
            $pairing = $this->themeSettings->accent_content;
        }

        $this->styles = $this->compileCss($theme, $pairing);
    }

    /**
     * Compile the CSS from the theme and background pairing.
     */
    protected function compileCss(array $theme, string $pairing): string
    {
        $pairingKey = ucwords($pairing);
        $pairingColor = constant("Filament\Support\Colors\Color::{$pairingKey}");

        return <<<CSS
            :root {
                --accent: {$theme['light']['accent']};
                --accent-content: {$theme['light']['accent-content']};
                --accent-foreground: {$theme['light']['accent-foreground']};
                --accent-background: {$pairingColor[50]};
            }

            @media(prefers-color-scheme: dark) {
                :root {
                    --accent: {$theme['dark']['accent']};
                    --accent-content: {$theme['dark']['accent-content']};
                    --accent-foreground: {$theme['dark']['accent-foreground']};
                    --accent-background: {$pairingColor[900]};
                }
            }
        CSS;
    }

    /**
     * Match the pairing for the given accent.
     */
    public static function matchPairing(string $accent): string
    {
        return self::THEME_PAIRINGS[$accent];
    }
}
