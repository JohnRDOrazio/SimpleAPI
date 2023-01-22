<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class IntegerParameter {
    private int $_value;

    //public function __construct() {}

    public function setValue( int $value ) {
        $this->_value = $value;
    }

    public function getValue() : int {
        return $this->_value;
    }

}
