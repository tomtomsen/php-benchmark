<?php

/**
 * php-benchmark
 *
 * Copyright (c) 2002-2010, Tom Tomsen <tom.tomsen@inbox.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the University nor the names of its
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5
 *
 * @category  Tool
 * @package   php-benchmark
 * @author    Tom Tomsen <tom.tomsen@inbox.com>
 * @copyright 2010 Tom Tomsen <tom.tomsen@inbox.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://github.com/tomtomsen/benchmark
 * @since     File available since Release 1.0.0
 */

/**
 * Method Target
 *
 * @category  Tool
 * @package   php-benchmark
 * @author    Tom Tomsen <tom.tomsen@inbox.com>
 * @copyright 2010 Tom Tomsen <tom.tomsen@inbox.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: 1.0.0
 * @link      http://github.com/tomtomsen/benchmark
 * @since     Class available since Release 1.0.0
 */
class Method implements ITarget {

    /**
     * class name
     *
     * @var string
     */
    protected $class_name;
    /**
     * arguments of the constructor
     *
     * @var array
     */
    protected $constructor_arguments;
    /**
     * method name
     *
     * @var string
     */
    protected $method_name;
    /**
     * arguments of the method
     *
     * @var array
     */
    protected $method_arguments;
    /**
     * Description
     *
     * @var string
     */
    protected $description;
    /**
     * class object
     *
     * @var object
     */
    protected $class_obj;
    /**
     * pre-executed target
     *
     * @var ITarget
     */
    protected $pre_executed_target;
    /**
     * post-executed target
     *
     * @var ITarget
     */
    protected $post_executed_target;
    /**
     * reflection object
     *
     * @var ReflectionMethod
     */
    private $_reflection;
    /**
     * unique id
     *
     * @var string
     */
    private $_unique_id;

    /**
     * Constructor
     *
     * @param string $class_name            class name
     * @param array  $constructor_arguments arguments of class constructor
     * @param string $method_name           method name
     * @param array  $method_arguments      arguments of method
     * @param string $description           description
     */
    public function __construct(
    $class_name = '', $constructor_arguments = array(), $method_name = '', $method_arguments = array(), $description = ''
    ) {
        $this->class_name = null;
        $this->method_name = null;
        $this->constructor_arguments = array();
        $this->method_arguments = array();
        $this->description = null;
        $this->pre_executed_target = null;
        $this->post_executed_target = null;

        $this->_reflection = null;
        $this->_unique_id = null;

        $this->setClassName($class_name, $constructor_arguments);
        $this->setName($method_name);
        $this->setArguments($method_arguments);
        $this->setDescription($description);
    }

    /**
     * Returns the class as ?unique? string
     *
     * @return string
     */
    public function __toString() {
        $args = $this->getArguments();
        $arg_output = (!empty($args) ? '..' : '');
        $class_name = $this->getClassName();
        $method_name = $this->getName();

        return $class_name . '::' . $method_name . '(' . $arg_output . ')';
    }

    /**
     * Invokes the method call
     *
     * @return mixed result of the method
     */
    public function invoke() {
        if (!isset($this->_reflection) || !isset($this->class_obj)) {
            $class_name = $this->getClassName();
            if (!isset($class_name)) {
                throw new TargetNotFoundException('class not set');
            }
            $method_name = $this->getName();
            if (!isset($method_name)) {
                throw new TargetNotFoundException('method not set');
            }

            $exception_msg = $this->__toString() . ' not found';
            throw new TargetNotFoundException($exception_msg);
        }

        if ( isset($this->pre_executed_target) ) {
            $this->pre_executed_target->invoke();
        }

        $result =  $this->_reflection->invokeArgs(
                $this->class_obj,
                $this->getArguments()
        );

        if ( isset($this->post_executed_target) ) {
            $this->post_executed_target->invoke();
        }

        return $result;
    }

    /**
     * Sets the pre-executed target
     *
     * @param ITarget $target Target
     *
     * @returns Method
     */
    public function setPreExecutedTarget(ITarget $target) {
        if ( $target === $this ) {
            throw new PossibleRecursionException('Recursion detected');
        }
        $this->pre_executed_target = $target;

        return $this;
    }

    /**
     * Sets the post-executed target
     *
     * @param ITarget $target Target
     *
     * @returns Method
     */
    public function setPostExecutedTarget(ITarget $target) {
        if ( $target === $this ) {
            throw new PossibleRecursionException('Recursion detected');
        }
        $this->post_executed_target = $target;

        return $this;
    }

    /**
     * Sets the class object
     *
     * @param object $class an object
     *
     * @return Method
     */
    public function setClass($class) {
        if (isset($class) && is_object($class)) {
            $this->class_obj = $class;
            $this->class_name = get_class($class);

            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns the class object
     *
     * @return object
     */
    public function getClass() {
        return $this->class_obj;
    }

    /**
     * Sets classname
     *
     * @param string $class_name       class name
     * @param array  $constructor_args arguments for the constructor
     * 
     * @return Method
     */
    public function setClassName($class_name, array $constructor_args = null) {
        if (is_string($class_name) && strlen($class_name) > 0) {
            if (!class_exists($class_name)) {
                $msg = sprintf(
                                'could not find class with classname %s',
                                $class_name
                );
                throw new TargetNotFoundException($msg);
            }

            $this->class_name = $class_name;
            if (isset($constructor_args)) {
                $this->setConstructorArguments($constructor_args);
            }

            $this->_refreshClassObject();
            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns the classname
     *
     * @return string
     */
    public function getClassName() {
        return $this->class_name;
    }

    /**
     * Sets methodname
     *
     * @param string $name method name
     * 
     * @return Method
     */
    public function setName($name) {
        if (is_string($name) && strlen($name) > 0) {
            $this->method_name = $name;

            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns methodname
     *
     * @return string
     */
    public function getName() {
        return $this->method_name;
    }

    /**
     * Sets the arguments of the class constructor
     *
     * @param array $args constructor arguments
     *
     * @return Method
     */
    protected function setConstructorArguments(array $args) {
        if (is_array($args)) {
            $this->constructor_arguments = $args;
            $this->_refreshClassObject();
            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns the arguments of the class constructor
     *
     * @return array
     */
    public function getConstructorArguments() {
        return $this->constructor_arguments;
    }

    /**
     * Sets the arguments of the method
     *
     * @param array $args method arguments
     *
     * @return Method
     */
    public function setArguments(array $args) {
        if (is_array($args)) {
            $this->method_arguments = $args;
            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns the arguments of the method
     *
     * @return array
     */
    public function getArguments() {
        return $this->method_arguments;
    }

    /**
     * Sets the description
     *
     * @param string $description description
     * 
     * @return Method
     */
    public function setDescription($description) {
        if (is_string($description) && strlen($description) > 0) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Returns the code of the method
     *
     * @return string
     */
    public function getCode() {
        if (isset($this->_reflection)) {
            $file = new File($this->_reflection->getFileName());
            return $file->getPart(
                    $this->_reflection->getStartLine(),
                    $this->_reflection->getEndLine()
            );
        }

        return false;
    }

    /**
     * refreshes the class object or class name
     *
     * @return Method
     */
    private function _refreshClassObject() {
        $class_name = $this->getClassName();
        $constructor_args = $this->getConstructorArguments();

        if (isset($class_name) && is_array($constructor_args)) {
            $reflection = new ReflectionClass($class_name);
            try {
                $this->class_obj = $reflection->newInstanceArgs($constructor_args);
            } catch (ReflectionException $ex) {
                throw new TargetNotFoundException(
                        $this->__toString() . ' not found', 0, $ex
                );
            }
        }

        return $this;
    }

    /**
     * Updates reflection object
     *
     * @return boolean
     */
    private function _refreshReflection() {
        $class_name = $this->getClassName();
        $method_name = $this->getName();

        if (isset($class_name) && isset($method_name)) {
            try {
                $this->_reflection = new ReflectionMethod($class_name, $method_name);
                return true;
            } catch (ReflectionException $ex) {
                throw new TargetNotFoundException(
                        'target ' . $this->__toString() . ' couldnt be found', 0, $ex
                );
            }
        }

        return false;
    }

    /**
     * Returns a unique Id
     *
     * @return string
     */
    public function getUniqueId() {
        return $this->_unique_id;
    }

    /**
     * Updates the unique id
     *
     * @return boolean
     */
    private function _refreshUniqueId() {
        $name = $this->getName();
        $class_name = $this->getClassName();

        if (isset($name) && isset($class_name)) {
            $unique_id = sprintf(
                            '%s%s%s%s', $class_name,
                            serialize($this->getConstructorArguments()),
                            $name,
                            serialize($this->getArguments())
            );

            $this->_unique_id = $unique_id;
        } else {
            $this->_unique_id = uniqid('target');
        }

        return true;
    }

}