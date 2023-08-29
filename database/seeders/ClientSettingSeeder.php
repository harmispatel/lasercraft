<?php

namespace Database\Seeders;

use App\Models\ClientSettings;
use Illuminate\Database\Seeder;

class ClientSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientSettings = [
            [
                'key'            =>         'default_currency',
                'value'          =>         'INR',
            ],
        ];

        ClientSettings::insert($clientSettings);
    }
}
