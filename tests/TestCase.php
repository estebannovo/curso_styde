<?php

namespace Tests;

use App\Profession;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TestHelpers;

    protected $profession;
    protected $defaultData = [];

    protected function setUp()
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }
}
