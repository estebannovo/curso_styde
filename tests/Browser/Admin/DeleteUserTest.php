<?php

namespace Tests\Browser\Admin;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\User\UserHelper;

class DeleteUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    use UserHelper;

    /**
     * @test
     */
    public function it_can_delete_a_user()
    {
        //First of all I have to create a new user to try to delete it.
        $this->loadNewUser();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => $this->userData['bio'],
            'twitter' => $this->userData['twitter'],
            'user_id' => $this->user->id,
            'profession_id' => $this->profession->id,
        ]);

        $this->browse(function (Browser $browser){
            $browser->visit('/usuarios')
                ->assertSee($this->userData['name'])
                ->assertSee($this->userData['email'])
                ->press("@delete-{$this->user->id}")
                ->assertPathIs('/usuarios')
                ->assertDontSee($this->userData['name'])
                ->assertDontSee($this->userData['email']);
        });

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
        ]);

        $this->assertDatabaseMissing('user_profiles', [
            'bio' => $this->userData['bio'],
            'twitter' => $this->userData['twitter'],
            'user_id' => $this->user->id,
            'profession_id' => $this->profession->id,
        ]);
    }
}
