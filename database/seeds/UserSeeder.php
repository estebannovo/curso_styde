<?php

use App\Profession;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$professions = DB::select('SELECT  id FROM professions WHERE title=? LIMIT 1', ['Desarrollador Back-End']);

        /*$professionId = DB::table('professions')
            ->whereTitle('Diseñador Web')
            ->value('id');*/

        /*DB::table('users')->insert([
            'name' => 'Duilio Palacios',
            'email' => 'dulio@styde.net',
            'password' =>  bcrypt('laravel'),
            'profession_id' => $professionId,
        ]);*/

        $professionId = Profession::where('title','Desarrollador Back-End')->value('id');

        User::create([
            'name' => 'Duilio Palacios',
            'email' => 'dulio@styde.net',
            'password' =>  bcrypt('laravel'),
            'profession_id' => $professionId,
            'is_admin' => true
        ]);

        User::create([
            'name' => 'Another User',
            'email' => 'another@styde.net',
            'password' =>  bcrypt('laravel'),
            'profession_id' =>  $professionId //Profession::where('title','Diseñador Web')->value('id'),
        ]);


        User::create([
            'name' => 'Esteban Novo',
            'email' => 'novo.esteban@gmail.com',
            'password' =>  bcrypt('laravel'),
            'profession_id' => null,
        ]);
    }
}
