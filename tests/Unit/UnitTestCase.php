<?php

namespace Uniqoders\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Uniqoders\Game\Config\Config;

class UnitTestCase extends TestCase
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->application = new Application();
    }

    public function test_config_weapons_normal()
    {
        $commandTester = new Config();

        $result = $commandTester->weapons('Normal');
        $array = [
            0 => 'Tijeras',
            1 => 'Piedra',
            2 => 'Papel'
        ];
        $this->assertEquals($result, $array);
    }

    public function test_config_weapons_big_bang_theory()
    {
        $commandTester = new Config();

        $result = $commandTester->weapons('Big Bang Teory');
        $array = [
            0 => 'Tijeras',
            1 => 'Piedra',
            2 => 'Papel',
            3 => 'Lagarto',
            4 => 'Spok',
        ];
        $this->assertContains('Piedra', $array);
    }

    public function test_config_rules_normal()
    {
        $commandTester = new Config();

        $result = $commandTester->rules('Normal');
        $array = [
            0 => [2],
            1 => [0],
            2 => [1],
        ];
        $this->assertSame($result, $array);
    }

    public function test_config_rules_big_bang_theory()
    {
        $commandTester = new Config();

        $result = $commandTester->rules('Big Bang Teory');
        $array = [
            0 => [2, 3],
            1 => [0, 3],
            2 => [1, 4],
            3 => [2, 4],
            4 => [0, 1],
        ];
        $this->assertContains([1, 4], $array);
    }
}
