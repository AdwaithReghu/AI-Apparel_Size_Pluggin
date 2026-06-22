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
        $table->string('api_key')->nullable()->unique()->after('email');
        $table->integer('api_calls_today')->default(0)->after('api_key');
        $table->integer('api_calls_month')->default(0)->after('api_calls_today');
        $table->timestamp('api_key_generated_at')->nullable()->after('api_calls_month');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'api_key',
            'api_calls_today',
            'api_calls_month',
            'api_key_generated_at',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
};