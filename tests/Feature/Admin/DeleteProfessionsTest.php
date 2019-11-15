<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteProfessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_sends_a_profession_to_the_trash(){
        $profession = factory(Profession::class)->create();

        $this->patch("profession/{$profession->id}/trash")
            ->assertRedirect('professions');

        // Option 1
        $this->assertSoftDeleted('professions', [
            'id' => $profession->id
        ]);

        // Option 2:
        $profession->refresh();

        $this->assertTrue($profession->trashed());
    }

    /** @test */
    function it_completely_deletes_a_profession(){
        $profession = factory(Profession::class)->create([
            'deleted_at' => now()
        ]);

        $this->delete("profession/{$profession->id}")
            ->assertRedirect(route('trashed.index'));

        $this->assertDatabaseEmpty('professions');
    }

    /** @test */
    public function a_profession_associated_to_a_profile_cannot_be_deleted()
    {
        $this->withExceptionHandling();

        $profession = factory(Profession::class)->create();

        $profile = factory(UserProfile::class)->create([
            'profession_id' => $profession->id
        ]);

        $response = $this->patch("profession/{$profession->id}/trash");

        $response->assertStatus(400);

        $this->assertDatabaseHas('professions', [
            'id' => $profession->id
        ]);
    }
}
