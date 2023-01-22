<?php

namespace JohnRDOrazio\SimpleAPI;

/**
 * Search for the user's config file both in the same level
 * and one level up
 */

class Config {

    const DEFAULTS = [
        "ALLOWED_REQUEST_METHODS" => [
            "GET",
            "POST",
            "OPTIONS"
        ],
        "ALLOWED_REQUEST_CONTENT_TYPES" => [
            "application/x-www-form-urlencoded",
            "multipart/form-data",
            "application/json"
        ],
        "ALLOWED_ACCEPT_HEADERS" => [
            "application/json",
            "application/xml",
        ],
        "DEFAULT_MIME_TYPE"             => "application/json",
        "RELAX_FOR_TEXT_TYPE_REQUESTS"  => true,
        "CACHE_FOLDER_NAME"             => "apiCache",
        "CACHE_DURATION"                => "MONTH",
        "ALLOWED_ORIGINS"               => [ "*" ],
        "ALLOWED_REFERERS"              => [ "*" ],
        "FORCE_AJAX_REQUEST"            => false,
        "ENFORCE_PARAMETER_CASE"        => false,
        "GITHUB_REPO_USER"              => "JohnRDOrazio",
        "GITHUB_REPO_NAME"              => "SimpleAPI",
        "XML_PARENT_ELEMENT"            => "SampleAPI",
        "XML_NAMESPACE"                 => "https://myapidomain.com/SimpleAPI",
        "CALENDAR_DEFAULT_TIMEZONE"     => "Europe/Vatican",
        "CALENDAR_DEFAULT_LANGUAGE"     => "en-us",
        "CALENDAR_NAME"                 => "SimpleAPI Calendar"
    ];

    /**
     * Function LoadUserConfiguration
     * Called by LoadConfigs function
     * Searches for the user's configuration file up to three levels
     */

    private static function LoadUserConfiguration() {
        if( file_exists( 'config.php' ) ) {
            require_once( "config.php" );
        }
        else if( file_exists( '../config.php' ) ) {
            require_once( "../config.php" );
        }
        else if( file_exists( '../../config.php' ) ) {
            require_once( "../../config.php" );
        }
        else if (
            ( file_exists( 'config.sample.php' ) || file_exists( '../config.sample.php' ) || file_exists( '../../config.sample.php' ) )
            &&
            ( !file_exists( 'config.php' ) && !file_exists( '../config.php' ) && !file_exists( '../../config.php' ) )
          ) {
            header( "Content-Type: text/html; charset=utf-8" );
            $html = '<h3>Welcome to SimpleAPI!</h3>';
            $html .= '<p>You should really set your <b><i>config.php</i></b> before trying to use SimpleAPI!</p>';
            $html .= '<p>You may use the included <b><i>config.sample.php</i></b> to start with.</p>';
            die( $html );
        }

    }

    /** 
     * Function LoadConfigs
     * Makes sure that every constant needed gets set,
     * even if not set in the user's config file
     */
    public static function LoadConfigs() {

        Config::LoadUserConfiguration();

        foreach( Config::DEFAULTS as $key => $value ) {
            if(!defined($key) ) {
                define( $key, $value );
            }
        }

    }
}
