<x-filament-panels::page>
<style>
    .nytt-wrap { padding: 0; }

    .nytt-hero {
        background: linear-gradient(135deg, #6C63FF 0%, #4a44b5 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .nytt-hero-title {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 4px 0;
    }
    .nytt-hero-sub {
        font-size: 13px;
        opacity: 0.8;
        margin: 0;
    }
    .nytt-hero-icon {
        font-size: 48px;
        opacity: 0.8;
    }
    .nytt-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
    }
    .nytt-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .nytt-card-sub {
        font-size: 13px;
        color: #888;
        margin: 0 0 20px 0;
    }
    .nytt-divider {
        height: 1px;
        background: #f5f5f5;
        margin: 20px 0;
    }

    /* API Key */
    .nytt-key-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8f7ff;
        border: 1.5px solid #e0ddff;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 8px;
    }
    .nytt-key-icon {
        font-size: 20px;
    }
    .nytt-key-text {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #4a44b5;
        font-weight: 600;
        flex: 1;
        word-break: break-all;
    }
    .nytt-copy-btn {
        background: #6C63FF;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s;
    }
    .nytt-copy-btn:hover { background: #4a44b5; }
    .nytt-key-meta {
        font-size: 11px;
        color: #aaa;
        margin-top: 6px;
    }

    /* Stats */
    .nytt-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 4px;
    }
    .nytt-stat {
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    .nytt-stat.blue  { background: #eff6ff; }
    .nytt-stat.green { background: #f0fdf4; }
    .nytt-stat.purple{ background: #f5f3ff; }
    .nytt-stat-num {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .nytt-stat.blue   .nytt-stat-num { color: #2563eb; }
    .nytt-stat.green  .nytt-stat-num { color: #16a34a; }
    .nytt-stat.purple .nytt-stat-num { color: #7c3aed; }
    .nytt-stat-label {
        font-size: 12px;
        color: #888;
        font-weight: 500;
    }

    /* Embed code */
    .nytt-code-box {
        background: #0f172a;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 12px;
        position: relative;
    }
    .nytt-code-line {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.8;
    }
    .nytt-code-tag  { color: #7dd3fc; }
    .nytt-code-attr { color: #86efac; }
    .nytt-code-val  { color: #fcd34d; }
    .nytt-copy-embed {
        background: #6C63FF;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .nytt-copy-embed:hover { background: #4a44b5; }

    /* Steps */
    .nytt-steps {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .nytt-step {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }
    .nytt-step-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6C63FF, #4a44b5);
        color: white;
        font-weight: 800;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .nytt-step-content { flex: 1; }
    .nytt-step-title {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 2px;
    }
    .nytt-step-desc {
        font-size: 12px;
        color: #888;
        line-height: 1.5;
    }
    .nytt-step-divider {
        width: 1px;
        height: 16px;
        background: #e5e7eb;
        margin-left: 17px;
    }

    /* Regenerate button */
    .nytt-regen-btn {
        background: white;
        color: #e74c3c;
        border: 1.5px solid #fca5a5;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .nytt-regen-btn:hover {
        background: #fef2f2;
    }

    /* Dark mode */
    .dark .nytt-card {
        background: #1f2937;
        border-color: #374151;
    }
    .dark .nytt-card-title { color: #f9fafb; }
    .dark .nytt-key-box {
        background: #1e1b4b;
        border-color: #3730a3;
    }
    .dark .nytt-step-title { color: #f9fafb; }
</style>

<div class="nytt-wrap">

    {{-- Hero --}}
    <div class="nytt-hero">
        <div>
            <p class="nytt-hero-title">Integration Settings</p>
            <p class="nytt-hero-sub">
                Connect NYTT sizing widget to your e-commerce store
            </p>
        </div>
        <div class="nytt-hero-icon">🔌</div>
    </div>

    {{-- API Key --}}
    <div class="nytt-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
            <p class="nytt-card-title">🔑 Your API Key</p>
            <button
                class="nytt-regen-btn"
                wire:click="regenerateKey"
                onclick="return confirm('Regenerate API key? This will invalidate your current key.')">
                🔄 Regenerate
            </button>
        </div>
        <p class="nytt-card-sub">
            Keep this secret — never share it publicly or commit to code
        </p>

        <div class="nytt-key-box">
            <span class="nytt-key-icon">🔐</span>
            <span class="nytt-key-text" id="api-key-text">
                {{ $apiKey ?? 'No API key generated' }}
            </span>
            <button
                class="nytt-copy-btn"
                onclick="navigator.clipboard.writeText(document.getElementById('api-key-text').innerText).then(() => {this.textContent='✓ Copied!'; setTimeout(()=>this.textContent='📋 Copy',2000)})">
                📋 Copy
            </button>
        </div>
        <p class="nytt-key-meta">
            🕐 Generated:
            {{ auth()->user()->api_key_generated_at
                ? \Carbon\Carbon::parse(auth()->user()->api_key_generated_at)->diffForHumans()
                : 'Never' }}
        </p>
    </div>

    {{-- Usage Stats --}}
    <div class="nytt-card">
        <p class="nytt-card-title">📊 Usage Statistics</p>
        <p class="nytt-card-sub">Track how shoppers are using your sizing widget</p>

        <div class="nytt-stats-row">
            <div class="nytt-stat blue">
                <div class="nytt-stat-num">{{ $apiCallsToday }}</div>
                <div class="nytt-stat-label">API Calls Today</div>
            </div>
            <div class="nytt-stat green">
                <div class="nytt-stat-num">{{ $apiCallsMonth }}</div>
                <div class="nytt-stat-label">API Calls This Month</div>
            </div>
            <div class="nytt-stat purple">
                <div class="nytt-stat-num">
                    {{ \App\Models\Garment::where('user_id', auth()->id())->count() }}
                </div>
                <div class="nytt-stat-label">Total Garments</div>
            </div>
        </div>
    </div>

    {{-- Embed Code --}}
    <div class="nytt-card">
        <p class="nytt-card-title">💻 Widget Embed Code</p>
        <p class="nytt-card-sub">
            Paste this single line of code on your product pages
        </p>

        <div class="nytt-code-box">
            <div class="nytt-code-line">
                <span class="nytt-code-tag">&lt;script</span>
            </div>
            <div class="nytt-code-line" style="padding-left:16px">
                <span class="nytt-code-attr">src</span><span style="color:#fff">=</span><span class="nytt-code-val">"https://nytt.com/widget.js"</span>
            </div>
            <div class="nytt-code-line" style="padding-left:16px">
                <span class="nytt-code-attr">data-key</span><span style="color:#fff">=</span><span class="nytt-code-val">"{{ $apiKey }}"</span>
            </div>
            <div class="nytt-code-line" style="padding-left:16px">
                <span class="nytt-code-attr">data-brand</span><span style="color:#fff">=</span><span class="nytt-code-val">"YourBrand"</span>
            </div>
            <div class="nytt-code-line" style="padding-left:16px">
                <span class="nytt-code-attr">data-category</span><span style="color:#fff">=</span><span class="nytt-code-val">"Shirt"</span>
            </div>
            <div class="nytt-code-line">
                <span class="nytt-code-tag">&gt;&lt;/script&gt;</span>
            </div>
        </div>

        <button
            class="nytt-copy-embed"
            onclick="navigator.clipboard.writeText(`<script src='https://nytt.com/widget.js' data-key='{{ $apiKey }}' data-brand='YourBrand' data-category='Shirt'></script>`).then(() => {this.textContent='✓ Embed Code Copied!'; setTimeout(()=>this.textContent='📋 Copy Embed Code',2000)})">
            📋 Copy Embed Code
        </button>
    </div>

    {{-- How to integrate --}}
    <div class="nytt-card">
        <p class="nytt-card-title">🚀 How to Integrate</p>
        <p class="nytt-card-sub">
            Get your sizing widget live in 4 simple steps
        </p>

        <div class="nytt-steps">
            <div class="nytt-step">
                <div class="nytt-step-num">1</div>
                <div class="nytt-step-content">
                    <div class="nytt-step-title">Copy the embed code above</div>
                    <div class="nytt-step-desc">
                        Click "Copy Embed Code" button to copy to clipboard
                    </div>
                </div>
            </div>
            <div class="nytt-step-divider"></div>

            <div class="nytt-step">
                <div class="nytt-step-num">2</div>
                <div class="nytt-step-content">
                    <div class="nytt-step-title">Update brand and category</div>
                    <div class="nytt-step-desc">
                        Replace "YourBrand" and "Shirt" with your actual
                        brand name and product category
                    </div>
                </div>
            </div>
            <div class="nytt-step-divider"></div>

            <div class="nytt-step">
                <div class="nytt-step-num">3</div>
                <div class="nytt-step-content">
                    <div class="nytt-step-title">Paste on your product pages</div>
                    <div class="nytt-step-desc">
                        Add the code before the closing &lt;/body&gt; tag
                        on each product page
                    </div>
                </div>
            </div>
            <div class="nytt-step-divider"></div>

            <div class="nytt-step">
                <div class="nytt-step-num">4</div>
                <div class="nytt-step-content">
                    <div class="nytt-step-title">Widget appears automatically!</div>
                    <div class="nytt-step-desc">
                        Shoppers will see "📏 Find My Size" button
                        and get instant size recommendations
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>