<?php

if (!class_exists('ComplexClass')) {
    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of ComplexClass
     *
     * @author tomtomsen
     */
    class ComplexClass
    {

        protected $param1;
        protected $param2;

        public function __construct($param1, $param2)
        {
            $this->param1 = $param1;
            $this->param2 = $param2;
        }

        public function doSomething($param1, $param2)
        {
            $this->param1 = $param1;
            $this->param2 = $param2;
        }

        public function getArguments()
        {
            return array($this->param1, $this->param2);
        }

    }

}
