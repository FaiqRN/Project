<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder{

    public function run(): void{

        $data=[
            ['level_id' => 1, 
            'level_kode' => 'ADM', 
            'level_nama' => 'Administrator'],
            
            ['level_id' => 2, 
            'level_kode' => 'KPR', 
            'level_nama' => 'Kaprodi'],

            ['level_id' => 3, 
            'level_kode' => 'DSN', 
            'level_nama' => 'Dosen'],
        ];
        
        DB::table('m_level')->insert($data);
    }
}
