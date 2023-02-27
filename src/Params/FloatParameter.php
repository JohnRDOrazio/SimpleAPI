<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class FloatParameter {
    private float $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : float {
        if( gettype($value) !== 'double' ) {
            $value = filter_var( $value, FILTER_VALIDATE_FLOAT );
        }
        return $value;
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : float {
        return $this->_value;
    }

}
