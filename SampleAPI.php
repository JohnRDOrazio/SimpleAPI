<?php
/**
 * This is an example implementation of the SimpleAPI class
 * Change this in your own project to fine tune the SimpleAPI for your own implementation
 * The SimpleAPI does not implement on it's own any features for translation,
 *   these should be taken care of by your own API implementation
 * The SimpleAPI is transparent when it comes to accepted parameters:
 *   your API implementation should define the accepted parameters
*/

include_once( 'includes/APICore.php' );
include_once( 'includes/enums/CacheDuration.php' );
include_once( "includes/APIParams.php" );

class SampleAPI {
    const API_VERSION                               = '3.7';
    public SimpleAPI $SimpleAPI;
    private string $CacheDuration                   = "";
    private string $CACHEFILE                       = "";
    private array $AllowedResponseTypes;

    public function __construct(){
        $this->APICore                              = new APICore();
        $this->CacheDuration                        = "_" . CacheDuration::MONTH . date( "m" );
    }

    private function initParameterData() {
        if ( $this->SimpleAPI->getRequestContentType() === RequestContentType::JSON ) {
            $json = file_get_contents( 'php://input' );
            $data = json_decode( $json, true );
            if( NULL === $json || "" === $json ){
                header( $_SERVER[ "SERVER_PROTOCOL" ]." 400 Bad Request", true, 400 );
                die( '{"error":"No JSON data received in the request: <' . $json . '>"' );
            } else if ( json_last_error() !== JSON_ERROR_NONE ) {
                header( $_SERVER[ "SERVER_PROTOCOL" ]." 400 Bad Request", true, 400 );
                die( '{"error":"Malformed JSON data received in the request: <' . $json . '>, ' . json_last_error_msg() . '"}' );
            } else {
                $this->APISettings = new APIParams( $data );
            }
        } else {
            switch( $this->APICore->getRequestMethod() ) {
                case RequestMethod::POST:
                    $this->LitSettings = new APIParams( $_POST );
                    break;
                case RequestMethod::GET:
                    $this->LitSettings = new APIParams( $_GET );
                    break;
                case RequestMethod::OPTIONS:
                    //continue
                    break;
                default:
                    header( $_SERVER[ "SERVER_PROTOCOL" ]." 405 Method Not Allowed", true, 405 );
                    $errorMessage = '{"error":"You seem to be forming a strange kind of request? Allowed Request Methods are ';
                    $errorMessage .= implode( ' and ', $this->APICore->getAllowedRequestMethods() );
                    $errorMessage .= ', but your Request Method was ' . $this->APICore->getRequestMethod() . '"}';
                    die( $errorMessage );
            }
        }
        if( $this->APIParams->ResponseType !== null ) {
            if( in_array( $this->APIParams->ResponseType, $this->AllowedResponseTypes ) ) {
                $this->SimpleAPI->setResponseContentType( $this->SimpleAPI->getAllowedAcceptHeaders()[ array_search( $this->APIParams->ResponseType, $this->AllowedResponseTypes ) ] );
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ]." 406 Not Acceptable", true, 406 );
                $errorMessage = '{"error":"You are requesting a content type which this API cannot produce. Allowed content types are ';
                $errorMessage .= implode( ' and ', $this->AllowedResponseTypes );
                $errorMessage .= ', but you have issued a parameter requesting a Content Type of ' . strtoupper( $this->APIParams->ResponseType ) . '"}';
                die( $errorMessage );
            }
        } else {
            if( $this->SimpleAPI->hasAcceptHeader() ) {
                if( $this->SimpleAPI->isAllowedAcceptHeader() ) {
                    $this->APIParams->ResponseType = $this->AllowedResponseTypes[ $this->SimpleAPI->getIdxAcceptHeaderInAllowed() ];
                    $this->SimpleAPI->setResponseContentType( $this->SimpleAPI->getAcceptHeader() );
                } else {
                    //Requests from browser windows using the address bar will probably have an Accept header of text/html
                    //In order to not be too drastic, let's treat text/html as though it were application/json
                    $acceptHeaders = explode( ",", $this->SimpleAPI->getAcceptHeader() );
                    if( in_array( 'text/html', $acceptHeaders ) || in_array( 'text/plain', $acceptHeaders ) || in_array( '*/*', $acceptHeaders ) ) {
                        $this->APIParams->ResponseType = ReturnType::JSON;
                        $this->APICore->setResponseContentType( AcceptHeader::JSON );
                    } else {
                        header( $_SERVER[ "SERVER_PROTOCOL" ]." 406 Not Acceptable", true, 406 );
                        $errorMessage = '{"error":"You are requesting a content type which this API cannot produce. Allowed Accept headers are ';
                        $errorMessage .= implode( ' and ', $this->APICore->getAllowedAcceptHeaders() );
                        $errorMessage .= ', but you have issued an request with an Accept header of ' . $this->APICore->getAcceptHeader() . '"}';
                        die( $errorMessage );
                    }

                }
            } else {
                $this->APIParams->ResponseType = $this->AllowedResponseTypes[ 0 ];
                $this->SimpleAPI->setResponseContentType( $this->SimpleAPI->getAllowedAcceptHeaders()[ 0 ] );
            }
        }
    }

}
