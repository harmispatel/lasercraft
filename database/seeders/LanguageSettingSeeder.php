<?php

namespace Database\Seeders;

use App\Models\LanguageSettings;
use Illuminate\Database\Seeder;

class LanguageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langueg_settings = [
            [
                'key' =>      'primary_language',
                'value' =>      1,
            ],
            [
                'key' =>      'google_translate',
                'value' =>      0,
            ],
        ];

        LanguageSettings::insert($langueg_settings);
    }
}
