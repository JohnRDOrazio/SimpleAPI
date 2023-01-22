<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class BooleanParameter {
    private bool $_value;

    //public function __construct() {}

    public function setValue( bool $value ) {
        $this->_value = $value;
    }

    public function getValue() : bool {
        return $this->_value;
    }

}
