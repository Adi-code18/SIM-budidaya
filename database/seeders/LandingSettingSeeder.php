<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LandingSettingSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('landing_settings')->truncate();
        Schema::enableForeignKeyConstraints();

        $defaults = LandingSetting::defaults();
        foreach ($defaults as $key => $val) {
            if ($val !== null) {
                LandingSetting::create([
                    'key'   => $key,
                    'value' => (string) $val,
                ]);
            }
        }
    }
}
