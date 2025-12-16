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
        Schema::table('lawyer_availability', function (Blueprint $table) {
            $table->boolean('is_vacation')->default(false)->after('status');
            $table->text('vacation_reason')->nullable()->after('is_vacation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lawyer_availability', function (Blueprint $table) {
            $table->dropColumn(['is_vacation', 'vacation_reason']);
        });
    }
};

