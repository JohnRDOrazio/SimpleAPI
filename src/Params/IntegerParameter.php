<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class IntegerParameter {
    private int $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : int {
        if( gettype($value) !== 'integer' ) {
            $value = filter_var( $value, FILTER_VALIDATE_INT );
        }
        return $value;
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : int {
        return $this->_value;
    }

}
