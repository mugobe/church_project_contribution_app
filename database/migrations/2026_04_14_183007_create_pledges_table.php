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
    Schema::create('pledges', function (Blueprint $table) {
        $table->id();
        $table->foreignId('member_id')->constrained()->onDelete('cascade');
        $table->foreignId('project_id')->constrained()->onDelete('cascade');
        $table->decimal('pledged_amount', 15, 2);
        $table->date('pledge_date');
        $table->date('expiry_date')->nullable();
        $table->enum('status', ['active', 'fulfilled', 'expired', 'cancelled'])->default('active');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pledges');
    }
};
