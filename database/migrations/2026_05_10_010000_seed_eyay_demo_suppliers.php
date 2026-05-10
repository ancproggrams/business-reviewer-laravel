<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedEyAyDemoSuppliers extends Migration
{
    public function up()
    {
        $reviewerId = $this->upsertUser([
            'name' => 'EyAy',
            'surname' => 'Reviewer',
            'email' => 'reviewer@eyay.local',
            'type' => 'reviewer',
        ]);

        $suppliers = [
            [
                'name' => 'FlowMind AI',
                'email' => 'flowmind@eyay.local',
                'city' => 'Amsterdam',
                'country' => 'Nederland',
                'categories' => ['Klantenservice AI', 'Chatbots & voicebots', 'AI-agents'],
                'description' => 'Specialist in klantenservice AI, kennisbankautomatisering en AI-agents voor MKB en enterprise.',
                'rating' => 5,
                'image' => 'images/people-collaboration.jpg',
            ],
            [
                'name' => 'NovaAutomate',
                'email' => 'novaautomate@eyay.local',
                'city' => 'Rotterdam',
                'country' => 'Nederland',
                'categories' => ['Operations AI', 'Integratiepartners', 'Maatwerk AI'],
                'description' => 'Bouwt workflow automation tussen CRM, ERP en interne teams met meetbare procesbesparing.',
                'rating' => 4,
                'image' => 'images/people-governance.jpg',
            ],
            [
                'name' => 'DialogIQ',
                'email' => 'dialogiq@eyay.local',
                'city' => 'Utrecht',
                'country' => 'Nederland',
                'categories' => ['Klantenservice AI', 'Sales AI', 'Chatbots & voicebots'],
                'description' => 'Conversationele AI voor support en sales, inclusief voicebot routing en live agent assist.',
                'rating' => 5,
                'image' => 'images/people-collaboration.jpg',
            ],
            [
                'name' => 'DataPilot',
                'email' => 'datapilot@eyay.local',
                'city' => 'Eindhoven',
                'country' => 'Nederland',
                'categories' => ['Data-analyse AI', 'Finance AI', 'Operations AI'],
                'description' => 'Predictive analytics en decision intelligence voor teams die betere forecasts nodig hebben.',
                'rating' => 4,
                'image' => 'images/people-governance.jpg',
            ],
            [
                'name' => 'AssistLayer',
                'email' => 'assistlayer@eyay.local',
                'city' => 'Den Haag',
                'country' => 'Nederland',
                'categories' => ['Klantenservice AI', 'Documentverwerking', 'AI-agents'],
                'description' => 'Beheerde AI-assistenten voor support, intake en interne documentvragen zonder groot IT-team.',
                'rating' => 4,
                'image' => 'images/people-collaboration.jpg',
            ],
            [
                'name' => 'LegalBotics',
                'email' => 'legalbotics@eyay.local',
                'city' => 'Amsterdam',
                'country' => 'Nederland',
                'categories' => ['Documentverwerking', 'Maatwerk AI', 'AI-agents'],
                'description' => 'Document AI voor contractanalyse, juridische intake en gecontroleerde RAG-implementaties.',
                'rating' => 5,
                'image' => 'images/people-governance.jpg',
            ],
            [
                'name' => 'RetailSense AI',
                'email' => 'retailsense@eyay.local',
                'city' => 'Tilburg',
                'country' => 'Nederland',
                'categories' => ['Marketing AI', 'Sales AI', 'Data-analyse AI'],
                'description' => 'AI voor productaanbevelingen, campagnepersonalisatie en voorraadgestuurde klantsegmenten.',
                'rating' => 4,
                'image' => 'images/people-collaboration.jpg',
            ],
            [
                'name' => 'OpsGenie AI',
                'email' => 'opsgenie@eyay.local',
                'city' => 'Amersfoort',
                'country' => 'Nederland',
                'categories' => ['Operations AI', 'HR & recruitment AI', 'Integratiepartners'],
                'description' => 'AI-operating model, procesagents en change-aanpak voor grote organisaties met meerdere teams.',
                'rating' => 5,
                'image' => 'images/people-governance.jpg',
            ],
        ];

        foreach ($suppliers as $supplier) {
            $ownerId = $this->upsertUser([
                'name' => $supplier['name'],
                'surname' => 'Team',
                'email' => $supplier['email'],
                'type' => 'business',
                'city' => $supplier['city'],
                'country' => $supplier['country'],
            ]);

            DB::table('businesses')->updateOrInsert(
                ['slug' => Str::slug($supplier['name'])],
                [
                    'name' => $supplier['name'],
                    'country' => $supplier['country'],
                    'address' => 'EyAy verified supplier profile',
                    'city' => $supplier['city'],
                    'phone_number' => '+31 20 000 0000',
                    'website_url' => 'https://' . Str::slug($supplier['name'], '') . '.example',
                    'email' => $supplier['email'],
                    'slug' => Str::slug($supplier['name']),
                    'geo_location' => json_encode([52.3676, 4.9041]),
                    'description' => $supplier['description'],
                    'average_review' => $supplier['rating'],
                    'front_image' => $supplier['image'],
                    'owner_id' => $ownerId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $businessId = DB::table('businesses')->where('slug', Str::slug($supplier['name']))->value('id');

            foreach ($supplier['categories'] as $categoryName) {
                $categoryId = DB::table('categories')->where('name', $categoryName)->value('id');

                if ($categoryId) {
                    DB::table('business_category')->updateOrInsert(
                        ['business_id' => $businessId, 'category_id' => $categoryId],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            DB::table('reviews')->updateOrInsert(
                ['business_id' => $businessId, 'user_id' => $reviewerId],
                [
                    'body' => 'EyAy demo review: sterke aansluiting op volwassen AI-gebruik, duidelijke scope en praktische implementatieaanpak.',
                    'showcased' => true,
                    'rating' => $supplier['rating'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down()
    {
        $slugs = [
            'flowmind-ai',
            'novaautomate',
            'dialogiq',
            'datapilot',
            'assistlayer',
            'legalbotics',
            'retailsense-ai',
            'opsgenie-ai',
        ];

        $businessIds = DB::table('businesses')->whereIn('slug', $slugs)->pluck('id');

        DB::table('reviews')->whereIn('business_id', $businessIds)->delete();
        DB::table('business_category')->whereIn('business_id', $businessIds)->delete();
        DB::table('businesses')->whereIn('id', $businessIds)->delete();
        DB::table('users')->where('email', 'like', '%@eyay.local')->delete();
    }

    private function upsertUser(array $user)
    {
        DB::table('users')->updateOrInsert(
            ['email' => $user['email']],
            [
                'name' => $user['name'],
                'surname' => $user['surname'],
                'country' => $user['country'] ?? 'Nederland',
                'city' => $user['city'] ?? 'Amsterdam',
                'average_rating' => 0,
                'review_count' => 0,
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'type' => $user['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return DB::table('users')->where('email', $user['email'])->value('id');
    }
}
