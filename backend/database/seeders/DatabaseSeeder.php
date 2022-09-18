<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CodingSystemsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seeders = [
        ];

        foreach ($seeders as $seederClass) {
            $this->call($seederClass);
        }
    }
}
