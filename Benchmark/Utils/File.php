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
 * File functions
 *
 * @category  Utils
 * @package   File
 * @author    Tom Tomsen <tom.tomsen@inbox.com>
 * @copyright 2010 Tom Tomsen <tom.tomsen@inbox.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: 1.0.0
 * @link      http://github.com/tomtomsen/php-utils
 * @since     Class available since Release 1.0.0
 */
class File
{

    /**
     * Filename
     *
     * @var string
     */
    protected $filename;

    /**
     * Constructor
     *
     * @param string $filename filename
     */
    public function __construct($filename)
    {
        if (!is_file($filename)) {
            include_once dirname(__FILE__) . '/Exceptions/FileNotFoundException.php';
            throw new FileNotFoundException($filename . ' not found');
        }

        $this->filename = $filename;
    }

    /**
     * Returns the filename
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns a part of a file
     *
     * @param integer $startline start line number
     * @param integer $endline   end line number
     * 
     * @return string
     */
    public function getPart($startline, $endline)
    {
        if (!is_numeric($startline) || !is_numeric($endline)) {
            return false;
        }

        if ($startline <= 0) {
            $startline = 1;
        }

        $content = false;
        $fp = @fopen($this->filename, 'r');
        if (is_resource($fp)) {
            if (flock($fp, LOCK_SH)) {
                $this->readLines($fp, $startline - 1);
                $content = $this->readLines($fp, $endline - $startline + 1);

                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

        return $content;
    }

    /**
     * Reads some line
     *
     * @param resource $fp  file-pointer
     * @param integer  $num linecount
     * 
     * @return string 
     */
    protected function readLines($fp, $num)
    {
        $content = '';

        for (
        $i = 0, $j = $num; $i < $j && (null !== ($content .= fgets($fp))); $i++) {

        }

        return $content;
    }

}