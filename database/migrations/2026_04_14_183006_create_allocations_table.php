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
    Schema::create('allocations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('member_id')->constrained()->onDelete('cascade');
        $table->foreignId('project_id')->constrained()->onDelete('cascade');
        $table->decimal('allocated_amount', 15, 2);
        $table->enum('method', ['auto', 'manual', 'csv'])->default('auto');
        $table->enum('status', ['pending', 'notified', 'acknowledged'])->default('pending');
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->unique(['member_id', 'project_id']); // one allocation per member per project
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
