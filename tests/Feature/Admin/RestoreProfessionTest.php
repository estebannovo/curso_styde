<?php

namespace Tests\Feature\Admin;

use App\Profession;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestoreProfessionTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_can_resotre_trashed_professions()
    {
        $profession = factory(Profession::class)->create([
            'deleted_at' => now()
        ]);

        $this->get("profession/{$profession->id}/restore")
            ->assertRedirect(route('profession.index'));

        $this->assertDatabaseHas('professions', [
            'id'=> $profession->id,
            'deleted_at' => null
        ]);
    }
}
