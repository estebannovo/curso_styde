<?php

namespace Tests\Feature\Admin;

use App\User;
use App\UserProfile;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_sends_a_user_to_the_trash(){
        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'user_id' => $user->id
        ]);

        $this->patch("usuarios/{$user->id}/trash")
            ->assertRedirect('usuarios');

        // Option 1
        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);

        $this->assertSoftDeleted('user_profiles', [
            'user_id' => $user->id
        ]);

        // Option 2:
        $user->refresh();

        $this->assertTrue($user->trashed());
    }

    /** @test */
    function it_completely_deletes_a_user(){
        $user = factory(User::class)->create([
            'deleted_at' => now()
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $user->id
        ]);

        $this->delete("usuarios/{$user->id}")
            ->assertRedirect(route('trashed.index'));

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function it_cannot_delete_a_user_that_is_not_in_the_trash(){
        $this->withExceptionHandling();

        $user = factory(User::class)->create([
            'deleted_at' => null,
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $user->id
        ]);

        $this->delete("usuarios/{$user->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('users', [
            'id'=> $user->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    function it_can_delete_old_users(){
        //Create 50 users trashed 21 days ago
        factory(User::class, 50)->create([
            'deleted_at' => Carbon::now()->subDays(21)
        ])->each(function ($user){
            $user->profile()->create([
                    'bio' => 'Programador',
                    'deleted_at' => Carbon::now()->subDays(21)
                ]
            );
        });

        //Create 10 users trashed now
        factory(User::class, 10)->create([
            'deleted_at' => now()
        ])->each(function ($user){
            $user->profile()->create([
                    'bio' => 'Programador',
                    'deleted_at' => now()
                ]
            );
        });

        //Create 10 active users
        factory(User::class, 10)->create(['deleted_at' => null])->each(function ($user){
            $user->profile()->create(['bio' => 'Programador','deleted_at' => null]
            );
        });

        $this->assertDatabaseCount('users', 70);
        $this->assertDatabaseCount('user_profiles', 70);

        $response = $this->get('/usuarios/destroyOldUsers');
        $response->assertStatus(200);

        $response->assertSeeText("We have deleted 50 users from the trash");

        $this->assertDatabaseCount('users', 20);
        $this->assertDatabaseCount('user_profiles', 20);
    }
}
