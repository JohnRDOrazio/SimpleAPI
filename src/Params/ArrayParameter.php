<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class ArrayParameter {
    private array $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : array {
        if( gettype($value) !== 'array' ) {
            header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
            die( "Cannot fulfill this request, parameter {$this->_name} should be of type array, but it's value in the request was not of type array" );
        }
        return $value;
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : array {
        return $this->_value;
    }

}
