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
            $table->foreignId('availability_id')->nullable()->after('consultation_id')->constrained('lawyer_availability')->onDelete('set null');
            $table->text('cancellation_reason')->nullable()->after('notes');
            $table->enum('cancelled_by', ['lawyer', 'client'])->nullable()->after('cancellation_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['availability_id']);
            $table->dropColumn(['availability_id', 'cancellation_reason', 'cancelled_by']);
        });
    }
};

