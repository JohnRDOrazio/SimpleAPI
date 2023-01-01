<?php

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

    public static function isValid( string $value ) : bool {
        return in_array( $value, self::$values );
    }
    
    public static function fromResponseType( string $value ) : string|false {
        return array_search( $value, self::$values );
    }
}
