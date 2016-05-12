<?php

namespace Jp\Usps;

function __autoload($class_name) {
    include $class_name . '.php';
}

class Usps {

    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function test() {
        return ['test'=>'valid'];
    }
}
