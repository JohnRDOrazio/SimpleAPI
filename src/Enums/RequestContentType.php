<?php

namespace JohnRDOrazio\SimpleAPI\Enums;

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

    public static function isValid( string $value ): bool {
        return in_array( $value, self::$values );
    }

    public static function areValid( array $values ): bool {
        return empty( array_diff( $values, self::$values ) );
    }
}
