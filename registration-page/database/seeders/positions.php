<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class positions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Position::insert([
            ['position' => 'Lawyer'],
            ['position' => 'Content manager'],
            ['position' => 'Security'],
            ['position' => 'Designer'],
        ]);
    }
}
