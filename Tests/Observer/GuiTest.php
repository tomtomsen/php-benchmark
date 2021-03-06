<?php

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once dirname(__FILE__) . '/../../Benchmark/Benchmark.php';

/**
 * Test class for Gui.
 * Generated by PHPUnit on 2010-08-28 at 18:31:55.
 */
class GuiTest extends PHPUnit_Extensions_OutputTestCase
{

    /**
     * @var Gui
     */
    protected $gui;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->gui = new Gui();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testUpdate_WithTargets() {
        $_SERVER['SERVER_SOFTWARE'] = 'Test';
        $benchmark = new Benchmark('title');

        require_once dirname(__FILE__) . '/../Helper/ComplexClass.php';
        $description = 'some description';
        $benchmark->addTarget(new BenchmarkMethod('ComplexClass', array(1,2), 'doSomething', array('param1', 'param2'), $description));

        $benchmark->setIterations(2);
        $benchmark->setGui($this->gui);
        $benchmark->run();

        $this->expectOutputRegex('/<html>.*' . $description . '.*<\/html>/si');
    }

    public function testUpdate_InvalidArguments() {
        self::assertFalse($this->gui->update(new TemporaryObservable()));
    }

    public function testUpdate_InvalidState() {
        try {
            $benchmark = $this->getMock('Benchmark', array('getState'), array('title'));

            $benchmark->expects($this->once())
                      ->method('getState')
                      ->will($this->returnValue(999));

            $this->gui->update($benchmark);
            self::fail('InvalidStateException excepted');
        } catch ( InvalidStateException $ex ) {}
    }
}

class TemporaryObservable  {

    protected function notify()
    {
        ;
    }
}