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
        // تحديث enum ليشمل 'employee'
        DB::statement("ALTER TABLE appointments MODIFY COLUMN cancelled_by ENUM('lawyer', 'client', 'employee') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع للقيم الأصلية
        DB::statement("ALTER TABLE appointments MODIFY COLUMN cancelled_by ENUM('lawyer', 'client') NULL");
    }
};

