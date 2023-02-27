<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class TrueParameter {
    private bool $_value; //from PHP 8.1, we can use true as a type of it's own
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : bool { //from PHP 8.1 we can use true as a type rather than bool
        if( gettype($value) !== 'boolean' ) {
            $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
        }
        if( $value !== true ) {
            header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
            die( "Cannot fulfill this request, parameter {$this->_name} is of type true, but it's value in the request was not of type true" );
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
