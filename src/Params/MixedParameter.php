<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class MixedParameter {
    private mixed $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : mixed {
        if( gettype($value) === 'string' ) {
            $value = strip_tags( $value );
        }
        return $value;
    }

    public function setValue( mixed $value ) {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : mixed {
        return $this->_value;
    }

}
