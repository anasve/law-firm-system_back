<?php

namespace Database\Seeders;

use App\Models\Law;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
    {
        $laws = [
            [
                'title'       => 'Civil Rights Act',
                'category'    => 'Civil Law',
                'summary'     => 'A landmark law prohibiting discrimination on the basis of race, color, religion, sex, or national origin.',
                'full_content'=> 'The Civil Rights Act of 1964 is a significant piece of legislation in the United States that outlawed major forms of discrimination...',
                'status'      => 'draft',
            ],
            [
                'title'       => 'Criminal Code Reform',
                'category'    => 'Criminal Law',
                'summary'     => 'Modernization of the criminal code to improve legal clarity and fairness.',
                'full_content'=> 'This law addresses the criminal procedure changes including sentencing and evidence reform...',
                'status'      => 'draft',
            ],
            [
                'title'       => 'Family Law Amendment',
                'category'    => 'Family Law',
                'summary'     => 'Updated regulations concerning marriage, divorce, and child custody.',
                'full_content'=> 'The amendment improves protection for minors and updates divorce procedures...',
                'status'      => 'draft',
            ],
            [
                'title'       => 'Employment Law Update',
                'category'    => 'Labor Law',
                'summary'     => 'Defines new standards for workplace safety and minimum wage policies.',
                'full_content'=> 'Employers are now required to follow stricter safety measures and wage guarantees...',
                'status'      => 'draft',
            ],
            [
                'title'       => 'Environmental Protection Law',
                'category'    => 'Environmental Law',
                'summary'     => 'New standards for pollution control and sustainability enforcement.',
                'full_content'=> 'This law targets industrial pollutants and outlines penalties for environmental violations...',
                'status'      => 'draft',
            ],
        ];

        foreach ($laws as $law) {
            Law::create([
                'title'        => $law['title'],
                'category'     => $law['category'],
                'summary'      => $law['summary'],
                'full_content' => $law['full_content'],
                'status'       => $law['status'],
            ]);
        }
    }
}
