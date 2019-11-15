<?php

namespace Tests\Feature\Admin;

use App\Skill;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestoreSkillTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_can_resotre_trashed_skill()
    {
        $skill = factory(Skill::class)->create([
            'deleted_at' => now()
        ]);

        $this->get("skill/{$skill->id}/restore")
            ->assertRedirect(route('skill.index'));

        $this->assertDatabaseHas('skills', [
            'id'=> $skill->id,
            'deleted_at' => null
        ]);
    }
}
