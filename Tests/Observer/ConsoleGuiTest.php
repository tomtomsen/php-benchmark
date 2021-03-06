<?php

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once dirname(__FILE__) . '/../../Benchmark/Benchmark.php';
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Test class for ConsoleGui.
 * Generated by PHPUnit on 2010-10-02 at 14:55:13.
 */
class ConsoleGuiTest extends PHPUnit_Extensions_OutputTestCase
{

    /**
     * @var ConsoleGui
     */
    protected $console;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->console = new ConsoleGui;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testOutput()
    {
        TestHelper::includeDoSomethingFunction();

        $bm = new Benchmark('title');
        $bm->setGui($this->console);

        $bm->addTarget(new BenchmarkFunction('doSomething', array(1,2)));

        $bm->setIterations(9);

        $bm->run();

        $this->assertOutputContains('Iterations.*9');
        $this->assertOutputContains('Running');
        $this->assertOutputContains('Results');
        $this->assertOutputContains('Factor');
        $this->assertOutputContains('php benchmark');
        $this->assertOutputContains('tom tomsen');
        $this->assertOutputContains('@');
    }

    protected function assertOutputContains($str)
    {
        $this->expectOutputRegex('/.*' . $str . '.*/si');
    }
}