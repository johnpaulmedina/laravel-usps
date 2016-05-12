<?php

namespace Usps;

class Usps {

    private $config;

    function __autoload($class_name) {
        include $class_name . '.php';
    }

    public function __construct($config) {
        $this->config = $config;
    }

    public function test() {
        return ['test'=>'valid'];
    }
}
