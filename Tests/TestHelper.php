<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestHelper
 *
 * @author tomtomsen
 */
class TestHelper {

    static public function includeDoSomethingFunction() {
        require dirname(__FILE__) . '/helper/function_doSomething.php';
    }

    static public function includeComplexClass() {
        require_once dirname(__FILE__) . '/helper/ComplexClass.php';
    }
}