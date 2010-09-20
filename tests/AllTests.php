<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'PHPUnit/Framework.php';

/**
 * Description of AllTests
 *
 * @author tomtomsen
 */
class AllTests
{

    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Benchmark');

        self::collectTests($suite, dirname(__FILE__));

        return $suite;
    }

    static protected function collectTests(PHPUnit_Framework_TestSuite $suite, $path)
    {
        $testing_folders = array('observer');

        $directory = new DirectoryIterator($path);

        foreach ($directory as $fileInfo) {
            if ($fileInfo->isFile()) {
                $filename = $fileInfo->getBasename('.php');
                if (strtolower(substr($filename, -4)) == 'test') {
                    self::addTestToSuite($suite, $filename, $fileInfo->getPathname());
                }
            } else if ($fileInfo->isDir() && in_array($fileInfo->getBasename(), $testing_folders)) {
                self::collectTests($suite, $fileInfo->getPathname());
            }
        }
    }

    static protected function addTestToSuite(PHPUnit_Framework_TestSuite &$suite,
            $class, $path)
    {

        require_once $path;
        $suite->addTestSuite($class);
    }

}
