<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Garment;
use App\Models\Scan;

class SupportTools extends Page
{
    protected static ?string $navigationLabel = 'Support Tools';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.admin.pages.support-tools';

    public string $searchEmail = '';
    public ?array $merchantData = null;
    public string $searchError = '';

    public function searchMerchant(): void
    {
        $this->searchError  = '';
        $this->merchantData = null;

        if (empty($this->searchEmail)) {
            $this->searchError = 'Please enter an email address';
            return;
        }

        $merchant = User::where('email', $this->searchEmail)->first();

        if (!$merchant) {
            $this->searchError = 'No merchant found with this email';
            return;
        }

        $this->merchantData = [
            'id'              => $merchant->id,
            'name'            => $merchant->name,
            'email'           => $merchant->email,
            'company'         => $merchant->company ?? '—',
            'phone'           => $merchant->phone ?? '—',
            'country'         => $merchant->country ?? '—',
            'joined'          => $merchant->created_at->format('M d, Y'),
            'total_garments'  => Garment::where('user_id', $merchant->id)->count(),
            'total_scans'     => Scan::where('user_id', $merchant->id)->count(),
            'api_calls_today' => $merchant->api_calls_today ?? 0,
            'api_calls_month' => $merchant->api_calls_month ?? 0,
            'api_key'         => $merchant->api_key ?? 'Not generated',
            'recent_garments' => Garment::where('user_id', $merchant->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($g) => [
                    'name'     => $g->name,
                    'brand'    => $g->brand ?? '—',
                    'category' => $g->category ?? '—',
                    'status'   => $g->status,
                    'created'  => $g->created_at->format('M d, Y'),
                ])
                ->toArray(),
        ];
    }

    public function resetMerchantApiCalls(): void
    {
        if (!$this->merchantData) return;

        User::where('email', $this->searchEmail)->update([
            'api_calls_today' => 0,
            'api_calls_month' => 0,
        ]);

        Notification::make()
            ->title('API calls reset successfully!')
            ->success()
            ->send();

        $this->searchMerchant();
    }
}