<?php

namespace Tests\Browser\Admin;

use App\Profession;
use App\Skill;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_be_created()
    {
        $profession = factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $userName = 'Duilio';
        $userEmail = 'duilio@styde.net';
        $userPassword = 'laravel';
        $userBiography = 'Programador';
        $userTwitterAccount = 'https://twitter.com/sileence';

        $this->browse(function(Browser $browser) use ($profession, $skillA, $skillB, $userName, $userEmail, $userPassword, $userBiography, $userTwitterAccount) {
            $browser->visit('/usuarios/nuevo')
                ->type('name', $userName)
                ->type('email', $userEmail)
                ->type('password', $userPassword)
                ->type('bio', $userBiography)
                ->select('profession_id', $profession->id)
                ->type('twitter', $userTwitterAccount)
                ->check("skills[{$skillA->id}]")
                ->check("skills[{$skillB->id}]")
                //->radio('role', 'user')
                ->press('Crear usuario')
                ->assertPathIs('/usuarios')
                ->assertSee($userName)
                ->assertSee($userEmail);
        });

        $this->assertCredentials([
            'name'=> $userName,
            'email'=> $userEmail,
            'password' => $userPassword,
            'role' => 'user',
        ]);

        $user = User::findByEmail($userEmail);
        $this->assertDatabaseHas('user_profiles', [
            'bio' => $userBiography,
            'twitter' => $userTwitterAccount,
            'user_id' => $user->id,
            'profession_id' => $profession->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]);
    }
}
