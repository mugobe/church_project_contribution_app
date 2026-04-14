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
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('capacity_tier_id')->nullable()->constrained()->nullOnDelete();
        $table->string('member_number')->unique();   // e.g. SDC-0001
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->date('joined_date')->nullable();
        $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
