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
 * View interface
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
class Gui implements IObserver
{

    /**
     * Used Targets
     *
     * @var array
     */
    protected $best_fitting_gui;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->best_fitting_gui = null;
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
        if (!$observable instanceof Benchmark) {
            return false;
        }

        $this->getBestFittingGui()->update($observable);
    }

    /**
     * Returns and sets the best fitting GUI
     *
     * @return IObserver
     */
    protected function getBestFittingGui()
    {
        if (!isset($this->best_fitting_gui)) {
            if ($this->isBrowser()) {
                include_once dirname(__FILE__) . '/HtmlGui.php';
                $this->best_fitting_gui = new HtmlGui();
            } else {
                include_once dirname(__FILE__) . '/ConsoleGui.php';
                $this->best_fitting_gui = new ConsoleGui();
            }
        }

        return $this->best_fitting_gui;
    }

    /**
     * Returns if the environment in which the Gui is running
     * is a browser
     *
     * @return boolean
     */
    protected function isBrowser()
    {
        return isset($_SERVER['SERVER_SOFTWARE']);
    }

}