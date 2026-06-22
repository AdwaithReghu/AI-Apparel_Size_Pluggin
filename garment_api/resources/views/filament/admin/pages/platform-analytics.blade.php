<x-filament-panels::page>
<style>
    .pa-wrap { padding: 0; }

    .pa-hero {
        background: linear-gradient(135deg, #c0392b 0%, #922b21 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .pa-hero-title {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 4px 0;
    }
    .pa-hero-sub {
        font-size: 13px;
        opacity: 0.8;
        margin: 0;
    }
    .pa-hero-icon { font-size: 48px; opacity: 0.8; }

    .pa-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .pa-stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }
    .pa-stat-card::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 3px;
    }
    .pa-stat-card.red::after    { background: #e74c3c; }
    .pa-stat-card.blue::after   { background: #3498db; }
    .pa-stat-card.green::after  { background: #2ecc71; }
    .pa-stat-card.purple::after { background: #9b59b6; }
    .pa-stat-emoji {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }
    .pa-stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1;
        margin-bottom: 4px;
    }
    .pa-stat-label {
        font-size: 12px;
        color: #888;
        font-weight: 500;
    }

    .pa-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
    }
    .pa-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .pa-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .pa-activity-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 4px;
    }
    .pa-activity-item {
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .pa-activity-item.today  { background: #fef9f9; }
    .pa-activity-item.week   { background: #eff6ff; }
    .pa-activity-item.month  { background: #f0fdf4; }
    .pa-activity-value {
        font-size: 36px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
    }
    .pa-activity-item.today  .pa-activity-value { color: #e74c3c; }
    .pa-activity-item.week   .pa-activity-value { color: #3498db; }
    .pa-activity-item.month  .pa-activity-value { color: #2ecc71; }
    .pa-activity-label {
        font-size: 12px;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pa-activity-sub {
        font-size: 11px;
        color: #aaa;
        margin-top: 2px;
    }

    .pa-table {
        width: 100%;
        border-collapse: collapse;
    }
    .pa-table th {
        text-align: left;
        font-size: 11px;
        color: #aaa;
        font-weight: 700;
        padding: 8px 12px;
        border-bottom: 2px solid #f5f5f5;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pa-table td {
        padding: 14px 12px;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #fafafa;
        vertical-align: middle;
    }
    .pa-table tr:last-child td { border-bottom: none; }
    .pa-table tr:hover td { background: #fafafa; }

    .pa-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .pa-badge.blue   { background: #eff6ff; color: #2563eb; }
    .pa-badge.green  { background: #f0fdf4; color: #16a34a; }
    .pa-badge.purple { background: #f5f3ff; color: #7c3aed; }
    .pa-badge.red    { background: #fef2f2; color: #dc2626; }

    .pa-rank {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
    }
    .pa-rank.gold   { background: #fef9c3; color: #a16207; }
    .pa-rank.silver { background: #f1f5f9; color: #475569; }
    .pa-rank.bronze { background: #fff7ed; color: #c2410c; }
    .pa-rank.other  { background: #f5f5f5; color: #888; }

    .pa-merchant-name {
        font-weight: 700;
        color: #1a1a2e;
        font-size: 14px;
    }
    .pa-merchant-email {
        font-size: 11px;
        color: #aaa;
        margin-top: 2px;
    }

    .pa-progress-bar {
        height: 6px;
        background: #f0f0f0;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 6px;
    }
    .pa-progress-fill {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, #6C63FF, #4a44b5);
    }

    .dark .pa-stat-card,
    .dark .pa-card {
        background: #1f2937;
        border-color: #374151;
    }
    .dark .pa-stat-value,
    .dark .pa-card-title,
    .dark .pa-merchant-name { color: #f9fafb; }
    .dark .pa-table td { color: #d1d5db; border-color: #374151; }
    .dark .pa-table th { color: #6b7280; border-color: #374151; }
    .dark .pa-table tr:hover td { background: #374151; }
</style>

<div class="pa-wrap">

    {{-- Hero --}}
    <div class="pa-hero">
        <div>
            <p class="pa-hero-title">Platform Analytics</p>
            <p class="pa-hero-sub">
                Aggregate statistics across all merchants on NYTT platform
            </p>
        </div>
        <div class="pa-hero-icon">📊</div>
    </div>

    {{-- Platform Stats --}}
    <div class="pa-stats-grid">
        <div class="pa-stat-card red">
            <span class="pa-stat-emoji">👥</span>
            <div class="pa-stat-value">{{ $totalMerchants }}</div>
            <div class="pa-stat-label">Total Merchants</div>
        </div>
        <div class="pa-stat-card blue">
            <span class="pa-stat-emoji">📷</span>
            <div class="pa-stat-value">{{ $totalScans }}</div>
            <div class="pa-stat-label">Total Scans</div>
        </div>
        <div class="pa-stat-card green">
            <span class="pa-stat-emoji">👕</span>
            <div class="pa-stat-value">{{ $totalGarments }}</div>
            <div class="pa-stat-label">Total Garments</div>
        </div>
        <div class="pa-stat-card purple">
            <span class="pa-stat-emoji">🏷️</span>
            <div class="pa-stat-value">{{ $totalBrands }}</div>
            <div class="pa-stat-label">Total Brands</div>
        </div>
    </div>

    {{-- Scan Activity --}}
    <div class="pa-card">
        <div class="pa-card-header">
            <p class="pa-card-title">📈 Scan Activity</p>
        </div>
        <div class="pa-activity-grid">
            <div class="pa-activity-item today">
                <div class="pa-activity-value">{{ $scansToday }}</div>
                <div class="pa-activity-label">Today</div>
                <div class="pa-activity-sub">
                    {{ now()->format('M d, Y') }}
                </div>
            </div>
            <div class="pa-activity-item week">
                <div class="pa-activity-value">{{ $scansThisWeek }}</div>
                <div class="pa-activity-label">This Week</div>
                <div class="pa-activity-sub">
                    {{ now()->startOfWeek()->format('M d') }} —
                    {{ now()->endOfWeek()->format('M d') }}
                </div>
            </div>
            <div class="pa-activity-item month">
                <div class="pa-activity-value">{{ $scansThisMonth }}</div>
                <div class="pa-activity-label">This Month</div>
                <div class="pa-activity-sub">
                    {{ now()->format('F Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Top Merchants --}}
    <div class="pa-card">
        <div class="pa-card-header">
            <p class="pa-card-title">🏆 Top Merchants by Scans</p>
            <span class="pa-badge blue">Top 5</span>
        </div>

        @php
            $maxScans = collect($topMerchants)->max('scans') ?: 1;
        @endphp

        <table class="pa-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Merchant</th>
                    <th>Scans</th>
                    <th>Garments</th>
                    <th>API Calls</th>
                    <th>Activity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topMerchants as $index => $merchant)
                <tr>
                    <td>
                        @if($index === 0)
                            <span class="pa-rank gold">🥇</span>
                        @elseif($index === 1)
                            <span class="pa-rank silver">🥈</span>
                        @elseif($index === 2)
                            <span class="pa-rank bronze">🥉</span>
                        @else
                            <span class="pa-rank other">{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="pa-merchant-name">
                            {{ $merchant['name'] }}
                        </div>
                        <div class="pa-merchant-email">
                            {{ $merchant['email'] }}
                        </div>
                    </td>
                    <td>
                        <span class="pa-badge blue">
                            {{ $merchant['scans'] }}
                        </span>
                    </td>
                    <td>
                        <span class="pa-badge green">
                            {{ $merchant['garments'] }}
                        </span>
                    </td>
                    <td>
                        <span class="pa-badge purple">
                            {{ $merchant['api_calls'] }}
                        </span>
                    </td>
                    <td style="width: 120px;">
                        <div style="font-size:11px;color:#aaa;margin-bottom:4px">
                            {{ $merchant['scans'] }} scans
                        </div>
                        <div class="pa-progress-bar">
                            <div
                                class="pa-progress-fill"
                                style="width: {{ $maxScans > 0 ? ($merchant['scans'] / $maxScans * 100) : 0 }}%">
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if(empty($topMerchants))
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#aaa">
                        No merchant data available yet
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Platform Summary --}}
    <div class="pa-card">
        <div class="pa-card-header">
            <p class="pa-card-title">📋 Platform Summary</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px">
            <div style="text-align:center;padding:16px;background:#fef9f9;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#e74c3c">
                    {{ \App\Models\SizeChart::count() }}
                </div>
                <div style="font-size:12px;color:#888;margin-top:4px">
                    Size Charts
                </div>
            </div>
            <div style="text-align:center;padding:16px;background:#f0fdf4;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#2ecc71">
                    {{ \App\Models\Category::count() }}
                </div>
                <div style="font-size:12px;color:#888;margin-top:4px">
                    Categories
                </div>
            </div>
            <div style="text-align:center;padding:16px;background:#eff6ff;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#3498db">
                    {{ \App\Models\Garment::where('status','completed')->count() }}
                </div>
                <div style="font-size:12px;color:#888;margin-top:4px">
                    Completed Scans
                </div>
            </div>
            <div style="text-align:center;padding:16px;background:#f5f3ff;border-radius:12px">
                <div style="font-size:28px;font-weight:800;color:#9b59b6">
                    {{ \App\Models\Garment::where('status','pending')->count() }}
                </div>
                <div style="font-size:12px;color:#888;margin-top:4px">
                    Pending Scans
                </div>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>