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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('lawyer_id')->nullable()->constrained('lawyers')->onDelete('set null');
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->onDelete('set null');
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->enum('preferred_channel', ['chat', 'meeting_link'])->default('chat');
            $table->string('meeting_link')->nullable(); // رابط الاجتماع عند اختيار meeting_link
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('legal_summary')->nullable(); // ملخص قانوني من المحامي
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};

