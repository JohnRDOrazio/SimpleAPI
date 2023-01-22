<?php

namespace JohnRDOrazio\SimpleAPI\Enums;

/**
 * enum ParamType
 * Represents possible Types for API Parameters
 */

class ParamType {
    const STRING    = 'string';
    const INTEGER   = 'int';
    const FLOAT     = 'float';
    const BOOLEAN   = 'bool';
    const ARRAY     = 'array';
    const OBJECT    = 'object';
    const NULL      = 'null';
    const MIXED     = 'mixed';
//    const TRUE      = 'true';  //can be uncommented once PHP 8.1 becomes the minimum version supported
//    const FALSE     = 'false'; //can be uncommented once PHP 8.1 becomes the minimum version supported
    const RESPONSETYPE = 'responseType'; //special type proper to the SimpleAPI

    private static array $types = [
        'string',
        'int',
        'float',
        'bool',
        'array',
        'object',
        'null',
        'mixed',
        //'true',
        //'false',
        'responseType'
    ];

    public static function isValid( $type ) : bool {
        return in_array( $type, self::$types );
    }

}
