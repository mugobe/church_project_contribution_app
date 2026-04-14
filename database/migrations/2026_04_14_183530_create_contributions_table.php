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
    Schema::create('contributions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('member_id')->constrained()->onDelete('cascade');
        $table->foreignId('project_id')->constrained()->onDelete('cascade');
        $table->decimal('amount', 15, 2);
        $table->date('contribution_date');
        $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'other'])->default('cash');
        $table->string('reference_number')->nullable();  // mobile money ref, bank ref etc
        $table->string('recorded_by')->nullable();       // admin who entered it
        $table->text('notes')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
