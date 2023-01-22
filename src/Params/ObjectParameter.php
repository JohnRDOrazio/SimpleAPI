<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class ObjectParameter {
    private object $_value;

    //public function __construct() {}

    public function setValue( object $value ) {
        $this->_value = $value;
    }

    public function getValue() : object {
        return $this->_value;
    }

}
