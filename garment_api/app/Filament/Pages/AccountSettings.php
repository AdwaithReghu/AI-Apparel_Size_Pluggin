<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class AccountSettings extends Page
{
    protected static ?string $navigationLabel = 'Account Settings';
    protected static ?int $navigationSort = 11;
    protected string $view = 'filament.pages.account-settings';

    // Profile fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $country = '';

    // Password fields
    public string $current_password = '';
    public string $new_password = '';
    public string $confirm_password = '';

    // Notification preferences
    public bool $notify_scan_complete = true;
    public bool $notify_weekly_report = true;
    public bool $notify_new_features = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name    = $user->name;
        $this->email   = $user->email;
        $this->phone   = $user->phone ?? '';
        $this->company = $user->company ?? '';
        $this->country = $user->country ?? '';
        $this->notify_scan_complete = $user->notify_scan_complete ?? true;
        $this->notify_weekly_report = $user->notify_weekly_report ?? true;
        $this->notify_new_features  = $user->notify_new_features ?? false;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();
        $user->update([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'company' => $this->company,
            'country' => $this->country,
        ]);

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->danger()
                ->send();
            return;
        }

        if ($this->new_password !== $this->confirm_password) {
            Notification::make()
                ->title('New passwords do not match')
                ->danger()
                ->send();
            return;
        }

        if (strlen($this->new_password) < 8) {
            Notification::make()
                ->title('Password must be at least 8 characters')
                ->warning()
                ->send();
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password     = '';
        $this->confirm_password = '';

        Notification::make()
            ->title('Password updated successfully!')
            ->success()
            ->send();
    }

    public function updateNotifications(): void
    {
        $user = auth()->user();
        $user->update([
            'notify_scan_complete' => $this->notify_scan_complete,
            'notify_weekly_report' => $this->notify_weekly_report,
            'notify_new_features'  => $this->notify_new_features,
        ]);

        Notification::make()
            ->title('Notification preferences saved!')
            ->success()
            ->send();
    }
}