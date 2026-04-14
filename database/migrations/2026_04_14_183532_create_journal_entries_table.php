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
    Schema::create('journal_entries', function (Blueprint $table) {
        $table->id();
        $table->string('reference');                     // e.g. CONTRIB-00001
        $table->foreignId('contribution_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
        $table->enum('type', ['debit', 'credit']);
        $table->decimal('amount', 15, 2);
        $table->string('description');
        $table->date('entry_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
