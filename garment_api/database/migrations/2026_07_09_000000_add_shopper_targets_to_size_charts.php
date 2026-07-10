<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Workstream 2 — merchant-declared target-shopper attributes per size.
 *
 * ArUco already fills the garment measurement columns (chest/waist/…). These
 * new columns hold what the merchant declares for each size during scanning:
 * which shoppers that size is cut for. They are the sizing model's own features, so they
 * turn every scanned brand into a "known brand" the model can predict for.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('size_charts', function (Blueprint $table) {
            $table->string('target_gender', 20)->nullable()->after('size_label'); // men|women|unisex
            $table->decimal('weight_min', 6, 2)->nullable()->after('target_gender');
            $table->decimal('weight_max', 6, 2)->nullable()->after('weight_min');
            $table->unsignedSmallInteger('age_min')->nullable()->after('weight_max');
            $table->unsignedSmallInteger('age_max')->nullable()->after('age_min');
            $table->json('body_types')->nullable()->after('age_max'); // ["slim","athletic"]
        });
    }

    public function down(): void
    {
        Schema::table('size_charts', function (Blueprint $table) {
            $table->dropColumn([
                'target_gender', 'weight_min', 'weight_max',
                'age_min', 'age_max', 'body_types',
            ]);
        });
    }
};
