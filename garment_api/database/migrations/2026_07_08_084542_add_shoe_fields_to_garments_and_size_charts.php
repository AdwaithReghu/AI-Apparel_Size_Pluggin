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
    Schema::table('garments', function (Blueprint $table) {
        $table->decimal('shoe_length',   5, 1)->nullable()->after('rise');
        $table->decimal('shoe_width',    5, 1)->nullable()->after('shoe_length');
        $table->decimal('heel_width',    5, 1)->nullable()->after('shoe_width');
        $table->string('shoe_size_eu',  10)->nullable()->after('heel_width');
        $table->string('shoe_size_uk',  10)->nullable()->after('shoe_size_eu');
        $table->string('shoe_size_us',  10)->nullable()->after('shoe_size_uk');
        $table->decimal('fits_foot_min', 5, 1)->nullable()->after('shoe_size_us');
        $table->decimal('fits_foot_max', 5, 1)->nullable()->after('fits_foot_min');
    });

    Schema::table('size_charts', function (Blueprint $table) {
        $table->decimal('foot_length_min', 5, 1)->nullable()->after('inseam_max');
        $table->decimal('foot_length_max', 5, 1)->nullable()->after('foot_length_min');
        $table->decimal('shoe_width_min',  5, 1)->nullable()->after('foot_length_max');
        $table->decimal('shoe_width_max',  5, 1)->nullable()->after('shoe_width_min');
    });
}

public function down(): void
{
    Schema::table('garments', function (Blueprint $table) {
        $table->dropColumn([
            'shoe_length', 'shoe_width', 'heel_width',
            'shoe_size_eu', 'shoe_size_uk', 'shoe_size_us',
            'fits_foot_min', 'fits_foot_max',
        ]);
    });

    Schema::table('size_charts', function (Blueprint $table) {
        $table->dropColumn([
            'foot_length_min', 'foot_length_max',
            'shoe_width_min',  'shoe_width_max',
        ]);
    });
}
};
