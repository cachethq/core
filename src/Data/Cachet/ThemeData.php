<?php

namespace Cachet\Data\Cachet;

use Cachet\Data\BaseData;
use Cachet\Settings\ThemeSettings;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\Validation\Required;

class ThemeData extends BaseData
{
    private const THEME_PAIRINGS = [
        "cachet" => "zinc",
        "red" => "zinc",
        "orange" => "neutral",
        "amber" => "neutral",
        "yellow" => "stone",
        "lime" => "zinc",
        "green" => "zinc",
        "emerald" => "zinc",
        "teal" => "gray",
        "cyan" => "gray",
        "sky" => "gray",
        "blue" => "slate",
        "indigo" => "slate",
        "violet" => "gray",
        "purple" => "gray",
        "fuchsia" => "zinc",
        "pink" => "zinc",
        "rose" => "zinc",
    ];

    private const THEMES = [
        "gray" => [
            "light" => [
                "accent" => "var(--theme-gray-800)",
                "accent-content" => "var(--theme-gray-800)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-white)",
                "accent-content" => "var(--theme-white)",
                "accent-foreground" => "var(--theme-gray-800)",
            ],
        ],
        "zinc" => [
            "light" => [
                "accent" => "var(--theme-zinc-800)",
                "accent-content" => "var(--theme-zinc-800)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-white)",
                "accent-content" => "var(--theme-white)",
                "accent-foreground" => "var(--theme-zinc-800)",
            ],
        ],
        "neutral" => [
            "light" => [
                "accent" => "var(--theme-neutral-800)",
                "accent-content" => "var(--theme-neutral-800)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-white)",
                "accent-content" => "var(--theme-white)",
                "accent-foreground" => "var(--theme-neutral-800)",
            ],
        ],
        "stone" => [
            "light" => [
                "accent" => "var(--theme-stone-800)",
                "accent-content" => "var(--theme-stone-800)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-white)",
                "accent-content" => "var(--theme-white)",
                "accent-foreground" => "var(--theme-stone-800)",
            ],
        ],
        "red" => [
            "light" => [
                "accent" => "var(--theme-red-500)",
                "accent-content" => "var(--theme-red-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-red-500)",
                "accent-content" => "var(--theme-red-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "orange" => [
            "light" => [
                "accent" => "var(--theme-orange-500)",
                "accent-content" => "var(--theme-orange-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-orange-400)",
                "accent-content" => "var(--theme-orange-400)",
                "accent-foreground" => "var(--theme-orange-950)",
            ],
        ],
        "amber" => [
            "light" => [
                "accent" => "var(--theme-amber-400)",
                "accent-content" => "var(--theme-amber-600)",
                "accent-foreground" => "var(--theme-amber-950)",
            ],
            "dark" => [
                "accent" => "var(--theme-amber-400)",
                "accent-content" => "var(--theme-amber-400)",
                "accent-foreground" => "var(--theme-amber-950)",
            ],
        ],
        "yellow" => [
            "light" => [
                "accent" => "var(--theme-yellow-400)",
                "accent-content" => "var(--theme-yellow-600)",
                "accent-foreground" => "var(--theme-yellow-950)",
            ],
            "dark" => [
                "accent" => "var(--theme-yellow-400)",
                "accent-content" => "var(--theme-yellow-400)",
                "accent-foreground" => "var(--theme-yellow-950)",
            ],
        ],
        "lime" => [
            "light" => [
                "accent" => "var(--theme-lime-400)",
                "accent-content" => "var(--theme-lime-600)",
                "accent-foreground" => "var(--theme-lime-900)",
            ],
            "dark" => [
                "accent" => "var(--theme-lime-400)",
                "accent-content" => "var(--theme-lime-400)",
                "accent-foreground" => "var(--theme-lime-950)",
            ],
        ],
        "green" => [
            "light" => [
                "accent" => "var(--theme-green-600)",
                "accent-content" => "var(--theme-green-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-green-600)",
                "accent-content" => "var(--theme-green-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "emerald" => [
            "light" => [
                "accent" => "var(--theme-emerald-600)",
                "accent-content" => "var(--theme-emerald-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-emerald-600)",
                "accent-content" => "var(--theme-emerald-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "teal" => [
            "light" => [
                "accent" => "var(--theme-teal-600)",
                "accent-content" => "var(--theme-teal-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-teal-600)",
                "accent-content" => "var(--theme-teal-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "cyan" => [
            "light" => [
                "accent" => "var(--theme-cyan-600)",
                "accent-content" => "var(--theme-cyan-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-cyan-600)",
                "accent-content" => "var(--theme-cyan-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "sky" => [
            "light" => [
                "accent" => "var(--theme-sky-600)",
                "accent-content" => "var(--theme-sky-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-sky-600)",
                "accent-content" => "var(--theme-sky-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "blue" => [
            "light" => [
                "accent" => "var(--theme-blue-500)",
                "accent-content" => "var(--theme-blue-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-blue-500)",
                "accent-content" => "var(--theme-blue-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "indigo" => [
            "light" => [
                "accent" => "var(--theme-indigo-500)",
                "accent-content" => "var(--theme-indigo-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-indigo-500)",
                "accent-content" => "var(--theme-indigo-300)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "violet" => [
            "light" => [
                "accent" => "var(--theme-violet-500)",
                "accent-content" => "var(--theme-violet-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-violet-500)",
                "accent-content" => "var(--theme-violet-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "purple" => [
            "light" => [
                "accent" => "var(--theme-purple-500)",
                "accent-content" => "var(--theme-purple-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-purple-500)",
                "accent-content" => "var(--theme-purple-300)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "fuchsia" => [
            "light" => [
                "accent" => "var(--theme-fuchsia-600)",
                "accent-content" => "var(--theme-fuchsia-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-fuchsia-600)",
                "accent-content" => "var(--theme-fuchsia-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "pink" => [
            "light" => [
                "accent" => "var(--theme-pink-600)",
                "accent-content" => "var(--theme-pink-600)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-pink-600)",
                "accent-content" => "var(--theme-pink-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "rose" => [
            "light" => [
                "accent" => "var(--theme-rose-500)",
                "accent-content" => "var(--theme-rose-500)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-rose-500)",
                "accent-content" => "var(--theme-rose-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
        "cachet" => [
            "light" => [
                "accent" => "var(--theme-primary-500)",
                "accent-content" => "var(--theme-primary-500)",
                "accent-foreground" => "var(--theme-white)",
            ],
            "dark" => [
                "accent" => "var(--theme-primary-500)",
                "accent-content" => "var(--theme-primary-400)",
                "accent-foreground" => "var(--theme-white)",
            ],
        ],
    ];

    #[Computed]
    public string $styles;

    public function __construct(
        #[Required]
        public readonly ThemeSettings $themeSettings,
    )
    {
        $this->generate();
    }

    /**
     * Generate the theme from the given settings.
     */
    protected function generate(): void
    {
        $theme = self::THEMES[$this->themeSettings->accent];
        $pairing = self::THEME_PAIRINGS[$this->themeSettings->accent];

        $this->styles = $this->compileCss($theme, $pairing);
    }

    /**
     * Compile the CSS from the theme and background pairing.
     */
    protected function compileCss(array $theme, string $pairing): string
    {
        return <<<CSS
            :root {
                --accent: {$theme['light']['accent']};
                --accent-content: {$theme['light']['accent-content']};
                --accent-foreground: {$theme['light']['accent-foreground']};
                --background: var(--theme-{$pairing}-100);
            }

            @media(prefers-color-scheme: dark) {
                :root {
                    --accent: {$theme['dark']['accent']};
                    --accent-content: {$theme['dark']['accent-content']};
                    --accent-foreground: {$theme['dark']['accent-foreground']};
                    --background: var(--theme-{$pairing}-900);
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
