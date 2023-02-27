<?php

namespace JohnRDOrazio\SimpleAPI\Params;

class NullParameter {
    private null $_value;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : null {
        if( gettype($value) !== 'NULL' ) {
            if( strtolower( $value ) === 'null' ) {
                $value = null;
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                die( "Cannot fulfill this request, parameter {$this->_name} should be of type null, but it's value in the request was not of type null" );
            }
        }
        return $value;
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : null {
        return $this->_value;
    }

}
