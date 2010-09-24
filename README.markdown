php-benchmark
===============
**php-benchmark** is a php framework to messure the performance of functions or class-methods.

Version: _1.0beta1_

Using the php-benchmark API
---------------------------

    <?php
      require_once 'Benchmark/Benchmark.php'
      require_once 'Benchmark/Observer/Gui.php'

      $benchmark = new Benchmark('benchmark name', 'description of the benchmark');
  
      $benchmark->addTarget(new BenchmarkFunction('name', array(), 'description'));
      $benchmark->addTarget(new BenchmarkMethod('MyClass', array(), 'doSomething', array(), 'description));

      $benchmark->setIterations(200);
      $benchmark->attach(new Gui());
      $benchmark->run();

 The *php-benchmark* is callable by the console `php my-benchmark.php` or by loading the script in a browser.

Upcoming features
-----------------

 - Sometimes its important, to ensure all methods/functions return the same result. 
   therefor `$benchmark->expectSameResults(true)`
 - Result logging. Sometimes i'd like to know if changes on my function/method improve the performance.

Requirements
------------
*php-benchmark* is tested with

 - php 5.3.3
 - php 5.2.14

Support
-------
I'm sure the files contain programming errors and lots of
English language mistakes. Find them and send them to me ;-)

Tom Tomsen <<tom.tomsen@inbox.com>>

