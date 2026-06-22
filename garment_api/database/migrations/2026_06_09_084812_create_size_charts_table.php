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
    Schema::create('size_charts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade');
        $table->string('category');
        $table->string('size_label');
        $table->decimal('chest_min', 8, 1)->nullable();
        $table->decimal('chest_max', 8, 1)->nullable();
        $table->decimal('waist_min', 8, 1)->nullable();
        $table->decimal('waist_max', 8, 1)->nullable();
        $table->decimal('length_min', 8, 1)->nullable();
        $table->decimal('length_max', 8, 1)->nullable();
        $table->decimal('shoulder_min', 8, 1)->nullable();
        $table->decimal('shoulder_max', 8, 1)->nullable();
        $table->decimal('sleeve_min', 8, 1)->nullable();
        $table->decimal('sleeve_max', 8, 1)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_charts');
    }
};
