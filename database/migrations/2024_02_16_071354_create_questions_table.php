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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id');
            $table->string('name');
            $table->enum('choice_type', ['short answer', 'paragraph', 'date', 'time', 'multiple choice', 'dropdown', 'checkboxes']);
            $table->string('choices')->nullable();
            $table->boolean('is_required')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
