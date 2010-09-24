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
 * Contains main functionality of Benchmark Gui's
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
abstract class AbstractGui implements IObserver
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
     * Template
     *
     * @var GuiTemplate
     */
    protected $view;
    /**
     * Targets which was executed before current target
     *
     * @var Target
     */
    protected $previous_target;

    /**
     * Template which gets used after benchmark started
     *
     * @return string
     */
    abstract function getBenchmarkStartedTemplate();

    /**
     * Template which gets used after benchmark has ended
     *
     * @return string
     */
    abstract function getBenchmarkEndedTemplate();

    /**
     * Template which gets used before a target gets executed
     *
     * @return string
     */
    abstract function getTargetExecutionStartedTemplate();

    /**
     * Template which gets used after a target gets executed
     *
     * @return string
     */
    abstract function getTargetExecutionEndedTemplate();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->previous_target = null;
        $this->targets_times = array();
        $this->targets_memory_leaked = array();

        $this->view = new GuiTemplate();
    }

    /**
     * Gets called if Benchmark notifies an event
     *
     * @param IObservable $observable Observable
     *
     * @return boolean false if event is unknown otherwise true
     */
    public function update(IObservable $observable)
    {
        if (!$observable instanceof IBenchmark) {
            return false;
        }

        $state = $observable->getState();
        switch ($state) {
            case State::BENCHMARK_STARTED:
                $this->benchmarkStarted($observable);
                break;

            case State::BENCHMARK_ENDED:
                $this->benchmarkEnded($observable);
                break;

            case State::TARGET_EXECUTION_STARTED:
                $this->targetExecutionStarted($observable);
                break;

            case State::TARGET_EXECUTION_ENDED:
                $this->targetExecutionEnded($observable);
                break;

            default:
                $msg = sprintf('Unknown state: %s', strval($state));
                throw new InvalidStateException($msg);
                break;
        }

        return true;
    }

    /**
     * Benchmark has started
     *
     * @param Benchmark $benchmark Current Benchmark
     *
     * @return void
     */
    protected function benchmarkStarted(Benchmark $benchmark)
    {
        $template = $this->getBenchmarkStartedTemplate();

        if (is_string($template) && strlen($template) > 0) {
            $this->view->setTemplate($this->getBenchmarkStartedTemplate());
            $this->assignGeneralInfos($benchmark);

            ob_start();
            $this->output($this->view->render());
        }

        return;
    }

    /**
     * An target is going to be executed
     *
     * @param Benchmark $benchmark current benchmark
     *
     * @return void
     */
    protected function targetExecutionStarted(Benchmark $benchmark)
    {
        $template = $this->getTargetExecutionStartedTemplate();
        $this->addToTargetPool($benchmark->getCurrentTarget());

        if (is_string($template) && strlen($template) > 0) {
            $this->view->setTemplate($template);
            $this->assignGeneralInfos($benchmark);
            $this->view->assign(
                'current_target', $benchmark->getCurrentTarget()
            );
            $current_iteration = $benchmark->getCurrentIteration();
            $this->view->assign(
                'current_iteration', $current_iteration
            );
            $this->view->assign(
                'percentage',
                $this->getPercentage(
                    $current_iteration - 1,
                    $benchmark->getIterations()
                )
            );

            $this->output($this->view->render());
        }

        return;
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
        $this->fetchExecutionInfos($benchmark);

        $template = $this->getTargetExecutionEndedTemplate();

        if (is_string($template) && strlen($template) > 0) {

            $this->view->setTemplate($template);
            $this->assignGeneralInfos($benchmark);
            $this->view->assign(
                'current_target', $benchmark->getCurrentTarget()
            );
            $current_iteration = $benchmark->getCurrentIteration();
            $this->view->assign(
                'current_iteration', $current_iteration
            );
            $this->view->assign(
                'percentage',
                $benchmark->getPercentage(
                    $current_iteration,
                    $benchmark->getIterations()
                )
            );
            $this->output($this->view->render());
        }

        $this->previous_target = $benchmark->getCurrentTarget();

        return $this;
    }

    /**
     * Benchmark has finished
     *
     * @param Benchmark $benchmark Current Benchmark
     *
     * @return void
     */
    protected function benchmarkEnded(Benchmark $benchmark)
    {
        $template = $this->getBenchmarkEndedTemplate();

        if (is_string($template) && strlen($template) > 0) {
            $this->view->setTemplate($template);
            $this->assignGeneralInfos($benchmark);

            $sorted_targets = ArrayUtils::sortArrayByArray(
                $this->getTargetPool(), $this->targets_times
            );

            $this->view->assign('benchmark_targets', $sorted_targets);
            $this->view->assign('times', $this->targets_times);
            $this->view->assign('memory', $this->targets_memory_leaked);

            $this->output($this->view->render());
            ob_end_clean();
        }

        return;
    }

    /**
     * Adds a target to the target pool
     *
     * @param ITarget $target Target
     *
     * @return void
     */
    protected function addToTargetPool(ITarget $target)
    {
        $key = $this->hash($target);
        if ( !isset($this->target_pool[$key]) ) {
            $this->target_pool[$key] = $target;
        }

        return;
    }

    /**
     * Returns the target pool
     *
     * @return array
     */
    protected function getTargetPool()
    {
        return $this->target_pool;
    }

    /**
     * Calculates a hash of an object
     *
     * @param object $obj object
     *
     * @return string
     */
    protected function hash($obj)
    {
        return spl_object_hash($obj);
    }

    /**
     * Calculates informations
     *
     * @param Benchmark $benchmark Benchmark
     *
     * @return void
     */
    protected function fetchExecutionInfos(Benchmark $benchmark)
    {
        $current_result = $benchmark->getLatestResult();
        $current_target = $benchmark->getCurrentTarget();

        $key = $this->hash($current_target);

        if (!isset($this->targets_times[$key])) {
            $this->targets_times[$key] = $current_result->getTimeElapsed();
            $this->targets_memory_leaked[$key] = $current_result->getMemoryLeaked();
        } else {
            $this->targets_times[$key] += $current_result->getTimeElapsed();
            $this->targets_memory_leaked[$key] += $current_result->getMemoryLeaked();
        }

        return;
    }

    /**
     * Assigns general informations
     *
     * @param Benchmark $benchmark Benchmark
     *
     * @return void
     */
    protected function assignGeneralInfos(Benchmark $benchmark)
    {
        $title = $benchmark->getTitle();
        $description = $benchmark->getDescription();
        $iterations = $benchmark->getIterations();
        $targets = $benchmark->getTargets();

        $this->view->assign('benchmark_title', $title);
        $this->view->assign('benchmark_description', $description);
        $this->view->assign('benchmark_iterations', $iterations);
        $this->view->assign('benchmark_targets', $targets);

        $this->view->assign('php_version', phpversion());

        return;
    }

    /**
     * Flushes message
     *
     * @param string $str message
     *
     * @return void
     */
    protected function output($str)
    {
        echo $str;
        ob_flush();
        flush();

        return;
    }

    /**
     * Returns previous target
     *
     * @return Targets
     */
    public function getPreviousTarget()
    {
        return $this->previous_target;
    }

    /**
     * Checks if current target is the very first one
     *
     * @return boolean
     */
    public function isFirstTarget()
    {
        return ($this->previous_target == null);
    }

    /**
     * Calculates the percentage
     *
     * @param numeric $current value
     * @param numeric $total   total value
     * 
     * @return numeric
     */
    protected function getPercentage($current, $total)
    {
        return 100 / $total * $current;
    }

}