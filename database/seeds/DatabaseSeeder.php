<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
            'users',
            'user_profiles',
            'user_skill',
            'professions',
            'skills',
        ]);

        $this->call([
            ProfessionSeeder::class,
            SkillSeeder::class,
            UserSeeder::class,
        ]);
    }

    public function truncateTables(array $tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table){
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
