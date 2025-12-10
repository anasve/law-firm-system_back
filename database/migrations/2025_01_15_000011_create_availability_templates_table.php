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
        Schema::create('availability_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->onDelete('cascade');
            $table->string('name'); // اسم القالب (مثلاً: "أوقات العمل الأسبوعية")
            $table->time('start_time');
            $table->time('end_time');
            $table->json('days_of_week'); // [1,2,3,4,5] للأيام من الاثنين للجمعة
            $table->date('start_date')->nullable(); // تاريخ بداية التطبيق
            $table->date('end_date')->nullable(); // تاريخ نهاية التطبيق
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_templates');
    }
};

