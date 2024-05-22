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
        Schema::table('allowed_domains', function (Blueprint $table) {
            $table->foreign(['form_id'])->references(['id'])->on('forms')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowed_domains', function (Blueprint $table) {
            $table->dropForeign('allowed_domains_form_id_foreign');
        });
    }
};
