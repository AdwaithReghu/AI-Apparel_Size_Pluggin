<x-filament-panels::page>
<style>
    .sh-wrap { padding: 0; }

    .sh-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .sh-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(46,213,115,0.1);
    }
    .sh-hero-title {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 4px 0;
    }
    .sh-hero-sub {
        font-size: 13px;
        opacity: 0.7;
        margin: 0 0 16px 0;
    }
    .sh-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(46,213,115,0.2);
        border: 1px solid rgba(46,213,115,0.4);
        color: #2ed573;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .sh-hero-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #2ed573;
        animation: blink 2s infinite;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .sh-hero-icon {
        font-size: 56px;
        position: relative;
        z-index: 1;
    }

    .sh-status-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .sh-status-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s;
    }
    .sh-status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .sh-status-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .sh-status-icon.green  { background: #f0fdf4; }
    .sh-status-icon.red    { background: #fef2f2; }
    .sh-status-icon.blue   { background: #eff6ff; }
    .sh-status-name {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 2px;
    }
    .sh-status-detail {
        font-size: 12px;
        color: #888;
    }
    .sh-status-badge {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }
    .sh-status-badge.online {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .sh-status-badge.offline {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .sh-status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
    }
    .sh-status-badge.online .sh-status-dot  { background: #16a34a; }
    .sh-status-badge.offline .sh-status-dot { background: #dc2626; }

    .sh-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
    }
    .sh-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .sh-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .sh-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .sh-info-item {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #f0f0f0;
    }
    .sh-info-label {
        font-size: 11px;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .sh-info-value {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
    }
    .sh-info-sub {
        font-size: 11px;
        color: #aaa;
        margin-top: 2px;
    }

    .sh-refresh-btn {
        background: white;
        border: 1.5px solid #e5e7eb;
        color: #555;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .sh-refresh-btn:hover {
        border-color: #6C63FF;
        color: #6C63FF;
        background: #f5f3ff;
    }

    .sh-check-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 10px;
        border: 1px solid #f0f0f0;
        background: #fafafa;
    }
    .sh-check-item:last-child { margin-bottom: 0; }
    .sh-check-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sh-check-emoji { font-size: 20px; }
    .sh-check-name {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
    }
    .sh-check-detail {
        font-size: 12px;
        color: #888;
    }
    .sh-check-pass {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .sh-check-pass.pass {
        background: #f0fdf4;
        color: #16a34a;
    }
    .sh-check-pass.fail {
        background: #fef2f2;
        color: #dc2626;
    }
    .sh-check-pass.warn {
        background: #fffbeb;
        color: #d97706;
    }

    .dark .sh-status-card,
    .dark .sh-card,
    .dark .sh-info-item,
    .dark .sh-check-item {
        background: #1f2937;
        border-color: #374151;
    }
    .dark .sh-status-name,
    .dark .sh-card-title,
    .dark .sh-info-value,
    .dark .sh-check-name { color: #f9fafb; }
    .dark .sh-info-item { background: #111827; }
    .dark .sh-check-item { background: #111827; }
</style>

<div class="sh-wrap">

    {{-- Hero --}}
    <div class="sh-hero">
        <div>
            <p class="sh-hero-title">System Health</p>
            <p class="sh-hero-sub">
                Monitor all services and infrastructure in real time
            </p>
            <div class="sh-hero-badge">
                <span class="sh-hero-dot"></span>
                All Systems Operational
            </div>
        </div>
        <div class="sh-hero-icon">❤️</div>
    </div>

    {{-- Service Status Cards --}}
    <div class="sh-status-grid">

        {{-- Laravel --}}
        <div class="sh-status-card">
            <div class="sh-status-icon green">⚡</div>
            <div>
                <div class="sh-status-name">Laravel API</div>
                <div class="sh-status-detail">v{{ $laravelVersion }}</div>
            </div>
            <div class="sh-status-badge online">
                <span class="sh-status-dot"></span>
                Running
            </div>
        </div>

        {{-- Database --}}
        <div class="sh-status-card">
            <div class="sh-status-icon {{ $databaseHealthy ? 'green' : 'red' }}">
                🗄️
            </div>
            <div>
                <div class="sh-status-name">Database</div>
                <div class="sh-status-detail">
                    SQLite · {{ $databaseSize }}
                </div>
            </div>
            <div class="sh-status-badge {{ $databaseHealthy ? 'online' : 'offline' }}">
                <span class="sh-status-dot"></span>
                {{ $databaseHealthy ? 'Connected' : 'Error' }}
            </div>
        </div>

        {{-- Python --}}
        <div class="sh-status-card">
            <div class="sh-status-icon {{ $pythonHealthy ? 'green' : 'red' }}">
                🐍
            </div>
            <div>
                <div class="sh-status-name">Python AI Service</div>
                <div class="sh-status-detail">port 8001</div>
            </div>
            <div class="sh-status-badge {{ $pythonHealthy ? 'online' : 'offline' }}">
                <span class="sh-status-dot"></span>
                {{ $pythonHealthy ? 'Running' : 'Offline' }}
            </div>
        </div>

    </div>

    {{-- System Checks --}}
    <div class="sh-card">
        <div class="sh-card-header">
            <p class="sh-card-title">🔍 System Checks</p>
            <button wire:click="refresh" class="sh-refresh-btn">
                🔄 Refresh
            </button>
        </div>

        <div class="sh-check-item">
            <div class="sh-check-left">
                <span class="sh-check-emoji">⚡</span>
                <div>
                    <div class="sh-check-name">Laravel API Server</div>
                    <div class="sh-check-detail">
                        Application is running and responding
                    </div>
                </div>
            </div>
            <span class="sh-check-pass pass">✓ Passing</span>
        </div>

        <div class="sh-check-item">
            <div class="sh-check-left">
                <span class="sh-check-emoji">🗄️</span>
                <div>
                    <div class="sh-check-name">Database Connection</div>
                    <div class="sh-check-detail">
                        SQLite database is accessible
                    </div>
                </div>
            </div>
            <span class="sh-check-pass {{ $databaseHealthy ? 'pass' : 'fail' }}">
                {{ $databaseHealthy ? '✓ Passing' : '✕ Failed' }}
            </span>
        </div>

        <div class="sh-check-item">
            <div class="sh-check-left">
                <span class="sh-check-emoji">🐍</span>
                <div>
                    <div class="sh-check-name">Python AI Service</div>
                    <div class="sh-check-detail">
                        Measurement microservice on port 8001
                    </div>
                </div>
            </div>
            <span class="sh-check-pass {{ $pythonHealthy ? 'pass' : 'fail' }}">
                {{ $pythonHealthy ? '✓ Passing' : '✕ Offline' }}
            </span>
        </div>

        <div class="sh-check-item">
            <div class="sh-check-left">
                <span class="sh-check-emoji">💼</span>
                <div>
                    <div class="sh-check-name">Failed Jobs</div>
                    <div class="sh-check-detail">
                        Queue jobs that failed to process
                    </div>
                </div>
            </div>
            <span class="sh-check-pass {{ $failedJobs === 0 ? 'pass' : 'warn' }}">
                {{ $failedJobs === 0 ? '✓ None' : '⚠ ' . $failedJobs . ' failed' }}
            </span>
        </div>

        <div class="sh-check-item">
            <div class="sh-check-left">
                <span class="sh-check-emoji">🐛</span>
                <div>
                    <div class="sh-check-name">Debug Mode</div>
                    <div class="sh-check-detail">
                        Should be OFF in production
                    </div>
                </div>
            </div>
            <span class="sh-check-pass {{ config('app.debug') ? 'warn' : 'pass' }}">
                {{ config('app.debug') ? '⚠ ON' : '✓ OFF' }}
            </span>
        </div>

        <div class="sh-check-item">
            <div class="sh-check-left">
                <span class="sh-check-emoji">🌍</span>
                <div>
                    <div class="sh-check-name">Environment</div>
                    <div class="sh-check-detail">
                        Current application environment
                    </div>
                </div>
            </div>
            <span class="sh-check-pass {{ app()->environment('production') ? 'pass' : 'warn' }}">
                {{ app()->environment() }}
            </span>
        </div>

    </div>

    {{-- System Information --}}
    <div class="sh-card">
        <div class="sh-card-header">
            <p class="sh-card-title">💻 System Information</p>
        </div>

        <div class="sh-info-grid">
            <div class="sh-info-item">
                <div class="sh-info-label">🐘 PHP Version</div>
                <div class="sh-info-value">{{ $phpVersion }}</div>
                <div class="sh-info-sub">Server-side language</div>
            </div>
            <div class="sh-info-item">
                <div class="sh-info-label">⚡ Laravel Version</div>
                <div class="sh-info-value">{{ $laravelVersion }}</div>
                <div class="sh-info-sub">PHP framework</div>
            </div>
            <div class="sh-info-item">
                <div class="sh-info-label">🗄️ Database Size</div>
                <div class="sh-info-value">{{ $databaseSize }}</div>
                <div class="sh-info-sub">SQLite file size</div>
            </div>
            <div class="sh-info-item">
                <div class="sh-info-label">💼 Failed Jobs</div>
                <div class="sh-info-value"
                    style="color: {{ $failedJobs > 0 ? '#e74c3c' : '#2ecc71' }}">
                    {{ $failedJobs }}
                </div>
                <div class="sh-info-sub">Queue failures</div>
            </div>
            <div class="sh-info-item">
                <div class="sh-info-label">🌍 Environment</div>
                <div class="sh-info-value">{{ app()->environment() }}</div>
                <div class="sh-info-sub">App environment</div>
            </div>
            <div class="sh-info-item">
                <div class="sh-info-label">🐛 Debug Mode</div>
                <div class="sh-info-value"
                    style="color: {{ config('app.debug') ? '#f39c12' : '#2ecc71' }}">
                    {{ config('app.debug') ? 'ON' : 'OFF' }}
                </div>
                <div class="sh-info-sub">
                    {{ config('app.debug') ? 'Disable in production' : 'Safe for production' }}
                </div>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>