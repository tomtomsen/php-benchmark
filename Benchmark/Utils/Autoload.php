<?php

/**
 * Utils
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
 * @category  Utils
 * @package   Utils
 * @author    Tom Tomsen <tom.tomsen@inbox.com>
 * @copyright 2010 Tom Tomsen <tom.tomsen@inbox.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://github.com/tomtomsen/php-utils
 * @since     File available since Release 1.0.0
 */

/**
 * Autoload Singleton
 *
 * @category  Utils
 * @package   Autoload
 * @author    Tom Tomsen <tom.tomsen@inbox.com>
 * @copyright 2010 Tom Tomsen <tom.tomsen@inbox.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: 1.0.0
 * @link      http://github.com/tomtomsen/php-utils
 * @since     Class available since Release 1.0.0
 */
class Autoload
{

    /**
     * Instance
     *
     * @var Autoload
     */
    static private $_instance;
    /**
     * Rootpath
     *
     * @var string
     */
    static protected $root_path;
    /**
     * class containing folders
     *
     * @var array
     */
    protected $folders;
    /**
     * filename patterns
     *
     * @var array
     */
    protected $patterns;
    /**
     * classname => path to class
     *
     * @var array
     */
    protected $map;

    // @codeCoverageIgnoreStart
    /**
     * disable cloning
     *
     * @return void
     */
    private function __clone()
    {
        return;
    }

    /**
     * disable construct
     */
    protected function __construct()
    {
        $this->folders = array('./');
        $this->patterns = array('*.php');
        $this->map = array();
    }

    // @codeCoverageIgnoreEnd

    /**
     * returns an instance of Autoload
     *
     * @param string $root_path root path
     *
     * @return Autoload
     */
    static public function getInstance($root_path = null)
    {
        if (!isset($root_path)) {
            $root_path = dirname(__FILE__) . '/../';
        }
        self::$root_path = rtrim($root_path, '/') . '/';

        if (!isset(self::$_instance)) {
            self::$_instance = new Autoload();
            self::$_instance->register();
        }

        return self::$_instance;
    }

    /**
     * registers a classname - path to class map
     *
     * @param array $map classname => path to class
     * 
     * @return Autoload
     */
    public function registerClassNames(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * registers folders, which contains classes
     *
     * @param array $folders list of folders
     * 
     * @return Autoload
     */
    public function registerFolders(array $folders)
    {
        for ($i = count($folders); $i--;) {
            $this->folders[] = rtrim($folders[$i], '/') . '/';
        }

        return $this;
    }

    /**
     * filename patterns
     *
     * '*' will be replaced by classname
     *
     * @param array $patterns patterns
     * 
     * @return Autoload
     */
    public function setFilePatterns(array $patterns)
    {
        array_splice($this->patterns, count($this->patterns), 0, $patterns);

        return $this;
    }

    /**
     * spl_autoload function
     *
     * @param string $classname classname
     *
     * @return boolean
     */
    protected function autoload($classname)
    {
        if (isset($this->map) && isset($this->map[$classname])) {
            include_once self::$root_path . $this->map[$classname];
            return true;
        }

        for ($i = count($this->folders); $i--;) {
            $path = self::$root_path . $this->folders[$i];

            for ($j = count($this->patterns); $j--;) {
                $filename = str_replace('*', $classname, $this->patterns[$j]);
                if (file_exists($path . $filename)) {
                    include_once $path . $filename;
                    return true;
                }
            } // @codeCoverageIgnoreStart
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * registers autoload function
     *
     * @return void
     */
    protected function register()
    {
        spl_autoload_register(array($this, 'autoload'));

        return;
    }

}
