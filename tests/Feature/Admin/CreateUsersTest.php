<?php

namespace Tests\Feature\Admin;

use App\{Profession, Skill, User};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Esteban Novo',
        'email' => 'novo.esteban@gmail.com',
        'password' => 'laravel',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter/estebannovo',
        'role' => 'user'
    ];

    /** @test */
    function  it_loads_the_new_users_page()
    {
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
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();

        $this->post('/usuarios', $this->withData([
            'skills'=>[
                $skillA->id, $skillB->id
            ],
        ]))->assertRedirect(route('users.index'));

        // ->assertSee('Procesando información...');

        $this->assertDatabaseHas('users', [
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com'
        ]);

        $this->assertCredentials([
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel',
            'role' => 'user',
        ]);

        $user = User::findByEmail('novo.esteban@gmail.com');
        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter/estebannovo',
            'user_id' => $user->id,
            'profession_id' => $this->profession->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]);

        $this->assertDatabaseMissing('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillC->id,
        ]);
    }

    /** @test */
    function the_twitter_field_is_optional(){
        $this->post('/usuarios', $this->withData([
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
    function the_role_field_is_optional(){
        $this->withExceptionHandling();

        $this->post('/usuarios', $this->withData([
            'role' => null
        ]))->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email'=> 'novo.esteban@gmail.com',
            'role'=> 'user'
        ]);
    }

    /** @test */
    function the_role_must_be_valid(){
        $this->handleValidationExceptions();

        $this->post('/usuarios', $this->withData([
            'role' => 'invalid-role'
        ]))->assertSessionHasErrors('role');

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_profession_id_is_absent_but_another_profession_is_passed(){
        $this->post('/usuarios', $this->withData([
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
        $this->post('/usuarios', $this->withData([
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
        $this->handleValidationExceptions();

        $this->post('/usuarios', $this->withData([
                'name' => ''
            ]))
            ->assertSessionHasErrors(['name' => 'The field name is required']);

        /*$this->assertDatabaseMissing('users',[
            'email'=> 'novo.esteban@gmail.com'
        ]);*/

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_user_is_redirected_to_the_previous_page_when_the_validation_fails(){
        $this->handleValidationExceptions();

        $this->post('/usuarios', []);
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required(){
        $this->handleValidationExceptions();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->withData([
                'email' => ''
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email' => 'The field email is required']);

        $this->assertEquals(0, User::count());
    }


    /** @test */
    function the_password_is_required(){
        $this->handleValidationExceptions();

        $this->post('/usuarios', $this->withData([
                'password' => ''
            ]))
            ->assertSessionHasErrors(['password' => 'The field password is required']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid(){
        $this->handleValidationExceptions();

        $this->post('/usuarios', $this->withData([
                'email'=> 'correo-no-valido'
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_profession_must_be_valid(){
        $this->handleValidationExceptions();

        $this->post('/usuarios', $this->withData([
                'profession_id' => '999'
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_selectable_profession_are_valid(){
        $this->handleValidationExceptions();

        $nonSelectableProfession = factory(Profession::class)->create([
            'selectable' => false
        ]);

        $this->post('/usuarios', $this->withData([
                'profession_id' => $nonSelectableProfession->id,
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_an_array(){
        $this->handleValidationExceptions();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->withData([
                'skills' => 'PHP, JS, CCS'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_valid(){
        $this->handleValidationExceptions();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this
            ->from('usuarios/nuevo')
            ->post('/usuarios', $this->withData([
                'skills' => $skillA->id, $skillB->id + 1
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }


//    /** @test */
//    function only_not_deleted_professions_can_be_selected(){
//        //$this->handleValidationExceptions();
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
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email'=> 'novo.esteban@gmail.com',
        ]);

        $this->post('/usuarios', $this->withData([
                'email'=> 'novo.esteban@gmail.com'
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_must_be_at_least_six_characters(){
        $this->handleValidationExceptions();

        $this->post('/usuarios', [
                'name'=> 'Esteban Novo',
                'email'=> 'novo.esteban@gmail.com',
                'password' => 'lar'
            ])
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0, User::count());
    }

}