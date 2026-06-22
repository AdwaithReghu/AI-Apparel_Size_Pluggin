<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Garment;
use App\Models\Scan;
use App\Models\Brand;

class PlatformAnalytics extends Page
{
    protected static ?string $navigationLabel = 'Platform Analytics';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.admin.pages.platform-analytics';

    public int $totalMerchants = 0;
    public int $totalScans = 0;
    public int $totalGarments = 0;
    public int $totalBrands = 0;
    public int $scansToday = 0;
    public int $scansThisWeek = 0;
    public int $scansThisMonth = 0;
    public array $topMerchants = [];

    public function mount(): void
    {
        $this->totalMerchants  = User::count();
        $this->totalScans      = Scan::count();
        $this->totalGarments   = Garment::count();
        $this->totalBrands     = Brand::count();
        $this->scansToday      = Scan::whereDate('created_at', today())->count();
        $this->scansThisWeek   = Scan::where('created_at', '>=', now()->startOfWeek())->count();
        $this->scansThisMonth  = Scan::where('created_at', '>=', now()->startOfMonth())->count();

        // Top merchants by scan count
        $this->topMerchants = User::withCount('scans')
            ->orderBy('scans_count', 'desc')
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'name'       => $u->name,
                'email'      => $u->email,
                'scans'      => $u->scans_count,
                'garments'   => $u->garments()->count(),
                'api_calls'  => $u->api_calls_month ?? 0,
            ])
            ->toArray();
    }
}