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
    public function it_deletes_a_skill()
    {
        $skill = factory(Skill::class)->create();

        $response = $this->delete("skills/{$skill->id}");

        $response->assertRedirect();

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

        $response = $this->delete("skills/{$skill->id}");

        $response->assertStatus(400);

        $this->assertDatabaseHas('user_skill', [
            'skill_id' => $skill->id,
            'user_id' => $user->id
        ]);
    }
}
