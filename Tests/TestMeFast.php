<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/estHelper.php';
require_once dirname(__FILE__) . '/../Benchmark/Benchmark.php';

class TestMeFast extends PHPUnit_Framework_TestCase {

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->benchmark = new Benchmark('benchmark-title',
                        'benchmark-description');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function test_SERVER()
    {
        var_dump($_SERVER);
        self::assertTrue(true);
    }
}