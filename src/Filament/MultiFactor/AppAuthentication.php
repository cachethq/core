<?php

namespace Cachet\Filament\MultiFactor;

use Cachet\Settings\AppSettings;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication as BaseAppAuthentication;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\FontFamily;

class AppAuthentication extends BaseAppAuthentication
{
    /**
     * The status page name identifies the account in authenticator apps, rather
     * than the host application's name. Resolved lazily to avoid querying the
     * settings table while the panel is being configured.
     */
    public function getBrandName(): string
    {
        return $this->brandName ?? app(AppSettings::class)->name ?? parent::getBrandName();
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return array_map(
            fn (Action $action): Action => $action->getName() === 'setUpAppAuthentication'
                ? $this->configureSetUpAction($action)
                : $action,
            parent::getActions(),
        );
    }

    /**
     * Restyles Filament's set-up wizard while reusing its verification and persistence logic.
     * Only the visual QR code group is rebuilt; the one-time code input, with its validation
     * and rate limiting, is kept as-is. If Filament's internal structure changes, the stock
     * wizard is rendered unchanged.
     */
    protected function configureSetUpAction(Action $action): Action
    {
        return $action
            ->button()
            ->modifyWizardUsing(function (Wizard $wizard) use ($action): Wizard {
                $wizard->hiddenHeader();

                $steps = $wizard->getDefaultChildComponents();
                $appStep = is_array($steps) ? ($steps[0] ?? null) : null;

                if (! $appStep instanceof Step) {
                    return $wizard;
                }

                $stepComponents = $appStep->getDefaultChildComponents();

                $codeInput = is_array($stepComponents)
                    ? collect($stepComponents)->first(fn (mixed $component): bool => $component instanceof OneTimeCodeInput)
                    : null;

                if (! $codeInput instanceof OneTimeCodeInput) {
                    return $wizard;
                }

                $secret = fn (): string => decrypt($action->getArguments()['encrypted'])['secret'];

                $appStep->schema([
                    Group::make([
                        Text::make(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.qr_code.instruction'))
                            ->color('neutral'),
                        Image::make(
                            url: fn (): string => $this->generateQrCodeDataUri($secret()),
                            alt: __('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.qr_code.alt'),
                        )
                            ->imageHeight('10rem')
                            ->alignCenter(),
                        Flex::make([
                            Text::make(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.text_code.instruction'))
                                ->color('neutral')
                                ->grow(false),
                            Text::make($secret)
                                ->fontFamily(FontFamily::Mono)
                                ->badge()
                                ->copyable()
                                ->copyMessage(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.text_code.messages.copied'))
                                ->grow(false),
                        ])->from('sm'),
                    ])
                        ->dense(),
                    $codeInput,
                ]);

                return $wizard;
            });
    }
}
