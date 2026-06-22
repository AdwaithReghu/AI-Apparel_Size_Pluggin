<x-filament-panels::page>
<style>
    .acc-wrap { padding: 0; }

    .acc-hero {
        background: linear-gradient(135deg, #6C63FF 0%, #4a44b5 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .acc-hero-left { flex: 1; }
    .acc-hero-title {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 4px 0;
    }
    .acc-hero-sub {
        font-size: 13px;
        opacity: 0.8;
        margin: 0;
    }
    .acc-hero-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: white;
        border: 3px solid rgba(255,255,255,0.4);
    }

    .acc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .acc-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
    }
    .acc-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
    }
    .acc-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .acc-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }
    .acc-card-sub {
        font-size: 13px;
        color: #888;
        margin: 0 0 20px 0;
        padding-left: 46px;
    }

    .acc-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    .acc-form-group { display: flex; flex-direction: column; gap: 6px; }
    .acc-form-group.full { grid-column: 1 / -1; }
    .acc-label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .acc-input {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1a1a2e;
        background: #fafafa;
        outline: none;
        transition: all 0.2s;
        width: 100%;
        box-sizing: border-box;
    }
    .acc-input:focus {
        border-color: #6C63FF;
        background: white;
        box-shadow: 0 0 0 3px rgba(108,99,255,0.1);
    }

    .acc-save-btn {
        background: linear-gradient(135deg, #6C63FF, #4a44b5);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .acc-save-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(108,99,255,0.3);
    }

    .acc-danger-btn {
        background: white;
        color: #e74c3c;
        border: 1.5px solid #fca5a5;
        padding: 12px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .acc-danger-btn:hover { background: #fef2f2; }

    .acc-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    .acc-toggle-row:last-of-type { border-bottom: none; }
    .acc-toggle-info { flex: 1; }
    .acc-toggle-title {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 2px;
    }
    .acc-toggle-desc {
        font-size: 12px;
        color: #888;
    }
    .acc-toggle {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .acc-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .acc-toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #e5e7eb;
        border-radius: 24px;
        transition: 0.3s;
    }
    .acc-toggle-slider:before {
        content: '';
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .acc-toggle input:checked + .acc-toggle-slider {
        background: #6C63FF;
    }
    .acc-toggle input:checked + .acc-toggle-slider:before {
        transform: translateX(20px);
    }

    .acc-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .acc-info-item {
        background: #f8f7ff;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    .acc-info-value {
        font-size: 24px;
        font-weight: 800;
        color: #6C63FF;
        margin-bottom: 4px;
    }
    .acc-info-label {
        font-size: 11px;
        color: #888;
        font-weight: 500;
    }
    .acc-info-date {
        font-size: 13px;
        font-weight: 600;
        color: #6C63FF;
    }

    .dark .acc-card {
        background: #1f2937;
        border-color: #374151;
    }
    .dark .acc-card-title,
    .dark .acc-toggle-title { color: #f9fafb; }
    .dark .acc-input {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
</style>

<div class="acc-wrap">

    {{-- Hero --}}
    <div class="acc-hero">
        <div class="acc-hero-left">
            <p class="acc-hero-title">Account Settings</p>
            <p class="acc-hero-sub">
                Manage your profile, security and preferences
            </p>
        </div>
        <div class="acc-hero-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    </div>

    {{-- Account Info Stats --}}
    <div class="acc-card">
        <div class="acc-card-header">
            <div class="acc-card-icon" style="background:#f5f3ff">📊</div>
            <p class="acc-card-title">Account Overview</p>
        </div>
        <p class="acc-card-sub">Your account activity at a glance</p>

        <div class="acc-info-grid">
            <div class="acc-info-item">
                <div class="acc-info-value">
                    {{ \App\Models\Garment::where('user_id', auth()->id())->count() }}
                </div>
                <div class="acc-info-label">Total Garments</div>
            </div>
            <div class="acc-info-item">
                <div class="acc-info-value">
                    {{ \App\Models\Scan::where('user_id', auth()->id())->count() }}
                </div>
                <div class="acc-info-label">Total Scans</div>
            </div>
            <div class="acc-info-item">
                <div class="acc-info-date">
                    {{ auth()->user()->created_at->format('M d, Y') }}
                </div>
                <div class="acc-info-label">Member Since</div>
            </div>
        </div>
    </div>

    {{-- Profile Information --}}
    <div class="acc-card">
        <div class="acc-card-header">
            <div class="acc-card-icon" style="background:#eff6ff">👤</div>
            <p class="acc-card-title">Profile Information</p>
        </div>
        <p class="acc-card-sub">Update your personal and business details</p>

        <div class="acc-form-grid">
            <div class="acc-form-group">
                <label class="acc-label">Full Name</label>
                <input
                    type="text"
                    wire:model="name"
                    class="acc-input"
                    placeholder="Your full name"
                />
            </div>
            <div class="acc-form-group">
                <label class="acc-label">Email Address</label>
                <input
                    type="email"
                    wire:model="email"
                    class="acc-input"
                    placeholder="your@email.com"
                />
            </div>
            <div class="acc-form-group">
                <label class="acc-label">Phone Number</label>
                <input
                    type="text"
                    wire:model="phone"
                    class="acc-input"
                    placeholder="+91 9876543210"
                />
            </div>
            <div class="acc-form-group">
                <label class="acc-label">Company Name</label>
                <input
                    type="text"
                    wire:model="company"
                    class="acc-input"
                    placeholder="Your company"
                />
            </div>
            <div class="acc-form-group">
                <label class="acc-label">Country</label>
                <input
                    type="text"
                    wire:model="country"
                    class="acc-input"
                    placeholder="India"
                />
            </div>
        </div>

        <button wire:click="updateProfile" class="acc-save-btn">
            💾 Save Profile
        </button>
    </div>

    {{-- Change Password --}}
    <div class="acc-card">
        <div class="acc-card-header">
            <div class="acc-card-icon" style="background:#fef9f9">🔒</div>
            <p class="acc-card-title">Change Password</p>
        </div>
        <p class="acc-card-sub">
            Keep your account secure with a strong password
        </p>

        <div style="max-width: 480px;">
            <div class="acc-form-group" style="margin-bottom:16px">
                <label class="acc-label">Current Password</label>
                <input
                    type="password"
                    wire:model="current_password"
                    class="acc-input"
                    placeholder="••••••••"
                />
            </div>
            <div class="acc-form-group" style="margin-bottom:16px">
                <label class="acc-label">New Password</label>
                <input
                    type="password"
                    wire:model="new_password"
                    class="acc-input"
                    placeholder="••••••••"
                />
            </div>
            <div class="acc-form-group" style="margin-bottom:20px">
                <label class="acc-label">Confirm New Password</label>
                <input
                    type="password"
                    wire:model="confirm_password"
                    class="acc-input"
                    placeholder="••••••••"
                />
            </div>

            <button wire:click="updatePassword" class="acc-save-btn">
                🔐 Update Password
            </button>
        </div>
    </div>

    {{-- Notification Preferences --}}
    <div class="acc-card">
        <div class="acc-card-header">
            <div class="acc-card-icon" style="background:#f0fdf4">🔔</div>
            <p class="acc-card-title">Notification Preferences</p>
        </div>
        <p class="acc-card-sub">
            Choose which notifications you want to receive
        </p>

        <div class="acc-toggle-row">
            <div class="acc-toggle-info">
                <div class="acc-toggle-title">Scan Complete</div>
                <div class="acc-toggle-desc">
                    Get notified when a garment scan is completed
                </div>
            </div>
            <label class="acc-toggle">
                <input
                    type="checkbox"
                    wire:model="notify_scan_complete"
                />
                <span class="acc-toggle-slider"></span>
            </label>
        </div>

        <div class="acc-toggle-row">
            <div class="acc-toggle-info">
                <div class="acc-toggle-title">Weekly Report</div>
                <div class="acc-toggle-desc">
                    Receive weekly summary of scans and measurements
                </div>
            </div>
            <label class="acc-toggle">
                <input
                    type="checkbox"
                    wire:model="notify_weekly_report"
                />
                <span class="acc-toggle-slider"></span>
            </label>
        </div>

        <div class="acc-toggle-row">
            <div class="acc-toggle-info">
                <div class="acc-toggle-title">New Features</div>
                <div class="acc-toggle-desc">
                    Get notified about new features and updates
                </div>
            </div>
            <label class="acc-toggle">
                <input
                    type="checkbox"
                    wire:model="notify_new_features"
                />
                <span class="acc-toggle-slider"></span>
            </label>
        </div>

        <div style="margin-top: 20px;">
            <button wire:click="updateNotifications" class="acc-save-btn">
                🔔 Save Preferences
            </button>
        </div>
    </div>

</div>
</x-filament-panels::page>