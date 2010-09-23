<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(__FILE__) . '/../Benchmark/Benchmark.php';

/**
 * Test class for BenchmarkFunction.
 * Generated by PHPUnit on 2010-09-01 at 22:09:45.
 */
class BenchmarkFunctionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var BenchmarkFunction
     */
    protected $function;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->function = new BenchmarkFunction;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testGetCode()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testInvoke()
    {
        TestHelper::includeDoSomethingFunction();
        $param1 = 1;
        $param2 = array(1, true, 'string');

        global $doSomething_called;
        global $doSomething_arguments;

        self::assertFalse($doSomething_called,
                        'doSomething must be uncalled');
        self::assertTrue(empty($doSomething_arguments),
                        'doSomething must not have gotten any arguments');

        $this->function->setName('doSomething');
        $this->function->setArguments(array($param1, $param2));
        $this->function->invoke();

        self::assertTrue($doSomething_called,
                        'doSomething must have been called');
        self::assertSame(array($param1, $param2), $doSomething_arguments,
                        'doSomething must have gotten some arguments');
    }

    public function testInvoke_ThrowsException()
    {
        try {
            $this->function->invoke();
            self::fail('TargetNotFoundException expected');
        } catch (TargetNotFoundException $ex) {

        }
    }

    public function testSetName_FunctionExists()
    {
        TestHelper::includeDoSomethingFunction();

        $name = 'doSomething';
        $this->function->setName($name);
        self::assertSame($name, $this->function->getName(),
                        'classname must contain new name');
    }

    public function testSetName_FunctionDoesNotExist()
    {
        $name = 'unknownFunction';
        try {
            $this->function->setName($name);
            $self::fail('TargetNotFoundException expected');
        } catch (TargetNotFoundException $ex) {

        }
    }

    public function testGetName_DefaultValue()
    {
        self::assertNull($this->function->getName());
    }

    public function testGetName_ContainsNameSetByConstructor()
    {
        TestHelper::includeDoSomethingFunction();
        $name = 'doSomething';
        $this->function = new BenchmarkFunction($name, array(1, 2));

        self::assertSame($name, $this->function->getName());
    }

    public function testSetArguments()
    {
        $args = array(1, true, 'string', array());
        $this->function->setArguments($args);

        self::assertSame($args, $this->function->getArguments(),
                        'given arguments must be returned');
    }

    public function testSetArguments_AreGivenToFunction()
    {

        TestHelper::includeDoSomethingFunction();

        $args = array(1, true);
        $this->function->setName('doSomething');
        $this->function->setArguments($args);
        $this->function->invoke();

        global $doSomething_arguments;
        self::assertSame($args, $doSomething_arguments);
    }

    public function testSetArguments_EnforceReflectionException() {
        $this->function = $this->getMock('BenchmarkFunction', array('getName'));

        $this->function->expects($this->exactly(2))
                ->method('getName')
                ->will($this->returnValue('unknownFunction'));

        try {
            $this->function->setArguments(array(1,2)); // enforce reflection to be refreshed
            self::fail('argetNotFoundException expected');
        } catch ( TargetNotFoundException $ex ) {
            self::assertType('ReflectionException', $ex->getPrevious());
        }
    }

    public function testGetArguments_DefaultValue()
    {
        $arguments = $this->function->getArguments();
        self::assertTrue(empty($arguments),
                        'by default arguments array must be empty');
    }

    public function testGetArguments_SetByConstructor()
    {
        TestHelper::includeDoSomethingFunction();

        $arguments = array(1, true, array());
        $this->function = new BenchmarkFunction('doSomething', $arguments);

        self::assertSame($arguments, $this->function->getArguments());
    }

    public function testSetDescription()
    {
        $description = 'new description';
        $this->function->setDescription($description);
        self::assertSame($description, $this->function->getDescription(),
                        'description must contain value which was set before');
    }

    public function testSetDescription_InvalidArgument()
    {
        $description = 'description';
        $this->function->setDescription($description); // fill with valid value

        $this->function->setDescription(1); // number
        $this->function->setDescription(true); // boolean
        $this->function->setDescription(array()); // array
        $this->function->setDescription(new stdClass()); // object

        self::assertSame($description, $this->function->getDescription());
    }

    public function testGetDescription_DefaultValue()
    {
        self::assertNull($this->function->getDescription());
    }

    public function testGetDescription_SetByConstructor()
    {
        TestHelper::includeDoSomethingFunction();

        $description = 'new description';
        $this->function = new BenchmarkFunction('doSomething', array(1, 2), $description);

        self::assertSame($description, $this->function->getDescription());
    }

    public function test__toString()
    {
        TestHelper::includeDoSomethingFunction();

        $this->function->setName('doSomething');
        $this->function->setArguments(array(1, 2));

        self::assertSame('doSomething(..)', $this->function->__toString());
    }

    public function testSetPreExecutedTarget() {
        TestHelper::includeDoSomethingFunction();

        global $doSomething2_called;
        global $doSomething2_arguments;

        self::assertFalse($doSomething2_called,
                        'doSomething must be uncalled');
        self::assertTrue(empty($doSomething2_arguments),
                        'doSomething must not have gotten any arguments');

        $args = array('arg1', 'arg2');
        $target2 = new BenchmarkFunction('doSomething2', $args, 'description');
        $this->function->setPreExecutedTarget($target2);

        $this->function->setName('doSomething');
        $this->function->setArguments(array('arg3', 'arg4'));
        $this->function->invoke();

        self::assertTrue($doSomething2_called);
        global $doSomething2_arguments;
        self::assertSame($args, $doSomething2_arguments);
    }

    public function testSetPostExecutedTarget() {
        TestHelper::includeDoSomethingFunction();

        global $doSomething2_called;
        global $doSomething2_arguments;

        self::assertFalse($doSomething2_called,
                        'doSomething must be uncalled');
        self::assertTrue(empty($doSomething2_arguments),
                        'doSomething must not have gotten any arguments');

        $args = array('arg1', 'arg2');
        $target2 = new BenchmarkFunction('doSomething2', $args, 'description');
        $this->function->setPostExecutedTarget($target2);

        $this->function->setName('doSomething');
        $this->function->setArguments(array('arg3', 'arg4'));
        $this->function->invoke();

        self::assertTrue($doSomething2_called);
        global $doSomething2_arguments;
        self::assertSame($args, $doSomething2_arguments);
    }

    /**
     * @expectedException PossibleRecursionException
     */
    public function testSetPreExecutedRecursiveTarget() {
        TestHelper::includeDoSomethingFunction();

        $args = array('arg1', 'arg2');
        $target2 = new BenchmarkFunction('doSomething2', $args, 'description');
        $target2->setPreExecutedTarget($target2);
        $this->function->setPreExecutedTarget($target2);

        $this->function->setName('doSomething');
        $this->function->setArguments(array('arg3', 'arg4'));
        $this->function->invoke();
    }

    /**
     * @expectedException PossibleRecursionException
     */
    public function testSetPostExecutedRecursiveTarget() {
        TestHelper::includeDoSomethingFunction();

        $args = array('arg1', 'arg2');
        $target2 = new BenchmarkFunction('doSomething2', $args, 'description');
        $target2->setPostExecutedTarget($target2);
        $this->function->setPostExecutedTarget($target2);

        $this->function->setName('doSomething');
        $this->function->setArguments(array('arg3', 'arg4'));
        $this->function->invoke();
    }
}