<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحديث القيم الموجودة
        DB::statement("ALTER TABLE consultations MODIFY COLUMN preferred_channel ENUM('chat', 'in_office', 'call', 'appointment') DEFAULT 'chat'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE consultations MODIFY COLUMN preferred_channel ENUM('chat', 'in_office', 'call') DEFAULT 'chat'");
    }
};

