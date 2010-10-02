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
 * Description of ConsoleGui
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
class ConsoleGui extends AbstractGui
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns template name
     *
     * @return string
     */
    protected function getBenchmarkStartedTemplate()
    {
        return 'console.pre_output.php';
    }

    /**
     * Returns template name
     *
     * @return string
     */
    protected function getTargetExecutionStartedTemplate()
    {
        return 'console.dialog.php';
    }

    /**
     * Returns template name
     *
     * @return string
     */
    protected function getTargetExecutionEndedTemplate()
    {
        return null;
    }

    /**
     * Returns template name
     *
     * @return string
     */
    protected function getBenchmarkEndedTemplate()
    {
        return 'console.post_output.php';
    }

    /**
     * Benchmark has finished
     *
     * @param Benchmark $benchmark Current Benchmark
     *
     * @return Gui
     */
    protected function benchmarkEnded(Benchmark $benchmark)
    {
        $this->clearLastDialogOutput();

        return parent::benchmarkEnded($benchmark);
    }

    /**
     * An target is going to be executed
     *
     * @param Benchmark $benchmark current benchmark
     *
     * @return Gui
     */
    protected function targetExecutionStarted(Benchmark $benchmark)
    {
        if ( !$this->isFirstTarget() ) {
            $this->clearLastDialogOutput();
        }

        return parent::targetExecutionStarted($benchmark);
    }

    /**
     * Clears last dialog message in a console
     *
     * @return boolean always true
     */
    protected function clearLastDialogOutput()
    {
        $last_output = $this->view->render();
        echo "\r" . str_pad('', strlen($last_output), ' ') . "\r";

        return true;
    }

}