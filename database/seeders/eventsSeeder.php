<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class eventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("events")->insert([
            [
                'name' => "Konser Musik",
                'category' => "Musik",
                'price' => 50000,
                'description' => "Konser musik yang menampilkan band-band terkenal.",
                'image' => null,
                'created_at'=> now(),
                "updated_at"=> now(),
            ],
            [
                'name' => "Festival Seni",
                'category' => "Festival",
                'price' => 75000,
                'description' => "Festival seni dengan berbagai pertunjukan dan pameran.",
                'image' => null,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => "Seminar Teknologi",
                'category' => "Seminar",
                'price' => 100000,
                'description' => "Seminar teknologi dengan pembicara ahli di bidangnya.",
                'image' => null,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => "Konser Jazz",
                'category' => "Musik",
                'price' => 60000,
                'description' => "Konser jazz dengan musisi terkenal.",
                'image' => null,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => "Festival Film",
                'category' => "Festival",
                'price' => 80000,
                'description' => "Festival film dengan pemutaran film-film terbaik.",
                'image' => null,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => "Seminar Kewirausahaan",
                'category' => "Seminar",
                'price' => 120000,
                'description' => "Seminar kewirausahaan dengan pembicara sukses.",
                'image' => null,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ]);
    }
}
