<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = ['German' => 'de', 'English' => 'en',  'French' => 'fr'];
        foreach ($languages as $language => $code) {
            Language::create(['name' => $language, 'code' => $code]);
        }
    }
}
