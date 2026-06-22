<x-filament-panels::page>
<style>
    .nytt-admin-wrap { padding: 0; }
    .nytt-hero {
        background: linear-gradient(135deg, #c0392b 0%, #922b21 100%);
        border-radius: 16px;
        padding: 32px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .nytt-hero-title {
        font-size: 28px;
        font-weight: 800;
        margin: 0 0 4px 0;
    }
    .nytt-hero-sub {
        font-size: 14px;
        opacity: 0.8;
        margin: 0;
    }
    .nytt-hero-badge {
        background: rgba(255,255,255,0.2);
        border-radius: 50px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .nytt-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .nytt-stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }
    .nytt-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px;
        height: 100%;
        border-radius: 4px 0 0 4px;
    }
    .nytt-stat-card.red::before   { background: #e74c3c; }
    .nytt-stat-card.blue::before  { background: #3498db; }
    .nytt-stat-card.green::before { background: #2ecc71; }
    .nytt-stat-label {
        font-size: 13px;
        color: #888;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .nytt-stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #1a1a2e;
        margin-bottom: 4px;
        line-height: 1;
    }
    .nytt-stat-desc {
        font-size: 12px;
        color: #aaa;
    }
    .nytt-stat-icon {
        position: absolute;
        top: 20px; right: 20px;
        font-size: 32px;
        opacity: 0.15;
    }
    .nytt-section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .nytt-section-title span {
        width: 4px;
        height: 20px;
        background: #e74c3c;
        border-radius: 2px;
        display: inline-block;
    }
    .nytt-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    .nytt-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #f0f0f0;
    }
    .nytt-quick-links {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    .nytt-quick-link {
        background: white;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #f0f0f0;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .nytt-quick-link:hover {
        border-color: #e74c3c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231,76,60,0.1);
    }
    .nytt-quick-link-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .nytt-quick-link-text {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }
    .nytt-quick-link-sub {
        font-size: 11px;
        color: #aaa;
    }
    .nytt-merchant-table {
        width: 100%;
        border-collapse: collapse;
    }
    .nytt-merchant-table th {
        text-align: left;
        font-size: 12px;
        color: #aaa;
        font-weight: 600;
        padding: 8px 12px;
        border-bottom: 1px solid #f5f5f5;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nytt-merchant-table td {
        padding: 12px;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #f9f9f9;
    }
    .nytt-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .nytt-badge.green { background: #e8f8f0; color: #1a7a4a; }
    .nytt-badge.blue  { background: #e8f0ff; color: #1a4a7a; }
    .nytt-badge.red   { background: #fef0ef; color: #c0392b; }
    .nytt-system-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    .nytt-system-item:last-child { border-bottom: none; }
    .nytt-system-name {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    .nytt-system-sub {
        font-size: 12px;
        color: #aaa;
    }
    .nytt-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .nytt-dot.green { background: #2ecc71; }
    .nytt-dot.red   { background: #e74c3c; }
    .dark .nytt-stat-card,
    .dark .nytt-card,
    .dark .nytt-quick-link {
        background: #1f2937;
        border-color: #374151;
    }
    .dark .nytt-stat-value,
    .dark .nytt-section-title,
    .dark .nytt-system-name,
    .dark .nytt-quick-link-text,
    .dark .nytt-merchant-table td {
        color: #f9fafb;
    }
    .dark .nytt-merchant-table th { color: #6b7280; }
    .dark .nytt-merchant-table td { border-color: #374151; }
    .dark .nytt-system-item { border-color: #374151; }
</style>

<div class="nytt-admin-wrap">

    {{-- Hero --}}
    <div class="nytt-hero">
        <div>
            <p class="nytt-hero-title">NYTT Admin Panel</p>
            <p class="nytt-hero-sub">
                Internal dashboard — Manage merchants, monitor platform health
            </p>
        </div>
        <div class="nytt-hero-badge">
            🔴 Super Admin
        </div>
    </div>

    {{-- Stats --}}
    <div class="nytt-stats-grid">
        <div class="nytt-stat-card red">
            <div class="nytt-stat-icon">👥</div>
            <div class="nytt-stat-label">Total Merchants</div>
            <div class="nytt-stat-value">{{ \App\Models\User::count() }}</div>
            <div class="nytt-stat-desc">Registered on platform</div>
        </div>
        <div class="nytt-stat-card blue">
            <div class="nytt-stat-icon">📷</div>
            <div class="nytt-stat-label">Total Scans</div>
            <div class="nytt-stat-value">{{ \App\Models\Scan::count() }}</div>
            <div class="nytt-stat-desc">Across all merchants</div>
        </div>
        <div class="nytt-stat-card green">
            <div class="nytt-stat-icon">👕</div>
            <div class="nytt-stat-label">Total Garments</div>
            <div class="nytt-stat-value">{{ \App\Models\Garment::count() }}</div>
            <div class="nytt-stat-desc">Measured and saved</div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="nytt-section-title">
        <span></span> Quick Actions
    </div>
    <div class="nytt-quick-links">
        <a href="/admin/merchant-management" class="nytt-quick-link">
            <div class="nytt-quick-link-icon" style="background:#fef0ef">👥</div>
            <div>
                <div class="nytt-quick-link-text">Merchants</div>
                <div class="nytt-quick-link-sub">Manage all merchants</div>
            </div>
        </a>
        <a href="/admin/platform-analytics" class="nytt-quick-link">
            <div class="nytt-quick-link-icon" style="background:#e8f0ff">📊</div>
            <div>
                <div class="nytt-quick-link-text">Analytics</div>
                <div class="nytt-quick-link-sub">Platform statistics</div>
            </div>
        </a>
        <a href="/admin/a-i-model-management" class="nytt-quick-link">
            <div class="nytt-quick-link-icon" style="background:#e8f8f0">🤖</div>
            <div>
                <div class="nytt-quick-link-text">AI Model</div>
                <div class="nytt-quick-link-sub">Manage AI engine</div>
            </div>
        </a>
        <a href="/admin/system-health" class="nytt-quick-link">
            <div class="nytt-quick-link-icon" style="background:#fff8e8">❤️</div>
            <div>
                <div class="nytt-quick-link-text">System Health</div>
                <div class="nytt-quick-link-sub">Monitor services</div>
            </div>
        </a>
        <a href="/admin/support-tools" class="nytt-quick-link">
            <div class="nytt-quick-link-icon" style="background:#f0e8ff">🛠️</div>
            <div>
                <div class="nytt-quick-link-text">Support Tools</div>
                <div class="nytt-quick-link-sub">Troubleshoot merchants</div>
            </div>
        </a>
        <a href="/admin/merchant-management/create" class="nytt-quick-link">
            <div class="nytt-quick-link-icon" style="background:#fef0ef">➕</div>
            <div>
                <div class="nytt-quick-link-text">Add Merchant</div>
                <div class="nytt-quick-link-sub">Onboard new merchant</div>
            </div>
        </a>
    </div>

    {{-- Two column layout --}}
    <div class="nytt-grid-2">

        {{-- Recent Merchants --}}
        <div class="nytt-card">
            <div class="nytt-section-title">
                <span></span> Recent Merchants
            </div>
            <table class="nytt-merchant-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Garments</th>
                        <th>Scans</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\User::latest()->take(5)->get() as $merchant)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $merchant->name }}</div>
                            <div style="font-size:11px;color:#aaa">{{ $merchant->email }}</div>
                        </td>
                        <td>
                            <span class="nytt-badge green">
                                {{ $merchant->garments()->count() }}
                            </span>
                        </td>
                        <td>
                            <span class="nytt-badge blue">
                                {{ $merchant->scans()->count() }}
                            </span>
                        </td>
                        <td style="color:#aaa;font-size:12px">
                            {{ $merchant->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- System Status --}}
        <div class="nytt-card">
            <div class="nytt-section-title">
                <span></span> System Status
            </div>

            <div class="nytt-system-item">
                <div>
                    <div class="nytt-system-name">
                        <span class="nytt-dot green"></span>
                        Laravel API
                    </div>
                    <div class="nytt-system-sub">v{{ app()->version() }}</div>
                </div>
                <span class="nytt-badge green">Running</span>
            </div>

            <div class="nytt-system-item">
                <div>
                    <div class="nytt-system-name">
                        <span class="nytt-dot green"></span>
                        Database
                    </div>
                    <div class="nytt-system-sub">SQLite</div>
                </div>
                <span class="nytt-badge green">Connected</span>
            </div>

            <div class="nytt-system-item">
                <div>
                    <div class="nytt-system-name">
                        <span class="nytt-dot green"></span>
                        PHP
                    </div>
                    <div class="nytt-system-sub">v{{ PHP_VERSION }}</div>
                </div>
                <span class="nytt-badge green">Active</span>
            </div>

            <div class="nytt-system-item">
                <div>
                    <div class="nytt-system-name">
                        <span class="nytt-dot green"></span>
                        Filament
                    </div>
                    <div class="nytt-system-sub">v5.6.6</div>
                </div>
                <span class="nytt-badge green">Active</span>
            </div>

            <div class="nytt-system-item">
                <div>
                    <div class="nytt-system-name">
                        <span class="nytt-dot {{ \Illuminate\Support\Facades\Http::timeout(2)->get('http://127.0.0.1:8001')->successful() ? 'green' : 'red' }}"></span>
                        Python AI Service
                    </div>
                    <div class="nytt-system-sub">port 8001</div>
                </div>
                @php
                    try {
                        $pythonOk = \Illuminate\Support\Facades\Http::timeout(2)->get('http://127.0.0.1:8001')->successful();
                    } catch (\Exception $e) {
                        $pythonOk = false;
                    }
                @endphp
                <span class="nytt-badge {{ $pythonOk ? 'green' : 'red' }}">
                    {{ $pythonOk ? 'Running' : 'Offline' }}
                </span>
            </div>

        </div>
    </div>

    {{-- Platform Numbers --}}
    <div class="nytt-card">
        <div class="nytt-section-title">
            <span></span> Platform Overview
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px">
            <div style="text-align:center;padding:16px;background:#fef9f9;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#e74c3c">
                    {{ \App\Models\Brand::count() }}
                </div>
                <div style="font-size:13px;color:#888;margin-top:4px">Total Brands</div>
            </div>
            <div style="text-align:center;padding:16px;background:#f9fef9;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#2ecc71">
                    {{ \App\Models\SizeChart::count() }}
                </div>
                <div style="font-size:13px;color:#888;margin-top:4px">Size Charts</div>
            </div>
            <div style="text-align:center;padding:16px;background:#f9f9fe;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#3498db">
                    {{ \App\Models\Category::count() }}
                </div>
                <div style="font-size:13px;color:#888;margin-top:4px">Categories</div>
            </div>
            <div style="text-align:center;padding:16px;background:#fffdf9;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#f39c12">
                    {{ \App\Models\Scan::whereDate('created_at', today())->count() }}
                </div>
                <div style="font-size:13px;color:#888;margin-top:4px">Scans Today</div>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>