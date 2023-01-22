<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class FloatParameter {
    private float $_value;

    //public function __construct() {}

    public function setValue( float $value ) {
        $this->_value = $value;
    }

    public function getValue() : float {
        return $this->_value;
    }

}
