<?php

namespace JohnRDOrazio\SimpleAPI\Enums;

/**
 * enum AcceptHeader
 * Represents all possible Accept Headers
 * that the API might receive from a Request
 * and define the corresponding Content Type for the Response
 * and the corresponding file extensions for the cached response files
 */

class AcceptHeader {
    const ATTACHMENT= "application/octet-stream";
    const JSON      = "application/json";
    const XML       = "application/xml";
    const PDF       = "application/pdf";
    const HTML      = "text/html";
    const ICS       = "text/calendar";
    const TEXT      = "text/plain";
    const CSV       = "text/csv";
    const CSS       = "text/css";
    const JS        = "text/javascript";
    const MPEG      = "audio/mpeg";
    const VORBIS    = "audio/vorbis";
    const OGG       = "audio/ogg";
    const WEBM      = "audio/webm";
    const JPG       = "image/jpeg";
    const PNG       = "image/png";
    const APNG      = "image/apng";
    const AVIF      = "image/avif";
    const GIF       = "image/gif";
    const SVG       = "image/svg+xml";
    const WEBP      = "image/webp";
    const MP4       = "video/mp4";
    const VIDEO_OGG = "video/ogg";
    const VIDEO_WEBM= "video/webm";

    public static array $values = [
        "ATTACHMENT"=> "application/octet-stream",
        "JSON"      => "application/json",
        "XML"       => "application/xml",
        "PDF"       => "application/pdf",
        "HTML"      => "text/html",
        "ICS"       => "text/calendar",
        "TEXT"      => "text/plain",
        "CSV"       => "text/csv",
        "CSS"       => "text/css",
        "JS"        => "text/javascript",
        "MPEG"      => "audio/mpeg",
        "VORBIS"    => "audio/vorbis",
        "OGG"       => "audio/ogg",
        "WEBM"      => "audio/webm",
        "JPG"       => "image/jpeg",
        "PNG"       => "image/png",
        "APNG"      => "image/apng",
        "AVIF"      => "image/avif",
        "GIF"       => "image/gif",
        "SVG"       => "image/svg+xml",
        "WEBP"      => "image/webp",
        "MP4"       => "video/mp4",
        "VIDEO_OGG" => "video/ogg",
        "VIDEO_WEBM"=> "video/webm"
    ];
    
    public static array $fileExt = [
        "application/octet-stream"  => "blob",
        "application/json"          => "json",
        "application/xml"           => "xml",
        "application/pdf"           => "pdf",
        "text/html"                 => "html",
        "text/calendar"             => "ics",
        "text/plain"                => "txt",
        "text/csv"                  => "csv",
        "text/css"                  => "css",
        "text/javascript"           => "js",
        "audio/mpeg"                => "mpg",
        "audio/vorbis"              => "vorbis",
        "audio/ogg"                 => "ogg",
        "audio/webm"                => "webm",
        "image/jpeg"                => "jpg",
        "image/png"                 => "png",
        "image/apng"                => "apng",
        "image/avif"                => "avif",
        "image/gif"                 => "gif",
        "image/svg+xml"             => "svg",
        "image/webp"                => "webp",
        "video/mp4"                 => "mp4",
        "video/ogg"                 => "ogg",
        "video/webm"                => "webm"
    ];

    public static function isValid( string $value ) : bool {
        return in_array( $value, self::$values );
    }

    public static function fromResponseType( string $responseType ) : string|false {
        if( array_key_exists( $responseType, self::$values ) ) {
            return self::$values[ $responseType ];
        }
        return false;
    }

    public static function toFileExt( string $mimeType ) : string|false {
        if( array_key_exists( $mimeType, self::$fileExt ) ) {
            return self::$fileExt[ $mimeType ];
        }
        return false;
    }
}
