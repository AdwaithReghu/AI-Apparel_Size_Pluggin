<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable()->after('email');
        $table->string('company')->nullable()->after('phone');
        $table->string('country')->nullable()->after('company');
        $table->boolean('notify_scan_complete')->default(true)->after('country');
        $table->boolean('notify_weekly_report')->default(true)->after('notify_scan_complete');
        $table->boolean('notify_new_features')->default(false)->after('notify_weekly_report');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'phone',
            'company',
            'country',
            'notify_scan_complete',
            'notify_weekly_report',
            'notify_new_features',
        ]);
    });
}
};
