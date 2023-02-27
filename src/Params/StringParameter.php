<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class StringParameter {
    private string $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : string {
        if( gettype($value) !== 'string' ) {
            $value = (string) $value;
        }
        return strip_tags( $value );
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : string {
        return $this->_value;
    }

}
