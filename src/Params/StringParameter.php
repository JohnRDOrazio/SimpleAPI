<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class StringParameter {
    private string $_value;

    //public function __construct() {}

    public function setValue( string $value ) {
        $this->_value = $value;
    }

    public function getValue() : string {
        return $this->_value;
    }

}
