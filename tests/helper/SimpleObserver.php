<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../../Observer.php';
require_once dirname(__FILE__) . '/../../Benchmark.php';

/**
 * Description of SimpleObserver
 *
 * @author tomtomsen
 */
class SimpleObserver implements Observer, Observable {

    protected $observer;

    public function attach(Observer $observer) {
        $this->observer = $observer;
    }

    public function detach(Observer $observer) {
        $this->observer = array();
    }

    public function notify() {
        $this->observer->update($this);
    }

    public function update(Observable $observable) {
        if ($observable instanceof Benchmark) {
            switch ($observable->getState()) {
                case State::BENCHMARK_ENDED:
                    $this->nded($observable);
                    break;

                case State::BENCHMARK_STARTED:
                    $this->tarted($observable);
                    break;

                case State::RESULT_RECEIVED:
                    $this->esultReceived($observable);
                    break;

                default:
                    $this->fail();
                    break;
            }
        }
    }

    public function tarted(Benchmark $observable) {
        return $observable;
    }

    public function nded(Benchmark $observable) {
        return $observable;
    }

    public function ResultReceived(Benchmark $observable) {
        return $observable;
    }

    public function fail() {
        throw new Exception('unexpected situation');
    }
}
