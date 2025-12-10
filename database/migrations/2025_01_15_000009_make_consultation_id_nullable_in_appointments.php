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
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->foreignId('consultation_id')->nullable()->change();
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->foreignId('consultation_id')->nullable(false)->change();
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
        });
    }
};

