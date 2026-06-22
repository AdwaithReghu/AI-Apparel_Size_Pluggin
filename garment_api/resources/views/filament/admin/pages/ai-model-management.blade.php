<x-filament-panels::page>
<style>
    .ai-wrap { padding: 0; }

    .ai-hero {
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
    .ai-hero::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(108,99,255,0.15);
    }
    .ai-hero::after {
        content: '';
        position: absolute;
        bottom: -30px; right: 100px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(108,99,255,0.1);
    }
    .ai-hero-title {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 4px 0;
    }
    .ai-hero-sub {
        font-size: 13px;
        opacity: 0.7;
        margin: 0 0 16px 0;
    }
    .ai-hero-badge {
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
    .ai-hero-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #2ed573;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .ai-hero-icon {
        font-size: 56px;
        position: relative;
        z-index: 1;
    }

    .ai-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .ai-stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #f0f0f0;
        text-align: center;
    }
    .ai-stat-emoji {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }
    .ai-stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1;
        margin-bottom: 4px;
    }
    .ai-stat-label {
        font-size: 12px;
        color: #888;
    }

    .ai-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
    }
    .ai-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 4px 0;
    }
    .ai-card-sub {
        font-size: 13px;
        color: #888;
        margin: 0 0 20px 0;
    }

    .ai-endpoint-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-radius: 12px;
        background: #f8fafc;
        margin-bottom: 12px;
        border: 1px solid #f0f0f0;
        transition: all 0.2s;
    }
    .ai-endpoint-item:hover {
        border-color: #6C63FF;
        background: #f5f3ff;
    }
    .ai-endpoint-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .ai-endpoint-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .ai-endpoint-method {
        font-size: 11px;
        font-weight: 800;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        background: #6C63FF;
        margin-bottom: 4px;
        display: inline-block;
    }
    .ai-endpoint-path {
        font-family: 'Courier New', monospace;
        font-size: 14px;
        font-weight: 700;
        color: #1a1a2e;
    }
    .ai-endpoint-desc {
        font-size: 12px;
        color: #888;
        margin-top: 2px;
    }
    .ai-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .ai-status-badge.active {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .ai-status-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #16a34a;
    }

    .ai-retrain-card {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 20px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }
    .ai-retrain-title {
        font-size: 18px;
        font-weight: 800;
        margin: 0 0 6px 0;
    }
    .ai-retrain-desc {
        font-size: 13px;
        opacity: 0.7;
        margin: 0 0 8px 0;
        max-width: 500px;
    }
    .ai-retrain-warning {
        font-size: 12px;
        color: #fbbf24;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ai-retrain-btn {
        background: linear-gradient(135deg, #6C63FF, #4a44b5);
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .ai-retrain-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108,99,255,0.4);
    }

    .ai-metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .ai-metric {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #f0f0f0;
    }
    .ai-metric-label {
        font-size: 11px;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .ai-metric-value {
        font-size: 22px;
        font-weight: 800;
        color: #1a1a2e;
    }
    .ai-metric-sub {
        font-size: 11px;
        color: #aaa;
        margin-top: 2px;
    }

    .dark .ai-stat-card,
    .dark .ai-card,
    .dark .ai-endpoint-item,
    .dark .ai-metric {
        background: #1f2937;
        border-color: #374151;
    }
    .dark .ai-stat-value,
    .dark .ai-card-title,
    .dark .ai-endpoint-path,
    .dark .ai-metric-value { color: #f9fafb; }
    .dark .ai-endpoint-item { background: #111827; }
    .dark .ai-endpoint-item:hover { background: #1e1b4b; }
    .dark .ai-metric { background: #111827; }
</style>

<div class="ai-wrap">

    {{-- Hero --}}
    <div class="ai-hero">
        <div>
            <p class="ai-hero-title">AI Model Management</p>
            <p class="ai-hero-sub">
                Monitor and manage the NYTT AI sizing engine
            </p>
            <div class="ai-hero-badge">
                <span class="ai-hero-dot"></span>
                Model Active — v{{ $modelVersion }}
            </div>
        </div>
        <div class="ai-hero-icon">🤖</div>
    </div>

    {{-- Stats --}}
    <div class="ai-stats-grid">
        <div class="ai-stat-card">
            <span class="ai-stat-emoji">🎯</span>
            <div class="ai-stat-value">{{ $modelVersion }}</div>
            <div class="ai-stat-label">Model Version</div>
        </div>
        <div class="ai-stat-card">
            <span class="ai-stat-emoji">📊</span>
            <div class="ai-stat-value">{{ $totalPredictions }}</div>
            <div class="ai-stat-label">Total Predictions</div>
        </div>
        <div class="ai-stat-card">
            <span class="ai-stat-emoji">✅</span>
            <div class="ai-stat-value">{{ $avgConfidence }}%</div>
            <div class="ai-stat-label">Avg Confidence</div>
        </div>
        <div class="ai-stat-card">
            <span class="ai-stat-emoji">🟢</span>
            <div class="ai-stat-value" style="text-transform:uppercase">
                {{ $modelStatus }}
            </div>
            <div class="ai-stat-label">Model Status</div>
        </div>
    </div>

    {{-- Endpoints --}}
    <div class="ai-card">
        <p class="ai-card-title">⚡ Model Endpoints</p>
        <p class="ai-card-sub">
            Live API endpoints powering the NYTT sizing engine
        </p>

        <div class="ai-endpoint-item">
            <div class="ai-endpoint-left">
                <div class="ai-endpoint-icon" style="background:#eff6ff">
                    📐
                </div>
                <div>
                    <div class="ai-endpoint-method">POST</div>
                    <div class="ai-endpoint-path">/extract-dimensions</div>
                    <div class="ai-endpoint-desc">
                        Extracts garment measurements from uploaded image
                    </div>
                </div>
            </div>
            <div class="ai-status-badge active">
                <span class="ai-status-dot"></span>
                Active
            </div>
        </div>

        <div class="ai-endpoint-item">
            <div class="ai-endpoint-left">
                <div class="ai-endpoint-icon" style="background:#f0fdf4">
                    🎯
                </div>
                <div>
                    <div class="ai-endpoint-method">POST</div>
                    <div class="ai-endpoint-path">/predict-size</div>
                    <div class="ai-endpoint-desc">
                        Predicts garment size from shopper body measurements
                    </div>
                </div>
            </div>
            <div class="ai-status-badge active">
                <span class="ai-status-dot"></span>
                Active
            </div>
        </div>

        <div class="ai-endpoint-item" style="margin-bottom:0">
            <div class="ai-endpoint-left">
                <div class="ai-endpoint-icon" style="background:#f5f3ff">
                    🔄
                </div>
                <div>
                    <div class="ai-endpoint-method">POST</div>
                    <div class="ai-endpoint-path">/retrain</div>
                    <div class="ai-endpoint-desc">
                        Triggers model retraining using recent feedback data
                    </div>
                </div>
            </div>
            <div class="ai-status-badge active">
                <span class="ai-status-dot"></span>
                Active
            </div>
        </div>
    </div>

    {{-- Model Metrics --}}
    <div class="ai-card">
        <p class="ai-card-title">📈 Model Metrics</p>
        <p class="ai-card-sub">
            Current performance indicators for the AI model
        </p>

        <div class="ai-metrics-grid">
            <div class="ai-metric">
                <div class="ai-metric-label">Total Predictions</div>
                <div class="ai-metric-value">{{ $totalPredictions }}</div>
                <div class="ai-metric-sub">All time</div>
            </div>
            <div class="ai-metric">
                <div class="ai-metric-label">Average Confidence</div>
                <div class="ai-metric-value">{{ $avgConfidence }}%</div>
                <div class="ai-metric-sub">Across all predictions</div>
            </div>
            <div class="ai-metric">
                <div class="ai-metric-label">Model Version</div>
                <div class="ai-metric-value">{{ $modelVersion }}</div>
                <div class="ai-metric-sub">Current production</div>
            </div>
            <div class="ai-metric">
                <div class="ai-metric-label">Total Garments</div>
                <div class="ai-metric-value">
                    {{ \App\Models\Garment::count() }}
                </div>
                <div class="ai-metric-sub">Training data points</div>
            </div>
            <div class="ai-metric">
                <div class="ai-metric-label">Completed Scans</div>
                <div class="ai-metric-value">
                    {{ \App\Models\Garment::where('status','completed')->count() }}
                </div>
                <div class="ai-metric-sub">Successfully measured</div>
            </div>
            <div class="ai-metric">
                <div class="ai-metric-label">Active Merchants</div>
                <div class="ai-metric-value">
                    {{ \App\Models\User::count() }}
                </div>
                <div class="ai-metric-sub">Using the platform</div>
            </div>
        </div>
    </div>

    {{-- Retrain --}}
    <div class="ai-retrain-card">
        <div>
            <p class="ai-retrain-title">🔄 Trigger Model Retraining</p>
            <p class="ai-retrain-desc">
                Retrain the AI model using recent scan feedback and
                correction data to improve measurement accuracy
            </p>
            <p class="ai-retrain-warning">
                ⚠️ Retraining may take several minutes to complete
            </p>
        </div>
        <button
            wire:click="triggerRetrain"
            wire:confirm="Are you sure you want to trigger model retraining? This process cannot be stopped once started."
            class="ai-retrain-btn">
            🚀 Trigger Retraining
        </button>
    </div>

</div>
</x-filament-panels::page>