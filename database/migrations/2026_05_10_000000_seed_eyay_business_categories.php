<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedEyAyBusinessCategories extends Migration
{
    public function up()
    {
        $categories = [
            'Klantenservice AI',
            'Sales AI',
            'Marketing AI',
            'HR & recruitment AI',
            'Finance AI',
            'Operations AI',
            'Data-analyse AI',
            'Documentverwerking',
            'Chatbots & voicebots',
            'AI-agents',
            'Maatwerk AI',
            'Integratiepartners',
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category],
                [
                    'name' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down()
    {
        DB::table('categories')
            ->whereIn('name', [
                'Klantenservice AI',
                'Sales AI',
                'Marketing AI',
                'HR & recruitment AI',
                'Finance AI',
                'Operations AI',
                'Data-analyse AI',
                'Documentverwerking',
                'Chatbots & voicebots',
                'AI-agents',
                'Maatwerk AI',
                'Integratiepartners',
            ])
            ->delete();
    }
}
