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
 * A result of a benchmark
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
class Result
{
    /**
     * start time (microtime) in seconds
     *
     * @var double
     */
    protected $start_time;
    /**
     * end time (microtime) in seconds
     *
     * @var double
     */
    protected $end_time;
    /**
     * result of the method
     *
     * @var mixed
     */
    protected $method_result;
    /**
     * memory leakish in bytes
     *
     * @var integer
     */
    protected $memory_leaked;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->start_time = null;
        $this->end_time = null;
        $this->method_result = null;
        $this->memory_leaked = null;
    }

    /**
     * Sets the result of the method
     *
     * @param mixed $result Method or function result
     *
     * @return Result
     */
    public function setMethodResult($result)
    {
        $this->method_result = $result;

        return $this;
    }

    /**
     * Returns the result of the method
     *
     * @return mixed
     */
    public function getMethodResult()
    {
        return $this->method_result;
    }

    /**
     * Sets the memory leaked
     *
     * In bytes
     *
     * @param integer $memory memory in bytes which leaked
     *
     * @return Result
     */
    public function setMemoryLeaked($memory)
    {
        $this->memory_leaked = $memory;

        return $this;
    }

    /**
     * Returns the memory leaked
     *
     * In bytes
     *
     * @return integer
     */
    public function getMemoryLeaked()
    {
        return $this->memory_leaked;
    }

    /**
     * Set the start time
     *
     * @param double $start_time start time
     *
     * @return Result
     */
    public function setStartTime($start_time)
    {

        if (is_numeric($start_time) && $start_time > 0) {
            $end_time = $this->getEndTime();
            if (isset($end_time) && $start_time > $end_time) {
                $this->start_time = $end_time;
                $this->setEndTime($start_time);
            } else {
                $this->start_time = $start_time;
            }
        }

        return $this;
    }

    /**
     * Returns the start time
     *
     * @return double
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Sets the end time
     *
     * @param double $end_time end time
     *
     * @return Result
     */
    public function setEndTime($end_time)
    {
        if (is_numeric($end_time) && $end_time > 0) {
            $start_time = $this->getStartTime();
            if (isset($start_time) && $end_time < $start_time) {
                $this->end_time = $start_time;
                $this->setStartTime($end_time);
            } else {
                $this->end_time = $end_time;
            }
        }

        return $this;
    }

    /**
     * Returns the end time
     *
     * @return double
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Returns the time elapsed between end and start time
     *
     * if end time is not set but start time, time will calculated
     * till now
     *
     * @return double
     */
    public function getTimeElapsed()
    {
        $start_time = $this->getStartTime();

        if (isset($start_time)) {
            $end_time = $this->getEndTime();

            if (!isset($end_time)) {
                $end_time = microtime(true);
            }

            return $end_time - $this->getStartTime();
        }

        return false;
    }

}