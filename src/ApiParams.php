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

    private array $params = [];
    private ?string $responseType = null;

    public function setValues( array $DATA ) {

        foreach( $DATA as $param => $value ) {
            if( false === ENFORCE_PARAMETER_CASE ) {
                $param = strtolower( $param );
            }
            if( array_key_exists( $param, $this->params ) ) {
                $this->params[ $param ]->setValue( $value );
                if( $this->params[ $param ] instanceof ResponseTypeParameter ) {
                    $this->responseType = $this->params[ $param ]->getValue();
                }
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 400 Bad Request", true, 400 );
                header('Content-Type: text/html', true);
                $response = "Cannot fulfill this request, parameter {$param} does not seem to be a supported parameter?<br>Supported parameters are: ";
                $params = array_reduce(array_keys( $this->params ), function( array $carry, string $item ) {
                    if( $this->params[$item] instanceof StringParameter ) {
                        $carry[$item] = "$item (string)";
                    }
                    else if( $this->params[$item] instanceof IntegerParameter ) {
                        $carry[$item] = "$item (int)";
                    }
                    else if( $this->params[$item] instanceof FloatParameter ) {
                        $carry[$item] = "$item (float)";
                    }
                    else if( $this->params[$item] instanceof BooleanParameter ) {
                        $carry[$item] = "$item (bool)";
                    }
                    else if( $this->params[$item] instanceof NullParameter ) {
                        $carry[$item] = "$item (null)";
                    }
                    else if( $this->params[$item] instanceof ArrayParameter ) {
                        $carry[$item] = "$item (array)";
                    }
                    else if( $this->params[$item] instanceof ObjectParameter ) {
                        $carry[$item] = "$item (object)";
                    }
                    else if( $this->params[$item] instanceof MixedParameter ) {
                        $carry[$item] = "$item (mixed)";
                    }
                    else if( $this->params[$item] instanceof ResponseTypeParameter ) {
                        $carry[$item] = "$item (enum) [" . implode(', ', ResponseType::$values ) . "]";
                    }
                    return $carry;
                }, []);
                $response .= "<br> - " . implode('<br> - ', array_values( $params ) );
                die( $response );
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
                    $this->params[$param] = new StringParameter( $param );
                break;
                case ParamType::INTEGER:
                    $this->params[$param] = new IntegerParameter( $param );
                break;
                case ParamType::FLOAT:
                    $this->params[$param] = new FloatParameter( $param );
                break;
                case ParamType::BOOLEAN:
                    $this->params[$param] = new BooleanParameter( $param );
                break;
                case ParamType::ARRAY:
                    $this->params[$param] = new ArrayParameter( $param );
                break;
                case ParamType::OBJECT:
                    $this->params[$param] = new ObjectParameter( $param );
                break;
                case ParamType::NULL:
                    $this->params[$param] = new NullParameter( $param );
                break;
                case ParamType::MIXED:
                    $this->params[$param] = new MixedParameter( $param );
                break;
                //from PHP 8.1 we will be able to use true and false types on their own
                // case ParamType::TRUE:
                //     $this->params[$param] = new TrueParameter( $param );
                // break;
                // case ParamType::FALSE:
                //     $this->params[$param] = new FalseParameter( $param );
                // break;
                case ParamType::RESPONSETYPE:
                    $this->params[$param] = new ResponseTypeParameter( $param );
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
        return $this->params[$param]->getValue();
    }

    public function setResponseType( string $value ) : void {
        $this->responseType = $value;
    }

    public function getResponseType() : ?string {
        return $this->responseType;
    }

    public function getAll() : array {
        return array_reduce(
            array_keys($this->params),
            function(array $carry, string $key) { $carry[$key] = $this->params[$key]->getValue(); return $carry; },
            []
        );
    }

    public function areDefined(): bool {
        return false === empty( array_keys($this->params) );
    }
}
