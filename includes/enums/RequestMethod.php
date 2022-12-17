<?php
/**
 * enum RequestMethod
 * Represents all possible Request Methods
 * for a Request that the API might receive
 */

class RequestMethod {
    const GET       = "GET";    //Read
    const POST      = "POST";   //Create / read
    const PATCH     = "PATCH";  //Update / modify
    const PUT       = "PUT";    //Update / replace
    const DELETE    = "DELETE"; //Delete
    const OPTIONS   = "OPTIONS";//used in http2 prefetch requests, for detecting supported request methods
    const HEAD      = "HEAD";   //The HEAD method asks for a response identical to a GET request, but without the response body
    const CONNECT   = "CONNECT";//The CONNECT method establishes a tunnel to the server identified by the target resource
    const TRACE     = "TRACE";  //The TRACE method performs a message loop-back test along the path to the target resource
                                //The TRACE method is not currently supported by any browser! See https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods

    public static array $values = [ "GET", "POST", "PATCH", "PUT", "DELETE", "OPTIONS", "HEAD", "CONNECT", "TRACE" ];

    public static function isValid( $value ) {
        return in_array( $value, self::$values );
    }
}
