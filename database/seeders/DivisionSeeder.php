<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Office of the General Manager',  'code' => 'OGM'],
            ['name' => 'Administrative Division',         'code' => 'ADMIN'],
            ['name' => 'Accounting Division',                'code' => 'ACC'],
            ['name' => 'Engineering Division',            'code' => 'ENG'],
            ['name' => 'Commercial Division',             'code' => 'COM'],
            ['name' => 'ICT Division',                    'code' => 'ICT'],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(['code' => $division['code']], $division);
        }
    }
}
