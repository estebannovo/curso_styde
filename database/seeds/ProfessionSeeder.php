<?php

use App\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::insert('INSERT INTO professions (title) VALUES (:title)', ['title'=> 'Desarrollador back-end']);

        /*DB::table('professions')->insert([
            'title' => 'Desarrollador Back-End'
        ]);

        DB::table('professions')->insert([
            'title' => 'Desarrollador front-end'
        ]);

        DB::table('professions')->insert([
            'title' => 'Diseñador Web'
        ]);
        */

        Profession::create([
            'title' => 'Desarrollador Back-End'
        ]);

        Profession::create([
            'title' => 'Desarrollador front-end'
        ]);

        Profession::create([
            'title' => 'Diseñador Web'
        ]);

    }
}
