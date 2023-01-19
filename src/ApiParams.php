<?php

namespace JohnRDOrazio\SimpleAPI;

if( class_exists("\Composer\Autoload\ClassLoader") )
{
    use JohnRDOrazio\SimpleAPI\Enums\ResponseType;
} else {
    // composer autoload.php has not been included/required
    include_once( 'Enums/ResponseType.php' );
}



class ApiParams {
    //define a public class property for each of your supported API parameters
    //and define here any default values that might be used/returned if the parameters are not set in the request
    public ?string $Param1      = null;
    public int     $Param2      = 0;
    public ?string $ResponseType = null;
  
    const ALLOWED_PARAMS  = [
      //list your supported API parameters here exactly as they should be received by the API,
      //in their string format
      "PARAM_ONE",
      "PARAM_TWO",
      "RESPONSETYPE" //if you want to allow requesting a certain response content type by parameter other than by the Accept header
    ];

    public function __construct( array $DATA ) {

        foreach( $DATA as $key => $value ) {
            //if you defined your supported API Params as uppercase strings in the ALLOWED_PARAMS constant,
            //then you'll want to make sure we check against the uppercase form,
            //so we operate the transformation here
            $key = strtoupper( $key );
            if( in_array( $key, self::ALLOWED_PARAMS ) ){
                switch( $key ){
                    case "PARAM_ONE":
                        $this->Param1 = $this->enforceParam1Validity( $value );
                        break;
                    case "PARAM_TWO":
                        $this->Param2 = $this->enforceParam2Validity( $value );
                        break;
                    case "RESPONSETYPE":
                        $this->ResponseType = ResponseType::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : null;
                        break;
                }
            }
        }

    }

    private function enforceParam1Validity( string|null $value ) : string|null {
        if( gettype($value) === 'string' ) {
            return strip_tags( $value );
        }
        return null;
    }

    private function enforceParam2Validity( int|null $value ) : int {
        if( gettype( $value ) === 'integer' ) {
            if( $value > 10 ) {
                $value = 10;
            }
            return $value;
        }
        return 0;
    }

    public function setResponseType( string $value ) : void {
        $this->ResponseType = $value;
    }
}
