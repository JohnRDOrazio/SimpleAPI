<?php

/**
 * class ResponseType
 * Represents possible Content Types for the Response
 *  as indicated in the request's Accept header
 *  (or possibly in a request paramter)
 * The $values array must follow exactly the $values array
 *  in the AcceptHeader class, so that conversions can be made
 */

class ResponseType {
    const ATTACHMENT    = "ATTACHMENT";
    const JSON          = "JSON";
    const XML           = "XML";
    const PDF           = "PDF";
    const HTML          = "HTML";
    const ICS           = "ICS";
    const TEXT          = "TEXT";
    const CSV           = "CSV";
    const CSS           = "CSS";
    const JS            = "JS";
    const MPEG          = "MPEG";
    const VORBIS        = "VORBIS";
    const OGG           = "OGG";
    const WEBM          = "WEBM";
    const JPG           = "JPG";
    const PNG           = "PNG";
    const APNG          = "APNG";
    const AVIF          = "AVIF";
    const GIF           = "GIF";
    const SVG           = "SVG";
    const WEBP          = "WEBP";
    const MP4           = "MP4";
    const VIDEO_OGG     = "VIDEO_OGG";
    const VIDEO_WEBM    = "VIDEO_WEBM";

    public static array $values = [
        "application/octet-stream"  => "ATTACHMENT",
        "application/json"          => "JSON",
        "application/xml"           => "XML",
        "application/pdf"           => "PDF",
        "text/html"                 => "HTML",
        "text/calendar"             => "ICS",
        "text/plain"                => "TEXT",
        "text/csv"                  => "CSV",
        "text/css"                  => "CSS",
        "text/javascript"           => "JS",
        "audio/mpeg"                => "MPEG",
        "audio/vorbis"              => "VORBIS",
        "audio/ogg"                 => "OGG",
        "audio/webm"                => "WEBM",
        "image/jpeg"                => "JPG",
        "image/png"                 => "PNG",
        "image/apng"                => "APNG",
        "image/avif"                => "AVIF",
        "image/gif"                 => "GIF",
        "image/svg+xml"             => "SVG",
        "image/webp"                => "WEBP",
        "video/mp4"                 => "MP4",
        "video/ogg"                 => "VIDEO_OGG",
        "video/webm"                => "VIDEO_WEBM"
    ];

    public static function isValid( $value ) : bool {
        return in_array( $value, self::$values );
    }
    
    public static function fromMimeType( string $mimeType ) : string|false {
        return array_search( $mimeType, self::$values );
    }
}
