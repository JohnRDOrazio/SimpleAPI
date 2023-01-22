<?php

namespace JohnRDOrazio\SimpleAPI;

use JohnRDOrazio\SimpleAPI\Enums\AcceptHeader;
use JohnRDOrazio\SimpleAPI\Enums\ResponseType;
use JohnRDOrazio\SimpleAPI\Enums\CacheDuration;
use JohnRDOrazio\SimpleAPI\Config;

class SimpleAPI {

    const VERSION                                   = '0.1.1';
    private array $AllowedOrigins;
    private array $AllowedReferers;
    private array $AllowedAcceptHeaders;
    private array $AllowedRequestMethods;
    private array $AllowedRequestContentTypes;
    private array $AllowedResponseTypes;
    private array $RequestHeaders;
    private string|false $JsonEncodedRequestHeaders = '';
    private ?string $RequestContentType             = null;
    private ?string $ResponseContentType            = null;
    private string $DefaultResponseContentType      = '';
    private ?string $CacheDuration                  = null;
    private ?string $CacheFile                      = null;
    private ?string $CacheFilePath                  = null;

    public function __construct() {
        Config::LoadConfigs();
        $this->AllowedOrigins                   = ALLOWED_ORIGINS;
        $this->AllowedReferers                  = ALLOWED_REFERERS;
        $this->AllowedAcceptHeaders             = ALLOWED_ACCEPT_HEADERS;
        $this->AllowedRequestMethods            = ALLOWED_REQUEST_METHODS;
        $this->AllowedRequestContentTypes       = ALLOWED_REQUEST_CONTENT_TYPES;
        $this->AllowedResponseTypes             = array_map(fn($mimeType): string => ResponseType::fromMimeType($mimeType), ALLOWED_ACCEPT_HEADERS);
        $this->DefaultResponseContentType       = DEFAULT_MIME_TYPE;
        $this->RequestHeaders                   = getallheaders();
        $this->JsonEncodedRequestHeaders        = json_encode( $this->RequestHeaders );
        if( isset( $_SERVER[ 'CONTENT_TYPE' ] ) ) {
            $this->RequestContentType = $_SERVER[ 'CONTENT_TYPE' ];
        }
    }

    private function setAllowedOriginHeader() {
        if( count( $this->AllowedOrigins ) === 1 && $this->AllowedOrigins[ 0 ] === "*" ) {
            header( 'Access-Control-Allow-Origin: *' );
        }
        elseif( $this->isAllowedOrigin() ) {
            header( 'Access-Control-Allow-Origin: ' . $this->RequestHeaders[ "Origin" ] );
        }
        else {
            header( "Access-Control-Allow-Origin: {$_SERVER['HTTP_HOST']}" );
        }
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Max-Age: 86400' );    // cache for 1 day
    }

    private function setAccessControlAllowMethods() {
        if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
            if ( isset( $_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_METHOD' ] ) )
                header( "Access-Control-Allow-Methods: " . implode(',', $this->AllowedRequestMethods) );
            if ( isset( $_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' ] ) )
                header( "Access-Control-Allow-Headers: {$_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' ]}" );
        }
    }

    private function validateRequestContentType() {
        if( isset( $_SERVER[ 'CONTENT_TYPE' ] ) && $_SERVER[ 'CONTENT_TYPE' ] !== '' && !in_array( explode( ';', $_SERVER[ 'CONTENT_TYPE' ] )[ 0 ], $this->AllowedRequestContentTypes ) ){
            header( $_SERVER[ "SERVER_PROTOCOL" ]." 415 Unsupported Media Type", true, 415 );
            die( '{"error":"You seem to be forming a strange kind of request? Allowed Content Types are '.implode( ' and ', $this->AllowedRequestContentTypes ).', but your Content Type was '.$_SERVER[ 'CONTENT_TYPE' ].'"}' );
        }
    }

    private function sendHeaderNotAcceptable() : void {
        header( $_SERVER[ "SERVER_PROTOCOL" ]." 406 Not Acceptable", true, 406 );
        $errorMessage = '{"error":"You are requesting a content type which this API cannot produce. Allowed Accept headers are ';
        $errorMessage .= implode( ' and ', $this->AllowedAcceptHeaders );
        $errorMessage .= ', but you have issued an request with an Accept header of ' . $this->RequestHeaders[ "Accept" ] . '"}';
        die( $errorMessage );
    }

    public function validateAcceptHeader() {
        if( $this->hasAcceptHeader() ) {
            $acceptHeaders = explode( ",", $this->RequestHeaders[ "Accept" ] );
            foreach($acceptHeaders as $acceptHeader) {
                if( $this->isAllowedAcceptHeader( $acceptHeader ) ) {
                    $this->ResponseContentType = $acceptHeader;
                    break;
                }
            }
            if( null === $this->ResponseContentType ) {
                if( RELAX_FOR_TEXT_TYPE_REQUESTS ) {
                    //Requests from browser windows using the address bar will probably have an Accept header of text/html
                    //In order to not be too drastic, let's treat text/html as though it were application/json for GET and POST requests only
                    if( in_array( 'text/html', $acceptHeaders ) || in_array( 'text/plain', $acceptHeaders ) || in_array( '*/*', $acceptHeaders ) ) {
                        $this->ResponseContentType = $this->DefaultResponseContentType;
                    } else {
                        $this->sendHeaderNotAcceptable();
                    }
                } else {
                    $this->sendHeaderNotAcceptable();
                }
            }
        } else {
            $this->ResponseContentType = $this->DefaultResponseContentType;
        }
    }

    public function setResponseContentTypeHeader() : void {
        header( 'Cache-Control: must-revalidate, max-age=259200' ); //cache for 1 month
        header( "Content-Type: {$this->ResponseContentType}; charset=utf-8" );
    }

    public function getAllowedAcceptHeaders() : array {
        return $this->AllowedAcceptHeaders;
    }

    public function getAllowedResponseTypes() : array {
        return $this->AllowedResponseTypes;
    }

    public function getAllowedRequestContentTypes() : array {
        return $this->AllowedRequestContentTypes;
    }

    public function getAllowedOrigins() : array {
        return $this->AllowedOrigins;
    }

    public function getCacheDuration() : ?string {
        return CACHE_DURATION;
    }

    public function getCacheFile() : ?string {
        return $this->CacheFile;
    }

    public function getCacheFolderName() : string {
        return CACHE_FOLDER_NAME;
    }

    public function getRelaxForTextTypeRequests() : bool {
        return RELAX_FOR_TEXT_TYPE_REQUESTS;
    }

    public function getForceAjaxRequest() : bool {
        return FORCE_AJAX_REQUEST;
    }

    public function getDefaultResponseContentType() : string {
        return $this->DefaultResponseContentType;
    }

    public function getAcceptHeader() : string {
        return $this->RequestHeaders[ "Accept" ];
    }

    public function getResponseTypeFromResponseContentType() : string|null {
        $idx = array_search( $this->ResponseContentType, $this->AllowedAcceptHeaders );
        if( $idx !== false ) {
            return $this->AllowedResponseTypes[ $idx ];
        } else {
            return $this->DefaultResponseContentType;
        }
        return null;
    }

    public function hasAcceptHeader() : bool {
        return isset( $this->RequestHeaders[ "Accept" ] );
    }

    public function isAjaxRequest() : bool {
        return ( !isset($_SERVER['HTTP_X_REQUESTED_WITH'] ) || empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) || strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) != 'xmlhttprequest' ) === false;
    }

    private function enforceAjaxRequest() : void {
        if( false === $this->isAjaxRequest() ) {
            header( $_SERVER[ "SERVER_PROTOCOL" ]." 418 I'm a teapot", true, 418 );
            $errorMessage = '{"error":"Request was not made via AJAX. When using Request Method ' . strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) . ', only AJAX requests from authorized Origins and Referers are processable."}';
            die( $errorMessage );
        }
    }

    public function isAllowedAcceptHeader( string $acceptHeader ) : bool {
        //return in_array( explode( ',', $this->RequestHeaders[ "Accept" ] )[0], $this->AllowedAcceptHeaders );
        return in_array( $acceptHeader, $this->AllowedAcceptHeaders );
    }

    public function isAllowedOrigin() : bool {
        return isset( $this->RequestHeaders[ "Origin" ] ) && in_array( $this->RequestHeaders[ "Origin" ], $this->AllowedOrigins );
    }

    public function isAllowedReferer() : bool {
        if( count($this->AllowedReferers) === 1 && $this->AllowedReferers[0] === "*" ) {
            return true;
        } else {
            return in_array( $_SERVER["HTTP_REFERER"], $this->AllowedReferers );
        }
    }

    public function getAllowedRequestMethods() : array {
        return $this->AllowedRequestMethods;
    }

    public function getRequestMethod() : string {
        return strtoupper( $_SERVER[ 'REQUEST_METHOD' ] );
    }

    public function getRequestHeaders() : array {
        return $this->RequestHeaders;
    }

    public function getJsonEncodedRequestHeaders() : string {
        return $this->JsonEncodedRequestHeaders;
    }

    public function getRequestContentType() : ?string {
        return $this->RequestContentType;
    }

    public function getResponseContentType() : ?string {
        return $this->ResponseContentType;
    }

    public function getAllowedReferers() : array {
        return $this->AllowedReferers;
    }

    public function enforceReferer() : void {
        if( false === $this->isAllowedReferer() ) {
            header( $_SERVER[ "SERVER_PROTOCOL" ]." 401 Unauthorized", true, 401 );
            $errorMessage = '{"error":"Request is coming from unauthorized referer ' . $_SERVER["HTTP_REFERER"] . '. Only AJAX requests from authorized Referers are processable."}';
            die( $errorMessage );
        }
    }

    public function validateResponseTypeParam( string $ResponseTypeParam ) {
        if( in_array( $ResponseTypeParam, $this->AllowedResponseTypes ) ) {
            $this->ResponseContentType = $this->AllowedAcceptHeaders[ array_search( $ResponseTypeParam, $this->AllowedResponseTypes ) ];
        } else {
            header( $_SERVER[ "SERVER_PROTOCOL" ]." 406 Not Acceptable", true, 406 );
            $errorMessage = '{"error":"You are requesting a content type which this API cannot produce. Allowed content types are ';
            $errorMessage .= implode( ' and ', $this->AllowedResponseTypes );
            $errorMessage .= ', but you have issued a parameter requesting a Content Type of ' . strtoupper( $ResponseTypeParam ) . '"}';
            die( $errorMessage );
        }
    }

    public function retrieveRequestParamsFromJsonBody() : object {
        $json = file_get_contents( 'php://input' );
        $data = json_decode( $json );
        if( "" === $json ){
            header( $_SERVER[ "SERVER_PROTOCOL" ]." 400 Bad Request", true, 400 );
            die( '{"error":"No JSON data received in the request"' );
        } else if ( json_last_error() !== JSON_ERROR_NONE ) {
            header( $_SERVER[ "SERVER_PROTOCOL" ]." 400 Bad Request", true, 400 );
            die( '{"error":"Malformed JSON data received in the request: <' . $json . '>, ' . json_last_error_msg() . '"}' );
        }
        return $data;
    }

    /**
     * Function setCacheDuration
     * Returns a string that will be appended to the Cache File Name
     *   which represents the desired cache duration associated with the file
     *   by calculating current time against the start of the unix epoch
     */
    public function setCacheDuration() : void {
        $secondsSinceUnixEpoch  = time();
        $minutesSinceUnixEpoch  = floor( $secondsSinceUnixEpoch    / 60 );
        $hoursSinceUnixEpoch    = floor( $minutesSinceUnixEpoch    / 60 );
        $daysSinceUnixEpoch     = floor( $hoursSinceUnixEpoch      / 24 );
        $weeksSinceUnixEpoch    = floor( $daysSinceUnixEpoch       / 7  );
        $monthsSinceUnixEpoch   = floor( $daysSinceUnixEpoch       / 30 );
        switch( CACHE_DURATION ) {
            case CacheDuration::INFINITE:
                $this->CacheDuration = "";
                break;
            case CacheDuration::MINUTE:
                $this->CacheDuration = "_" . CACHE_DURATION . "_" . $minutesSinceUnixEpoch;
                break;
            case CacheDuration::HOUR:
                $this->CacheDuration = "_" . CACHE_DURATION . "_" . $hoursSinceUnixEpoch;
                break;
            case CacheDuration::DAY:
                $this->CacheDuration = "_" . CACHE_DURATION . "_" . $daysSinceUnixEpoch;
                break;
            case CacheDuration::WEEK:
                $this->CacheDuration = "_" . CACHE_DURATION . "_" . $weeksSinceUnixEpoch;
                break;
            case CacheDuration::MONTH:
                $this->CacheDuration = "_" . CACHE_DURATION . "_" . $monthsSinceUnixEpoch;
                break;
            case CacheDuration::YEAR:
                $this->CacheDuration = "_" . CACHE_DURATION . "_" . date( "Y" ); //A full numeric representation of the year (4 digits) of the request
                break;
            default:
                $this->CacheDuration = null;
        }
    }

    private function determineCacheFile( APIParams $apiParams, string $apiVersion = "" ) : ?string {
        $this->CacheFilePath = CACHE_FOLDER_NAME . "/v" . str_replace( ".", "_", $apiVersion );
        if( $this->CacheDuration !== null ) {
            $cacheFileExtension = $apiParams->getResponseType() !== null ? ResponseType::toFileExt( $apiParams->getResponseType() ) : AcceptHeader::toFileExt( $this->DefaultResponseContentType );
            $cacheFileName = md5( serialize( $apiParams) ) . $this->CacheDuration . "." . $cacheFileExtension;
            return $this->CacheFilePath . "/" . $cacheFileName;
        }
        return null;
    }

    public function writeResponseToCacheFile( string|bool $response ) {
        if( $this->CacheDuration !== null ) {
            //we make sure we have a Cache folder for the current Version, if we have enabled cached responses
            if( realpath( $this->CacheFilePath ) === false ) {
                mkdir( $this->CacheFilePath, 0755, true );
            }
            if( gettype($response) === 'string' && $this->CacheFile !== null ) {
                file_put_contents( $this->CacheFile, $response );
            }
        }
    }

    /**
     * Function getCacheFileIsAvailable
    */
    public function getCacheFileIfAvailable( APIParams $apiParams, string $apiVersion = "" ) : ?string {
        $this->CacheFile = $this->determineCacheFile( $apiParams, $apiVersion );
        if( $this->CacheFile !== null && file_exists( $this->CacheFile ) ) {
            return file_get_contents( $this->CacheFile );
        }
        return null;
    }

    public function Init() {
        $this->setAllowedOriginHeader();
        $this->setAccessControlAllowMethods();
        $this->validateRequestContentType();
        $this->validateAcceptHeader();
        if( FORCE_AJAX_REQUEST ) {
            $this->enforceAjaxRequest();
        }
        $this->enforceReferer();
        $this->setCacheDuration();
    }

}
