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
 *   * Redistributions of source c  ode must retain the above copyright
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
 * Function Target
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
class BenchmarkFunction implements ITarget
{

    /**
     * name of the function
     *
     * @var string
     */
    protected $function_name;
    /**
     * function arguments
     *
     * @var array
     */
    protected $function_arguments;
    /**
     * Description
     *
     * @var string
     */
    protected $description;
    /**
     * Reflection object
     *
     * @var ReflectionFunction
     */
    private $_reflection;
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
     * Unique Id
     *
     * @var string
     */
    private $_unique_id;

    /**
     * Constructor
     *
     * @param string $name        function name
     * @param array  $args        function arguments
     * @param string $description description
     */
    public function __construct($name = '', $args = array(), $description = '')
    {
        $this->function_name = null;
        $this->function_arguments = array();
        $this->description = null;
        $this->pre_executed_target = null;
        $this->post_executed_target = null;

        $this->_reflection = null;
        $this->_unique_id = null;

        $this->setName($name);
        $this->setArguments($args);
        $this->setDescription($description);
    }

    /**
     * Returns a string representating the function
     *
     * To get a unique string use getUniqueId()
     *
     * @return string
     */
    public function __toString()
    {
        $args = $this->getArguments();
        return $this->getName() . '(' . (!empty($args) ? '..' : '') . ')';
    }

    /**
     * Returns the unique Id
     *
     * @return string
     */
    public function getUniqueId()
    {
        return $this->_unique_id;
    }

    /**
     * Returns the code of the function
     *
     * @return string
     */
    public function getCode()
    {
        if (isset($this->_reflection)) {
            $file = new File($this->_reflection->getFileName());
            $code = $file->getPart(
                $this->_reflection->getStartLine(),
                $this->_reflection->getEndLine()
            );

            return $code;
        }

        return false;
    }

    /**
     * Invokes the function
     *
     * @return mixed result of the function
     */
    public function invoke()
    {
        if (!isset($this->_reflection)) {
            $msg = $this->__toString() . ' not found.';
            throw new TargetNotFoundException($msg);
        }

        if (isset($this->pre_executed_target)) {
            $this->pre_executed_target->invoke();
        }

        $result = $this->_reflection->invokeArgs($this->getArguments());

        if (isset($this->post_executed_target)) {
            $this->post_executed_target->invoke();
        }

        return $result;
    }

    /**
     * Sets the pre-executed target
     *
     * @param ITarget $target Target
     *
     * @return BenchmarkMethod
     */
    public function setPreExecutedTarget(ITarget $target)
    {
        if ($target === $this) {
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
     * @return BenchmarkMethod
     */
    public function setPostExecutedTarget(ITarget $target)
    {
        if ($target === $this) {
            throw new PossibleRecursionException('Recursion detected');
        }
        $this->post_executed_target = $target;

        return $this;
    }

    /**
     * Set the function name
     *
     * @param string $name function name
     *
     * @return unction
     */
    public function setName($name)
    {
        if (is_string($name) && strlen($name) > 0) {
            if (!function_exists($name)) {
                $msg = sprintf('function \'%s\' not found.', $name);
                throw new TargetNotFoundException($msg);
            }

            $this->function_name = $name;

            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns the function name
     *
     * @return string
     */
    public function getName()
    {
        return $this->function_name;
    }

    /**
     * Sets the function arguments
     *
     * @param array $args function arguments
     *
     * @return unction
     */
    public function setArguments(array $args)
    {
        if (is_array($args)) {
            $this->function_arguments = $args;
            $this->_refreshReflection();
            $this->_refreshUniqueId();
        }

        return $this;
    }

    /**
     * Returns the function arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->function_arguments;
    }

    /**
     * Sets the function description
     *
     * @param string $description function description
     *
     * @return unction
     */
    public function setDescription($description)
    {
        if (is_string($description) && strlen($description) > 0) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * Returns the function description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Updates the reflection object
     *
     * @return boolean
     */
    private function _refreshReflection()
    {
        $function_name = $this->getName();

        if (isset($function_name)) {
            try {
                $this->_reflection = new ReflectionFunction($function_name);

                return true;
            } catch (ReflectionException $ex) {
                $msg = sprintf(
                    'target \'%s\' couldnt be found',
                    $this->__toString()
                );
                throw new TargetNotFoundException($msg, 0, $ex);
            }
        }

        return false;
    }

    /**
     * Updates the unique id
     *
     * @return boolean
     */
    private function _refreshUniqueId()
    {
        $name = $this->getName();
        if (isset($name)) {
            $this->_unique_id = $name . serialize($this->getArguments());
        } else {
            $this->_unique_id = uniqid('target');
        }

        return true;
    }

}
