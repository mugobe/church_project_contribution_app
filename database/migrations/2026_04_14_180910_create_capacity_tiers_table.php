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
    Schema::create('capacity_tiers', function (Blueprint $table) {
        $table->id();
        $table->string('name');              // Platinum, Gold, Silver, Standard
        $table->integer('weight');           // 4, 3, 2, 1
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacity_tiers');
    }
};
