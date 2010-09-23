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
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logger
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
class Logger implements IObserver
{

    /**
     * Array with calculated total time for each target
     *
     * @var array
     */
    protected $targets_times;
    /**
     * Array with calculated total memory leaked for each target
     *
     * @var array
     */
    protected $targets_memory_leaked;
    /**
     * directory name
     *
     * @var string
     */
    protected $directoy;

    /**
     * Constructor
     *
     * @param string $dirname directory name
     */
    public function __construct($dirname)
    {
        $result = $this->setDirectory($dirname);

        if (false === $result) {
            // @todo: exception
            throw new Exception('X');
        }

        $this->targets_times = array();
        $this->targets_memory_leaked = array();
    }

    /**
     * Gets called when a benchmark sends a state-changed-message
     *
     * @param IObservable $observable Observer
     *
     * @return boolean
     */
    public function update(IObservable $observable)
    {
        if (!$observable instanceof Benchmark) {
            return false;
        }

        $state = $observable->getState();
        switch ($state) {
        case State::BENCHMARK_ENDED:
            $this->nded($observable);
            break;

        case State::TARGET_EXECUTION_ENDED:
            $this->targetExecutionEnded($observable);
            break;
        }

        return true;
    }

    /**
     * Benchmark has finished
     *
     * @param Benchmark $benchmark Current Benchmark
     *
     * @return Gui
     */
    protected function nded(Benchmark $benchmark)
    {
        $targets = $benchmark->getTargets();
        $keys = array_keys($targets);

        for ($i = count($targets); $i--;) {
            $key = $keys[$i];
            $target = $targets[$keys[$i]];
            $directory = rtrim($this->getDirectory(), '/') . '/';
            $filename = $directory . $target->__toString() . '.log';
            $fp = fopen($filename, 'a');

            if (null != $fp) {
                $str = sprintf(
                    '%f;%d;%;%s' . "\n",
                    $this->targets_times[$key],
                    $this->targets_memory_leaked[$key],
                    $benchmark->getIterations(), serialize($target)
                );

                fputs($fp, $str, strlen($str));
                fclose($fp);
            }
        }
    }

    /**
     * An result has been generated
     *
     * @param Benchmark $benchmark Current Benchmark
     *
     * @return Gui
     */
    protected function targetExecutionEnded(Benchmark $benchmark)
    {
        $current_result = $benchmark->getLatestResult();
        $current_target = $benchmark->getCurrentTarget();

        $key = $current_target->getUniqueId();

        if (!isset($this->targets_times[$key])) {
            $this->targets_times[$key] = $current_result->getTimeElapsed();
            $this->targets_memory_leaked[$key] = $current_result->getMemoryLeaked();
        } else {
            $this->targets_times[$key] += $current_result->getTimeElapsed();
            $this->targets_memory_leaked[$key] += $current_result->getMemoryLeaked();
        }

        return $this;
    }

    /**
     * Sets the directory
     *
     * @param string $directory directory name
     * 
     * @return Logger
     */
    public function setDirectory($directory)
    {
        if (is_string($directory) && is_dir($directory)) {
            $this->directory = $directory;
            return $this;
        }

        return false;
    }

    /**
     * Returns the directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

}