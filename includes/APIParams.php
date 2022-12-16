<?php

include_once( 'enums/ResponseType.php' );

class APIParams {
    //define a public class property for each of your supported API parameters
    //example:
    //public ?string $Param1      = null;
    //public int     $Param2;
    //public ?string $ReponseType = null;
  
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
                    case "PARAM1":
                        $this->enforceParam1Validity( $value );
                        break;
                    case "PARAM2":
                        $this->enforceParam1Validity( $value );
                        break;
                    case "RESPONSETYPE":
                        $this->ResponseType       = ResponseType::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : ResponseType::JSON;
                        break;
                }
            }
        }

    }
}
