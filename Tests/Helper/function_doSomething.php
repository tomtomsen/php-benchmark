<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of function_doSomething
 *
 * @author tomtomsen
 */
global $doSomething_called;
global $doSomething_arguments;

$doSomething_called = false;
$doSomething_arguments = array();

if (!function_exists('doSomething')) {

    function doSomething($param1, $param2) {

        global $doSomething_called;
        global $doSomething_arguments;

        $doSomething_called = true;
        $doSomething_arguments = array($param1, $param2);

        return;
    }

}

global $doSomething2_called;
global $doSomething2_arguments;

$doSomething2_called = false;
$doSomething2_arguments = array();

if (!function_exists('doSomething2')) {

    function doSomething2($param1, $param2) {

        global $doSomething2_called;
        global $doSomething2_arguments;

        $doSomething2_called = true;
        $doSomething2_arguments = array($param1, $param2);
    }

}