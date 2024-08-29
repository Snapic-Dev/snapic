<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NichesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $niches = [
            'Adulto',
            'Viagens',
            'Cozinha',
            'Sensual',
            'Educação',
            'LifeStyle',
            'Moda',
            'Beleza',
            'Outros'
        ];

        foreach ($niches as $niche) {
            DB::table('niches')->updateOrInsert(
                ['name' => $niche],
                ['name' => $niche]
            );
        }
    }
}
