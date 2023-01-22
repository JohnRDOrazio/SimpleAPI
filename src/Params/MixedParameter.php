<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class MixedParameter {
    private mixed $_value;

    //public function __construct() {}

    public function setValue( mixed $value ) {
        $this->_value = $value;
    }

    public function getValue() : mixed {
        return $this->_value;
    }

}
