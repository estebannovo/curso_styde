<?php
use App\Profession;
use App\{Skill, User, UserProfile, Team};
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    protected $professions;
    protected $skills;
    protected $teams;

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

        //$professionId = Profession::where('title','Desarrollador Back-End')->value('id');

        $this->fetchRelations();
        $this->createAdmin();


        //Creamos un usuario con una Biography custom y profession_id random
        (factory(User::class)->create())->profile()->create([
            'bio'=> 'Programador',
            'profession_id' => Profession::all()->random()->id,
        ]);

        //Creamos un usuario con profile dinámicamente, todo usando las factories con $faker
        (factory(User::class)->create())->profile()->create(
            factory(UserProfile::class)->raw()
        );

        //Aquí geramos un usuario sin profile
        //factory(User::class)->create();

//        //De esta forma generabamos los usuarios cuando el profession_id estaba en la tabla user
//        factory(User::class, 48)->create([
//            'profession_id' => Profession::all()->random()->id
//        ]);

        //Creamos 46 usuarios y un perfil para cada uno, el perfil se genera también con su factory (UserProfileFactory.php) que usa $faker para generar datos dinámicamente
        foreach (range(1, 999) as $i) {
            $this->createRandomUsers();
        }

//        factory(User::class, 996)->create()->each(function ($user) use($professions, $skills) {
//        });
    }

    /**
     * @return array
     */
    protected function fetchRelations()
    {
        $this->professions = Profession::all();
        $this->skills = Skill::all();
        $this->teams = Team::all();
    }

    /**
     * @return mixed
     */
    protected function createAdmin()
    {
        //Generamos un usuario con datos Custom y lo guardamos en la variable $user
        $admin = factory(User::class)->create([
            'team_id' => $this->teams->firstWhere('name', 'Styde'),
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => bcrypt('laravel'),
            'role' => 'admin',
            'created_at' => now()->addDay(),
        ]);

        $admin->skills()->attach($this->skills);

        //Usamos la variable $user para llamar a la funcion profile y crear/asignar un profile custom al usuario
        $admin->profile()->create([
            'bio' => 'Programador, Profesor, editor, escritor, social media manager',
            'profession_id' => $this->professions->firstWhere('title', 'Desarrollador Back-End')->id,
        ]);
    }

    protected function createRandomUsers(): void
    {
        $user = factory(User::class)->create([
            'team_id' => rand(0, 2) ? null : $this->teams->random()->id,
        ]);
        $user->skills()->attach($this->skills->random(rand(0, 7)));

        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);
    }
}