<?php

namespace JohnRDOrazio\SimpleAPI\Params;

use JohnRDOrazio\SimpleAPI\Enums\ResponseType;

class ResponseTypeParameter {
    private ?string $_value = null;
    private string $_name;

    public function __construct( string $name ) {
        $this->_name = $name;
    }

    private function sanitize( mixed $value ) : ?string {
        return is_string( $value ) && ResponseType::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : null;
    }

    public function setValue( mixed $value ) : void {
        $this->_value = $this->sanitize( $value );
    }

    public function getValue() : ?string {
        return $this->_value;
    }

}
