<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class TrueParameter {
    private bool $_value; //from PHP 8.1, we can use true as a type of it's own

    //public function __construct() {}

    public function setValue( bool $value ) {
        $this->_value = $value;
    }

    public function getValue() : bool {
        return $this->_value;
    }

}
