<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class ArrayParameter {
    private array $_value;

    //public function __construct() {}

    public function setValue( array $value ) {
        $this->_value = $value;
    }

    public function getValue() : array {
        return $this->_value;
    }

}
