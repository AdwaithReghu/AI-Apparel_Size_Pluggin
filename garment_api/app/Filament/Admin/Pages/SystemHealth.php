<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SystemHealth extends Page
{
    protected static ?string $navigationLabel = 'System Health';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.admin.pages.system-health';

    public bool $databaseHealthy = false;
    public bool $pythonHealthy = false;
    public bool $laravelHealthy = true;
    public string $databaseSize = '0 KB';
    public int $totalJobs = 0;
    public int $failedJobs = 0;
    public string $phpVersion = '';
    public string $laravelVersion = '';

    public function mount(): void
    {
        // Check database
        try {
            DB::connection()->getPdo();
            $this->databaseHealthy = true;
        } catch (\Exception $e) {
            $this->databaseHealthy = false;
        }

        // Check Python service
        try {
            $response = Http::timeout(3)->get('http://127.0.0.1:8001');
            $this->pythonHealthy = $response->successful();
        } catch (\Exception $e) {
            $this->pythonHealthy = false;
        }

        // PHP and Laravel versions
        $this->phpVersion     = PHP_VERSION;
        $this->laravelVersion = app()->version();

        // Failed jobs
        try {
            $this->failedJobs = DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            $this->failedJobs = 0;
        }

        // Database size
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $bytes = filesize($dbPath);
            $this->databaseSize = round($bytes / 1024, 2) . ' KB';
        }
    }

    public function refresh(): void
    {
        $this->mount();
    }
}