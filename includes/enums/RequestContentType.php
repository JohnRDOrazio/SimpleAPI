<?php
/**
 * enum RequestContentType
 * Represents all possible Content Types
 * for a Request that the API might receive
 */
 
class RequestContentType {
    const JSON      = "application/json";
    const FORMDATA  = "application/x-www-form-urlencoded";
    const MULTIPARTFORMDATA = "multipart/form-data";

    public static array $values = [
        "application/json",
        "application/x-www-form-urlencoded",
        "multipart/form-data"
    ];

    public static function isValid( $value ) {
        return in_array( $value, self::$values );
    }
}
