php-benchmark
===============
**php-benchmark** is a php framework to messure the performance of functions or class-methods.

Using the php-benchmark API
---------------------------

<?php
  require_once 'Benchmark/Benchmark.php'
  require_once 'Benchmark/Observer/Gui.php'

  $benchmark = new Benchmark('benchmark name', 'description of the benchmark');
  
  $benchmark->addTarget(new Functionn('name', array(), 'description'));
  $benchmark->addTarget(new Method('MyClass', array(), 'doSomething', array(), 'description));

  $benchmark->setIterations(200);
  $benchmark->attach(new Gui());
  $benchmark->run();



