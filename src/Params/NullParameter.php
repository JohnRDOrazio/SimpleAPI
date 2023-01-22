<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class NullParameter {
    private null $_value;

    //public function __construct() {}

    public function setValue( null $value ) {
        $this->_value = $value;
    }

    public function getValue() : null {
        return $this->_value;
    }

}
