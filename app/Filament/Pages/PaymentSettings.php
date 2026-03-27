<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaymentSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payment settings';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.payment-settings';

    public bool $stripe_enabled = false;

    public bool $stripeConfigured = false;

    public function mount(): void
    {
        $this->stripe_enabled = Setting::current()->stripe_enabled;
        $this->stripeConfigured = filled(config('services.stripe.secret_key'))
            && filled(config('services.stripe.webhook_secret'));
    }

    public function save(): void
    {
        Setting::current()->update([
            'stripe_enabled' => $this->stripe_enabled,
        ]);

        Notification::make()
            ->title('Payment settings saved')
            ->success()
            ->send();
    }
}
