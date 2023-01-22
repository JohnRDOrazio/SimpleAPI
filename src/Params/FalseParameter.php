<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class FalseParameter {
    private bool $_value; //from PHP 8.1, we can use false as a type of it's own

    //public function __construct() {}

    public function setValue( bool $value ) {
        $this->_value = $value;
    }

    public function getValue() : bool {
        return $this->_value;
    }

}
