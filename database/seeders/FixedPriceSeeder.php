<?php

namespace Database\Seeders;

use App\Models\FixedPrice;
use Illuminate\Database\Seeder;

class FixedPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prices = [
            [
                'name' => 'Legal Fees',
                'name_ar' => 'الأتعاب القانونية',
                'type' => 'fee',
                'price' => 200.00,
                'unit' => 'hour',
                'description' => 'Hourly legal consultation fee',
                'is_active' => true,
            ],
            [
                'name' => 'Document Copy',
                'name_ar' => 'نسخ المستندات',
                'type' => 'copy',
                'price' => 5.00,
                'unit' => 'page',
                'description' => 'Per page document copy fee',
                'is_active' => true,
            ],
            [
                'name' => 'Official Stamps',
                'name_ar' => 'الطوابع',
                'type' => 'stamp',
                'price' => 10.00,
                'unit' => 'stamp',
                'description' => 'Official stamp fee',
                'is_active' => true,
            ],
            [
                'name' => 'Translation',
                'name_ar' => 'الترجمة',
                'type' => 'translation',
                'price' => 60.00,
                'unit' => 'page',
                'description' => 'Document translation per page',
                'is_active' => true,
            ],
            [
                'name' => 'Court Fees',
                'name_ar' => 'رسوم المحكمة',
                'type' => 'court_fee',
                'price' => 50.00,
                'unit' => 'filing',
                'description' => 'Court filing fee',
                'is_active' => true,
            ],
            [
                'name' => 'Document Preparation',
                'name_ar' => 'إعداد المستندات',
                'type' => 'document',
                'price' => 100.00,
                'unit' => 'document',
                'description' => 'Legal document preparation fee',
                'is_active' => true,
            ],
        ];

        foreach ($prices as $price) {
            FixedPrice::create($price);
        }
    }
}

