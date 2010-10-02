<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if ( !defined('SRC_DIR') ) {
    define('SRC_DIR', dirname(__FILE__) . '/../Benchmark/');
}

if ( !defined('TEST_DIR') ) {
    define('TEST_DIR', dirname(__FILE__));
}

/**
 * Description of TestHelper
 *
 * @author tomtomsen
 */
class TestHelper {

    static public function includeDoSomethingFunction() {
        require dirname(__FILE__) . '/Helper/function_doSomething.php';
    }

    static public function includeComplexClass() {
        require_once dirname(__FILE__) . '/Helper/ComplexClass.php';
    }
}