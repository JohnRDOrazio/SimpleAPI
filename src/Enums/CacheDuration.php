<?php

namespace JohnRDOrazio\SimpleAPI\Enums;

/**
 * enum CacheDuration
 * Helps define / configure the duration for cached responses
 */

class CacheDuration {
    const MINUTE    = "MINUTE";
    const HOUR      = "HOUR";
    const DAY       = "DAY";
    const WEEK      = "WEEK";
    const MONTH     = "MONTH";
    const YEAR      = "YEAR";
    const INFINITE      = "INFINITE";
    public static array $values = [ "MINUTE", "HOUR", "DAY", "WEEK", "MONTH", "YEAR", "INFINITE" ];

    public static function isValid( string $value ): bool {
        return in_array( $value, self::$values );
    }

    public static function toSeconds( string $value ): int {
        switch( $value ) {
            case CacheDuration::MINUTE:
                return 60;
            case CacheDuration::HOUR:
                return 60 * 60;
            case CacheDuration::DAY:
                return 60 * 60 * 24;
            case CacheDuration::WEEK:
                return 60 * 60 * 24 * 7;
            case CacheDuration::MONTH:
                return 60 * 60 * 24 * 30;
            case CacheDuration::YEAR:
                return 60 * 60 * 24 * 365;
            default:
                return 0;
        }
    }
}
