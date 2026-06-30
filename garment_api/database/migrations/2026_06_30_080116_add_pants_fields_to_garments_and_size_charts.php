<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add pants-specific measurement columns to garments table
        Schema::table('garments', function (Blueprint $table) {
            $table->decimal('hip',     8, 2)->nullable()->after('shoulder');
            $table->decimal('thigh',   8, 2)->nullable()->after('hip');
            $table->decimal('knee',    8, 2)->nullable()->after('thigh');
            $table->decimal('ankle',   8, 2)->nullable()->after('knee');
            $table->decimal('outseam', 8, 2)->nullable()->after('ankle');
            $table->decimal('inseam',  8, 2)->nullable()->after('outseam');
            $table->decimal('rise',    8, 2)->nullable()->after('inseam');

            // garment_type: 'shirt' or 'pants' — drives which fields are relevant
            $table->string('garment_type', 20)->default('shirt')->after('category');
        });

        // Same additions to size_charts so merchants can set pants size ranges
        Schema::table('size_charts', function (Blueprint $table) {
            $table->decimal('hip_min',     8, 2)->nullable()->after('waist_max');
            $table->decimal('hip_max',     8, 2)->nullable()->after('hip_min');
            $table->decimal('thigh_min',   8, 2)->nullable()->after('hip_max');
            $table->decimal('thigh_max',   8, 2)->nullable()->after('thigh_min');
            $table->decimal('inseam_min',  8, 2)->nullable()->after('thigh_max');
            $table->decimal('inseam_max',  8, 2)->nullable()->after('inseam_min');

            $table->string('garment_type', 20)->default('shirt')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('garments', function (Blueprint $table) {
            $table->dropColumn([
                'hip', 'thigh', 'knee', 'ankle',
                'outseam', 'inseam', 'rise', 'garment_type',
            ]);
        });

        Schema::table('size_charts', function (Blueprint $table) {
            $table->dropColumn([
                'hip_min', 'hip_max', 'thigh_min', 'thigh_max',
                'inseam_min', 'inseam_max', 'garment_type',
            ]);
        });
    }
};