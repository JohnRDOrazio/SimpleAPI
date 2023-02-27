<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class BooleanParameter {
    private bool $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : bool {
        if( gettype($value) !== 'boolean' ) {
            $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
        }
        return $value;
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : bool {
        return $this->_value;
    }

}
