<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Link;
use App\Models\Order;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $path = 'database/countries.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);

         \App\Models\User::factory()->create([
           'name' => 'Test User',
             'email' => 'test@example.com',
        ]);

        \App\Models\User::factory(10)->create();

       Link::factory(20000)->create();
        Order::factory(500)->create();
    }
}
