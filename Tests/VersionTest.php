<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../Benchmark/Version.php';

/**
 * Test class for Version.
 * Generated by PHPUnit on 2010-09-24 at 15:20:43.
 */
class VersionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Version
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Version;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @todo Implement testId().
     */
    public function testId()
    {
        self::assertRegExp('/^\d+\.\d(\w+\d?)?$/', Version::id());
    }

    /**
     * @todo Implement testGetVersionString().
     */
    public function testGetVersionString()
    {
        $version_string = Version::getVersionString();

        self::assertContains('Tom Tomsen', $version_string,
            'version string does not contain author');
        self::assertContains('@', $version_string,
            'version string does not contain email address');
        self::assertRegExp('/^.*\d+\.\d(\w+\d?)?.*$/', Version::id());
    }

}

?>
