<?php

namespace Tests\Feature;

use App\Profession;
use App\Skill;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Throwable;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $profession;

    /** @test */
    function it_shows_the_users_list()
    {
        factory(Profession::class)->times(5)->create();

        factory(User::class)->create([
            'name' => 'Joel',
            //'profession_id' => Profession::all()->random()->id
        ]);

        factory(User::class)->create([
            'name'=>'Ellie',
            //'profession_id' => Profession::all()->random()->id
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function it_shows_a_defaut_message_if_the_users_list_is_empty()
    {
        //DB::table('users')->truncate();

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados');
    }

    /** @test */
    function it_displays_the_users_details()
    {
        $user = factory(User::class)->create([
            'name' => 'Marcelo Gularte',
        ]);

        $this->get('/usuarios/'.$user->id)
            ->assertStatus(200)
            ->assertSee('Marcelo Gularte');
            //->assertSee('Mostrando detalle  del usuario: '.$user->id);
    }

    /** @test */
    function it_display_a_404_error_if_the_user_is_not_found(){
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }

    /** @test */
    function  it_loads_the_new_users_page()
    {
        $this->withoutExceptionHandling();

        $profession = factory(Profession::class)->create();

        $skillsA = factory(Skill::class)->create();
        $skillsB = factory(Skill::class)->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function ($professions) use($profession){
                return $professions->contains($profession);
            })
            ->assertViewHas('skills', function ($skills) use ($skillsA, $skillsB){
                return $skills->contains($skillsA) && $skills->contains($skillsB);
            });
    }

    /** @test */
    function it_creates_a_new_user(){
        $this->withoutExceptionHandling();

        $this->post('/usuarios', $this->getValidData())->assertRedirect(route('users.index'));

           // ->assertSee('Procesando información...');

        $this->assertDatabaseHas('users', [
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com'
        ]);

        $this->assertCredentials([
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter/estebannovo',
            'user_id' => User::findByEmail('novo.esteban@gmail.com')->id,
            'profession_id' => $this->profession->id,
        ]);
    }

    /** @test */
    function the_twitter_field_is_optional(){
        $this->withoutExceptionHandling();
        $this->post('/usuarios', $this->getValidData([
            'twitter' => null
        ]))->assertRedirect(route('users.index'));

        // ->assertSee('Procesando información...');

        $this->assertDatabaseHas('users', [
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com'
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => null,
            'user_id' => User::findByEmail('novo.esteban@gmail.com')->id
        ]);

        $this->assertCredentials([
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'
        ]);
    }

    /** @test */
    function the_profession_id_is_absent_but_another_profession_is_passed(){
        //$this->withoutExceptionHandling();
        $this->post('/usuarios', $this->getValidData([
            'profession_id' => null,
            'other_profession' => 'new profession',
        ]))->assertRedirect(route('users.index'));

        // ->assertSee('Procesando información...');

        //Obtenemos la nueva profession (Other Profession) que fue insertada al no pasar el profession_id
        $new_profession_id = Profession::where('title','new profession')->orderBy('id', 'DESC')->get()->last()->id;

        $this->assertDatabaseHas('users', [
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'user_id' => User::findByEmail('novo.esteban@gmail.com')->id,
            'profession_id' => $new_profession_id,
        ]);

        $this->assertCredentials([
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'
        ]);
    }

    /** @test */
    function the_other_profession_is_absent_but_profession_id_is_passed(){
        $this->post('/usuarios', $this->getValidData([
            'other_profession' => null,
        ]))->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'user_id' => User::findByEmail('novo.esteban@gmail.com')->id,
            'profession_id' => $this->profession->id,
        ]);
    }

    /** @test */
    function the_name_is_required(){
        //$this->withoutExceptionHandling();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'name' => ''
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'The field name is required']);

        /*$this->assertDatabaseMissing('users',[
            'email'=> 'novo.esteban@gmail.com'
        ]);*/

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required(){
        //$this->withoutExceptionHandling();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'email' => ''
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email' => 'The field email is required']);

        $this->assertEquals(0, User::count());
    }


    /** @test */
    function the_password_is_required(){
        //$this->withoutExceptionHandling();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'password' => ''
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password' => 'The field password is required']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid(){
        //$this->withoutExceptionHandling();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'email'=> 'correo-no-valido'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_profession_must_be_valid(){
        $this->handleValidationExceptions();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'profession_id' => '999'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_selectable_profession_are_valid(){
        $this->handleValidationExceptions();

        $nonSelectableProfession = factory(Profession::class)->create([
            'selectable' => false
        ]);

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'profession_id' => $nonSelectableProfession->id,
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

//    /** @test */
//    function only_not_deleted_professions_can_be_selected(){
//        //$this->handleValidationExceptions();
//        $this->withoutExceptionHandling();
//
//        $nonSelectableProfession = factory(Profession::class)->create([
//            'deleted_at' => now()->format('Y-m-d'),
//        ]);
//
//        $this
//            ->from('usuarios/nuevo')
//            ->post('/usuarios', $this->getValidData([
//                'profession_id' => $nonSelectableProfession->id,
//            ]))
//            ->assertRedirect('usuarios/nuevo')
//            ->assertSessionHasErrors(['profession_id']);
//
//        $this->assertDatabaseEmpty('users');
//    }

    /** @test */
    function the_email_must_be_unique(){
        //$this->withoutExceptionHandling();

        factory(User::class)->create([
            'email'=> 'novo.esteban@gmail.com',
        ]);

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'email'=> 'novo.esteban@gmail.com'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_must_be_at_least_six_characters(){
        //$this->withoutExceptionHandling();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', [
                'name'=> 'Esteban Novo',
                'email'=> 'novo.esteban@gmail.com',
                'password' => 'lar'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function  it_loads_the_edit_user_page()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            /*'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'*/
        ]);

        $this->get("/usuarios/{$user->id}/editar")
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function($viewUser) use ($user){
                return $viewUser->id == $user->id;
            });
    }

    /** @test */
    function  it_updates_a_user()
    {
        $user = factory(User::class)->create();

        //$this->withoutExceptionHandling();

        $this->put("/usuarios/{$user->id}", [
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'
        ])->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'
        ]);
    }

    /** @test */
    function  the_name_is_required_when_updating_a_user()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
            'name'=> '',
            'email'=> 'novo.esteban+2@gmail.com',
            'password' => 'laravel'
        ])
        ->assertRedirect("usuarios/{$user->id}/editar")
        ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email'=>'novo.esteban+2@gmail.com']);
    }

    /** @test */
    function the_email_is_required_when_updating_a_user(){
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 2',
                'email'=> '',
                'password' => 'laravel'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2']);
    }

    /** @test */
    function the_password_is_optional_when_updating_a_user(){
        $oldPassword = 'CLAVE_ANTERIOR';
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword),
        ]);

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 2',
                'email'=> 'novo.esteban+3@gmail.com',
                'password' => '',
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (user.show)
            //->assertSessionHasErrors(['password']);

        //$this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2', 'email'=>'novo.esteban+3@gmail.com']);
        $this->assertCredentials( [
            'name'=>'Esteban Novo 2',
            'email'=>'novo.esteban+3@gmail.com',
            'password' => $oldPassword, //Very important
        ]);
    }

    /** @test */
    function the_users_email_can_stay_the_same_when_updating_the_user(){
        $user = factory(User::class)->create();
        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 5',
                'email'=> 'novo.esteban+5@gmail.com',
                'password' => '1234535698',
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (user.show)
        //->assertSessionHasErrors(['password']);

        $this->assertDatabaseHas('users',[
            'name'=>'Esteban Novo 5',
            'email'=>'novo.esteban+5@gmail.com'
        ]);
    }

    /** @test */
    function the_email_must_be_valid_when_updating_a_user(){
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 2',
                'email'=> 'correo-no-valido',
                'password' => 'laravel'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2']);
    }

    /** @test */
    function the_email_must_be_unique_when_updating_a_user(){
        //$this->withoutExceptionHandling();

        factory(User::class)->create([
            'email'=> 'existing-email@example.com'
        ]);

        $user = factory(User::class)->create([
            'email'=> 'novo.esteban+3@gmail.com'
        ]);


        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 2',
                'email'=> 'existing-email@example.com',
                'password' => 'laravel'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

//        $this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2']);
    }
    /** @test */
    function it_deletes_a_user(){
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->delete("usuarios/{$user->id}")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);

        //$this->assertSame(0, User::count());
    }

    /**
     * @return array
     */
    protected function getValidData(array $custom = []): array
    {
        $this->profession = factory(Profession::class)->create();

        //dd($this->profession);

        /*return array_filter(array_merge([
            'name' => 'Esteban Novo',
            'email' => 'novo.esteban@gmail.com',
            'password' => 'laravel',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter/estebannovo'
        ], $custom));*/

        //Quitamos el array_filter para que no se eliminen las llaves con valor null ya que en la validación se indica que el campo debe estar presente
        return  array_merge([
            'name' => 'Esteban Novo',
            'email' => 'novo.esteban@gmail.com',
            'password' => 'laravel',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter/estebannovo'
        ], $custom);
    }

    /** @test */
    /*function the_password_must_be_at_least_six_characters_when_updating_a_user(){
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 2',
                'email'=> 'novo.esteban+3@gmail.com',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}/editar");
            //->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', ['email'=>'novo.esteban+3@gmail.com']);
    }*/


}
