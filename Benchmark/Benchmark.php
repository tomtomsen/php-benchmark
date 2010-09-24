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
require_once dirname(__FILE__) . '/Utils/Autoload.php';
Autoload::getInstance(dirname(__FILE__))->registerFolders(
    array(
        'Utils/',
        'Observer/',
        'Exception/',
    )
);

/**
 * A Benchmark can be run and starts specified methods/functions
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
class Benchmark implements IObservable, IBenchmark
{

    /**
     * Title of the benchmark
     *
     * @var string
     */
    protected $title;
    /**
     * Description of the benchmark
     *
     * @var string
     */
    protected $description;
    /**
     * Count of iteration per test method
     *
     * @var integer
     */
    protected $iterations;
    /**
     * list of targets (method, function)
     *
     * @var array
     */
    protected $targets;
    /**
     * list of observers
     *
     * @var array
     */
    protected $observers;
    /**
     * Current state
     *
     * @see State
     * @var integer
     */
    protected $state;
    /**
     * Latest target
     *
     * @var arget
     */
    protected $current_target;
    /**
     * Latest result
     *
     * @var Result
     */
    protected $latest_result;
    /**
     * Current iteration
     *
     * @var integer
     */
    protected $current_iteration;

    /**
     * constructor
     *
     * @param string $title       title of the benchmark
     * @param string $description description of the benchmark
     */
    public function __construct($title, $description = '')
    {
        $this->setTitle($title);
        $this->setDescription($description);

        $this->iterations = 1000;
        $this->targets = array();
        $this->observers = array();
        $this->state = null;
        $this->current_target = null;
        $this->latest_result = null;
        $this->current_iteration = null;
    }

    /**
     * Starts the benchmark
     *
     * @return boolean false if an error occured, otherwise true
     */
    public function run()
    {
        if (empty($this->observers)) {
            throw new NoObserverGivenException();
        }
        if (empty($this->targets)) {
            throw new NoTargetsGivenException();
        }

        $prev_execution_time = ini_get('max_execution_time');
        set_time_limit(0);

        $this->notify(State::BENCHMARK_STARTED);

        $keys = array_keys($this->targets);
        for ($c = $this->getIterations(), $j = $c; $j--;) {
            $this->setCurrentIteration($c - $j);

            for ($i = count($keys); $i--;) {

                $current_target = $this->targets[$keys[$i]];
                $this->setCurrentTarget($current_target);

                $this->notify(State::TARGET_EXECUTION_STARTED);
                $this->invokeCallback($current_target);
                $this->notify(State::TARGET_EXECUTION_ENDED);
            }
        }
        $this->notify(State::BENCHMARK_ENDED);

        set_time_limit($prev_execution_time);

        return true;
    }

    /**
     * Adds a target to the benchmark
     *
     * @param ITarget $target a target
     * 
     * @return Benchmark
     */
    public function addTarget(ITarget $target)
    {
        $this->targets[] = clone $target;
    }

    /**
     * Invokes a target
     *
     * @param arget $target method or function to call
     * 
     * @return Result
     */
    protected function invokeCallback(ITarget $target)
    {
        $memory_start = memory_get_usage(true);
        $time_start = microtime(true);

        $method_result = $target->invoke();

        $time_end = microtime(true);
        $memory_end = memory_get_usage(true);

        $result = new Result();
        $result->setStartTime($time_start);
        $result->setEndTime($time_end);
        $result->setMemoryLeaked($memory_end - $memory_start);
        $result->setMethodResult($method_result);

        $this->setLatestResult($result);

        return $result;
    }

    /**
     * Sets a the benchmark title
     *
     * @param string $title title of the benchmark
     * 
     * @return Benchmark
     */
    public function setTitle($title)
    {
        if (is_string($title) && strlen($title) > 0) {
            $this->title = $title;
        }

        return $this;
    }

    /**
     * Returns the benchmark title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the benchmark description
     *
     * @param string $description description of the benchmark
     *
     * @return Benchmark
     */
    public function setDescription($description)
    {
        if (is_string($description)) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * Returns the benchmark description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the latest benchmark target
     *
     * @param arget $target latest executed method or function
     *
     * @return Benchmark
     */
    protected function setCurrentTarget(ITarget $target)
    {
        $this->current_target = $target;

        return $this;
    }

    /**
     * Returns the latest benchmark target
     *
     * @return arget
     */
    public function getCurrentTarget()
    {
        return $this->current_target;
    }

    /**
     * Sets the latest benchmark result
     *
     * @param Result $result latest benchmark result
     *
     * @return Benchmark
     */
    protected function setLatestResult(Result $result)
    {
        $this->latest_result = $result;

        return $this;
    }

    /**
     * Returns the latest benchmark result
     *
     * @return Result
     */
    public function getLatestResult()
    {
        return $this->latest_result;
    }

    /**
     * Sets the current iteration
     *
     * @param integer $iteration current iteration
     *
     * @return Benchmark
     */
    protected function setCurrentIteration($iteration)
    {
        if (is_numeric($iteration)) {
            $this->current_iteration = intval($iteration);
        }

        return $this;
    }

    /**
     * Returns the current iteration
     *
     * @return integer
     */
    public function getCurrentIteration()
    {
        return $this->current_iteration;
    }

    /**
     * Returns all benchmark targets
     *
     * @return array contains arget objects
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Sets the number of iterations
     *
     * @param integer $iteration number of iterations per method/function
     *
     * @return Benchmark
     */
    public function setIterations($iteration)
    {
        if (is_numeric($iteration) && $iteration > 0) {
            $this->iterations = $iteration;
        }

        return $this;
    }

    /**
     * Returns the number of iterations
     *
     * @return integer
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * Attaches an observer
     *
     * @param IObserver $observer Observer to be attached
     *
     * @return Benchmark
     */
    public function attach(IObserver $observer)
    {
        if (!in_array($observer, $this->observers)) {
            $this->observers[] = $observer;
        }

        return $this;
    }

    /**
     * Detaches an observer
     *
     * @param IObserver $observer Observer to be removed
     *
     * @return Benchmark
     */
    public function detach(IObserver $observer)
    {
        if (in_array($observer, $this->observers)) {
            $offset = array_search($observer, $this->observers);
            $this->observers = array_splice($this->observers, $offset + 1, 1);
        }

        return $this;
    }

    /**
     * Notifies all observers
     *
     * @param integer $state State (see State)
     *
     * @return Benchmark
     */
    public function notify($state = null)
    {
        if (isset($state)) {
            $this->setState($state);

            foreach ($this->observers as $observer) {
                $observer->update($this);
            }
        }

        return $this;
    }

    /**
     * Sets the current state
     *
     * @param integer $state State
     * 
     * @see State
     *
     * @return Benchmark
     */
    protected function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Returns the current benchmark state
     *
     * @see State
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

}

if (!function_exists('memory_get_usage')) {

    /**
     * Alternative method for memory_get_usage()
     *
     * http://www.php.net/manual/de/function.memory-get-usage.php#64156
     *
     * @return integer Returns the memory amount in bytes. 
     */
    function memory_get_usage()
    {
        // If its Windows
        // Tested on Win XP Pro SP2. Should work on Win 2003 Server too
        // Doesn't work for 2000
        // If you need it to work for 2000 look at
        // http://us2.php.net/manual/en/function.memory-get-usage.php#54642
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $output = array();
            exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);

            return preg_replace('/[\D]/', '', $output[5]) * 1024;
        } else {
            // We now assume the OS is UNIX
            // Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
            // This should work on most UNIX systems
            $pid = getmypid();
            exec("ps -eo%mem,rss,pid | grep $pid", $output);
            $output = explode("  ", $output[0]);
            //rss is given in 1024 byte units
            return $output[1] * 1024;
        }
    }

}