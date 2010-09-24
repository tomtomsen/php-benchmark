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
 * Constants container
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
class GuiTemplate implements ITemplateView
{

    /**
     * Template name
     *
     * @var string
     */
    protected $template;
    /**
     * Variable Container
     *
     * @var array
     */
    protected $vars;

    /**
     * Constructor
     *
     * @param string $template template name
     */
    public function __construct($template = null)
    {
        $this->template = null;
        $this->vars = array();

        $this->setTemplate($template);
    }

    /**
     * Sets the template
     *
     * @param string $template template filename
     *
     * @return GuiTemplate
     */
    public function setTemplate($template)
    {

        if (is_string($template) && strlen($template) > 0) {
            $filename = $this->getTemplateDir() . $template . '.php';
            if (!is_file($filename)) {
                include_once dirname(__FILE__) .
                  '/../Exception/TemplateNotFoundException.php';
                throw new TemplateNotFoundException(
                  'template ' . $filename . ' not found.'
                );
            }

            $this->template = $filename;
        }

        return $this;
    }

    /**
     * Returns the current template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns the template directory
     *
     * @return string
     */
    protected function getTemplateDir()
    {
        return dirname(__FILE__) . '/templates/';
    }

    /**
     * Assigns a value to a template variable
     *
     * @param string $name  variable name
     * @param mixed  $value value of the variable
     *
     * @return GuiTemplate
     */
    public function assign($name, $value)
    {
        $this->vars[$name] = $value;

        return $this;
    }

    /**
     * Renders the template
     *
     * @return string template
     */
    public function render()
    {
        if (!isset($this->template)) {
            return false;
        }

        ob_start();
        include $this->template;
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    /**
     * Returns a template variable
     *
     * @param string $property name of the variable
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (isset($this->vars[$property])) {
            return $this->vars[$property];
        }

        return null;
    }

}