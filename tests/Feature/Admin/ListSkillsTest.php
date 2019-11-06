<?php

namespace Tests\Feature\Admin;

use App\Skill;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListSkillsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_skill_list()
    {
        factory(Skill::class)->create(['name' => 'React Native']);

        factory(Skill::class)->create(['name' => 'Angular']);

        factory(Skill::class)->create(['name' => 'Database Administrator']);

        $this->get('/skills/')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Angular',
                'Database Administrator',
                'React Native'
            ])
            ->assertSee('Listado de habilidades');
    }
}
