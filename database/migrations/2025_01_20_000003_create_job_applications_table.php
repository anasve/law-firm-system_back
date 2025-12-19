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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['lawyer', 'employee']); // نوع الطلب
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // معلومات عامة
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->integer('age');
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            
            // حقول خاصة بالمحامي
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->onDelete('set null');
            $table->integer('experience_years')->nullable();
            $table->text('bio')->nullable();
            $table->string('certificate')->nullable(); // للمحامي فقط
            
            // معلومات إضافية
            $table->text('admin_notes')->nullable(); // ملاحظات من الإدمن
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

