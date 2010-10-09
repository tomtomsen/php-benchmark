<?php

require_once dirname(__FILE__) . '/../../Benchmark/Benchmark.php';
require_once dirname(__FILE__) . '/../../Benchmark/Observer/Gui.php';

$entry_count = 1000;

$benchmark = new Benchmark('Counting Loops', 'comparing different types of loops iteration through an array with ' . $entry_count . ' elements.');

$data = array();
for ($i = $entry_count; $i--;) {
    $data[] = $i;
}

$benchmark->setIterations(200);

$benchmark->addTarget(new BenchmarkFunction('for_common', array($data), 'asdf asdfa asdf asdf asdf asdf <code>for($i = 0; $i < count($data); $i ++);</code>'));
$benchmark->addTarget(new BenchmarkFunction('for_minimal_reversed_order', array($data), '<code>for($i = count($data); $i --; );</code>'));
$benchmark->addTarget(new BenchmarkFunction('for_count_stored', array($data), '<code>for($i = 0, $j = count($data); $i < $j; $i++);</code>'));
$benchmark->addTarget(new BenchmarkFunction('for_minimal_non_reversed_order', array($data), '<code>for($c = count($tmp),$i = $c; $i --; );</code>'));
$benchmark->addTarget(new BenchmarkFunction('while_common', array($data), '<code>$i = 0; while($i < count($data)) ++$i;</code>'));
$benchmark->addTarget(new BenchmarkFunction('while_count_stored', array($data), '<code>$i = 0; $j = count($data); while($i < $j) ++$i;</code>'));
$benchmark->addTarget(new BenchmarkFunction('foreach_common', array($data), '<code>foreach($data as $key => $val);</code>'));

$benchmark->run();


function for_common($data) {
  for($i = 0; $i < count($data); $i ++);
}

function for_minimal_reversed_order($data) {
  for($i = count($data); $i --; );
}

function for_count_stored($data) {
  for($i = 0, $j = count($data); $i < $j; $i++);
}

function for_minimal_non_reversed_order($data) {
  for($c = count($data),$i = $c; $i --; );
}

function while_common($data) {
    $i = 0; while($i < count($data)) ++$i;
}

function while_count_stored($data) {
    $i = 0; $j = count($data); while($i < $j) ++$i;
}

function foreach_common($data) {
    foreach($data as $key => $value);
}
