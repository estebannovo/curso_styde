<?php

namespace Tests\Feature\Admin;

use App\User;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestoreUsersTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_can_resotre_trashed_user()
    {
        $user = factory(User::class)->create([
            'deleted_at' => now()
        ]);

        factory(UserProfile::class)->create([
            'user_id' => $user->id
        ]);

        $this->get("usuarios/{$user->id}/restore")
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id'=> $user->id,
            'deleted_at' => null
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id'=> $user->id,
            'deleted_at' => null
        ]);
    }
}
