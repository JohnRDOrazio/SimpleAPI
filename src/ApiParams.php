<?php

namespace JohnRDOrazio\SimpleAPI;

use ErrorException;
use JohnRDOrazio\SimpleAPI\Enums\ResponseType;
use JohnRDOrazio\SimpleAPI\Enums\ParamType;
use JohnRDOrazio\SimpleAPI\Params\StringParameter;
use JohnRDOrazio\SimpleAPI\Params\IntegerParameter;
use JohnRDOrazio\SimpleAPI\Params\FloatParameter;
use JohnRDOrazio\SimpleAPI\Params\BooleanParameter;
use JohnRDOrazio\SimpleAPI\Params\ArrayParameter;
use JohnRDOrazio\SimpleAPI\Params\ObjectParameter;
use JohnRDOrazio\SimpleAPI\Params\NullParameter;
use JohnRDOrazio\SimpleAPI\Params\MixedParameter;
//use JohnRDOrazio\SimpleAPI\Params\TrueParameter;
//use JohnRDOrazio\SimpleAPI\Params\FalseParameter;
use JohnRDOrazio\SimpleAPI\Params\ResponseTypeParameter;

class ApiParams {

    private array $_params = [];
    private ?string $_responseType = null;

    public function setValues( array $DATA ) {

        foreach( $DATA as $param => $value ) {
            if( false === ENFORCE_PARAMETER_CASE ) {
                $param = strtolower( $param );
            }
            if( array_key_exists( $param, $this->_params ) ) {
                $this->sanitizeAndSetValue( $param, $value );
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                die( "Cannot fulfill this request, parameter {$param} does not seem to be a supported parameter? Supported parameters are:" . print_r( $this->_params, true ) );
            }
        }

    }

    public function define(string $param, mixed $type): void {
        if( false === ENFORCE_PARAMETER_CASE ) {
            $param = strtolower( $param );
        }
        if( ParamType::isValid( $type ) ) {
            switch ( $type ) {
                case ParamType::STRING:
                    $this->_params[$param] = new StringParameter();
                break;
                case ParamType::INTEGER:
                    $this->_params[$param] = new IntegerParameter();
                break;
                case ParamType::FLOAT:
                    $this->_params[$param] = new FloatParameter();
                break;
                case ParamType::BOOLEAN:
                    $this->_params[$param] = new BooleanParameter();
                break;
                case ParamType::ARRAY:
                    $this->_params[$param] = new ArrayParameter();
                break;
                case ParamType::OBJECT:
                    $this->_params[$param] = new ObjectParameter();
                break;
                case ParamType::NULL:
                    $this->_params[$param] = new NullParameter();
                break;
                case ParamType::MIXED:
                    $this->_params[$param] = new MixedParameter();
                break;
                //from PHP 8.1 we will be able to use true and false types on their own
                // case ParamType::TRUE:
                //     $this->_params[$param] = new TrueParameter();
                // break;
                // case ParamType::FALSE:
                //     $this->_params[$param] = new FalseParameter();
                // break;
                case ParamType::RESPONSETYPE:
                    $this->_params[$param] = new ResponseTypeParameter();
                    $this->_params[$param]->setValue( null );
                break;
            }
        } else {
            throw new ErrorException("API Configuration error while trying to define API Parameter {$param}: {$type} is not a valid type");
        }
    }

    public function __get(string $param): mixed {
        if( false === ENFORCE_PARAMETER_CASE ) {
            $param = strtolower( $param );
        }
        return $this->_params[$param]->getValue();
    }

    private function sanitizeAndSetValue( string $param, mixed $value ) : void {
        if( $this->_params[ $param ] instanceof ArrayParameter ) {
            if( gettype($value) !== 'array' ) {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                die( "Cannot fulfill this request, parameter {$param} should be of type array, but it's value in the request was not of type array" );
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof BooleanParameter ) {
            if( gettype($value) !== 'boolean' ) {
                $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof FloatParameter ) {
            if( gettype($value) !== 'double' ) {
                $value = filter_var( $value, FILTER_VALIDATE_FLOAT );
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof IntegerParameter ) {
            if( gettype($value) !== 'integer' ) {
                $value = filter_var( $value, FILTER_VALIDATE_INT );
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof MixedParameter ) {
            if( gettype($value) === 'string' ) {
                $value = strip_tags( $value );
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof NullParameter ) {
            if( gettype($value) !== 'NULL' ) {
                if( strtolower( $value ) === 'null' ) {
                    $value = null;
                } else {
                    header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                    die( "Cannot fulfill this request, parameter {$param} should be of type null, but it's value in the request was not of type null" );
                }
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof ObjectParameter ) {
            if( gettype($value) !== 'object' ) {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                die( "Cannot fulfill this request, parameter {$param} should be of type object, but it's value in the request was not of type object" );
            }
            $this->_params[ $param ]->setValue( $value );
        }
        else if( $this->_params[ $param ] instanceof ResponseTypeParameter ) {
            $this->_params[ $param ]->setValue( ResponseType::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : null );
            $this->_responseType = $this->_params[ $param ]->getValue();
        }
        else if( $this->_params[ $param ] instanceof StringParameter ) {
            if( gettype($value) !== 'string' ) {
                $value = (string) $value;
            }
            $value = strip_tags( $value );
            $this->_params[ $param ]->setValue( $value );
        }
        /*
        else if( $this->_params[ $param ] instanceof FalseParameter ) {
            if( gettype($value) !== 'boolean' ) {
                $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
            }
            if( $value === false ) {
                $this->_params[ $param ]->setValue( $value );
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                die( "Cannot fulfill this request, parameter {$param} is of type false, but it's value in the request was not of type false" );
            }
        }
        */
        /*
        else if( $this->_params[ $param ] instanceof TrueParameter ) {
            if( gettype($value) !== 'boolean' ) {
                $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
            }
            if( $value === true ) {
                $this->_params[ $param ]->setValue( $value );
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                die( "Cannot fulfill this request, parameter {$param} is of type true, but it's value in the request was not of type true" );
            }
        }
        */
    }

    public function setResponseType( string $value ) : void {
        $this->_responseType = $value;
    }

    public function getResponseType() : ?string {
        return $this->_responseType;
    }

    public function getAll() : array {
        return array_reduce(
            array_keys($this->_params),
            function($carry, $key) { $carry[$key] = $this->_params[$key]->getValue(); return $carry; },
            []
        );
    }
}
