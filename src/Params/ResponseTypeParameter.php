<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class ResponseTypeParameter {
    private ?string $_value = null;

    //public function __construct() {}

    public function setValue( ?string $value ) {
        $this->_value = $value;
    }

    public function getValue() : ?string {
        return $this->_value;
    }

}
