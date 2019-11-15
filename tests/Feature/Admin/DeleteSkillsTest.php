<?php

namespace Tests\Feature\Admin;

use App\Skill;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteSkillsTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Duilio Palacios',
        'email' => 'duilio+7@styde.net',
        'password' => 'laravel',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter/sileence/',
        'role' => 'user'
    ];

    /** @test */
    function it_sends_a_skill_to_the_trash(){
        $skill = factory(Skill::class)->create();

        $this->patch("skill/{$skill->id}/trash")
            ->assertRedirect(route('skill.index'));

        // Option 1
        $this->assertSoftDeleted('skills', [
            'id' => $skill->id
        ]);

        // Option 2:
        $skill->refresh();

        $this->assertTrue($skill->trashed());
    }

    /** @test */
    function it_completely_deletes_a_skill(){
        $skill = factory(Skill::class)->create([
            'deleted_at' => now()
        ]);

        $this->delete("skill/{$skill->id}")
            ->assertRedirect(route('trashed.index'));

        $this->assertDatabaseEmpty('skills');
    }

    /** @test */
    public function a_skill_associated_to_a_users_cannot_be_deleted()
    {
        $this->withExceptionHandling();

        $skill = factory(Skill::class)->create();

        $this->post('/usuarios', $this->withData([
            'skills'=>[
                $skill->id
            ],
        ]));

        $user = User::findByEmail('duilio+7@styde.net');

        $response = $this->patch("skill/{$skill->id}/trash");

        $response->assertStatus(400);

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id
        ]);

        $this->assertDatabaseHas('user_skill', [
            'skill_id' => $skill->id,
            'user_id' => $user->id
        ]);
    }
}
