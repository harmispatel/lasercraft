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
                'firstname' =>      'Admin',
                'lastname' =>      'User',
                'email'     =>      'admin@gmail.com',
                'user_type' =>      2,
                'password'  =>      Hash::make(123456),
                'status'    =>      1,
            ],
        ];

        LanguageSettings::insert($langueg_settings);
    }
}
