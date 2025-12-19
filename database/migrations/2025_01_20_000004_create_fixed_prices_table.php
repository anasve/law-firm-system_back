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
        Schema::create('fixed_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الخدمة (مثل: الأتعاب، النسخ، الطوابع، الترجمة)
            $table->string('name_ar'); // الاسم بالعربية
            $table->string('type'); // نوع السعر (fee, copy, stamp, translation, court_fee, document)
            $table->decimal('price', 10, 2); // السعر
            $table->string('unit')->nullable(); // الوحدة (صفحة، مستند، إلخ)
            $table->text('description')->nullable(); // وصف
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_prices');
    }
};

