<?php

class CacheDuration {
    const MINUTE    = "MINUTE";
    const HOUR      = "HOUR";
    const DAY       = "DAY";
    const WEEK      = "WEEK";
    const MONTH     = "MONTH";
    const YEAR      = "YEAR";
    public static array $values = [ "MINUTE", "HOUR", "DAY", "WEEK", "MONTH", "YEAR" ];

    public static function isValid( $value ) {
        return in_array( $value, self::$values );
    }
}
