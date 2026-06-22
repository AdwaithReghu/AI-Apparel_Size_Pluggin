<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;

class IntegrationSettings extends Page
{
    protected static ?string $navigationLabel = 'Integration Settings';
    protected static ?int    $navigationSort  = 10;
    protected  string  $view            = 'filament.pages.integration-settings';

    public ?string $apiKey = null;
    public int $apiCallsToday = 0;
    public int $apiCallsMonth = 0;

    public function mount(): void
    {
        $user = auth()->user();
        $this->apiKey          = $user->api_key;
        $this->apiCallsToday   = $user->api_calls_today ?? 0;
        $this->apiCallsMonth   = $user->api_calls_month ?? 0;

        // Auto generate key if none exists
        if (!$this->apiKey) {
            $this->apiKey = $user->generateApiKey();
        }
    }

    public function regenerateKey(): void
    {
        $user = auth()->user();
        $this->apiKey = $user->generateApiKey();

        \Filament\Notifications\Notification::make()
            ->title('API key regenerated!')
            ->success()
            ->send();
    }

    public function getEmbedCode(): string
    {
        return '<script src="https://nytt.com/widget.js" data-key="' . $this->apiKey . '"></script>';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerate')
                ->label('Regenerate Key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate API Key?')
                ->modalDescription('This will invalidate your current key. Any websites using the old key will stop working.')
                ->action('regenerateKey'),
        ];
    }
}