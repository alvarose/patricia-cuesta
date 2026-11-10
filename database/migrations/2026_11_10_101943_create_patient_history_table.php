<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('field');
            $table->text('old_value')->nullable();
            $table->text('value')->nullable();
            $table->string('source');
            $table->nullableMorphs('actor');
            $table->text('description')->nullable();
            $table->dateTime('effective_at');

            $table->index(['patient_id', 'effective_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_history');
    }
};
