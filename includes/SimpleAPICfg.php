<?php
/**
 * Search for the user's config file both in the same level
 * and one level up
 */

if( file_exists( 'config.php' ) ) {
    require_once( "config.php" );
}
else if( file_exists( '../config.php' ) ) {
    require_once( "../config.php" );
} else if (
    ( file_exists( 'config.sample.php' ) || file_exists( '../config.sample.php' ) )
    &&
    ( !file_exists( 'config.php' ) || !file_exists( '../config.php' ) )
  ) {
    header( "Content-Type: text/html; charset=utf-8" );
    $html = '<h3>Welcome to SimpleAPI!</h3>';
    $html .= '<p>You should really set your <b><i>config.php</i></b> before trying to use SimpleAPI!</p>';
    $html .= '<p>You may use the included <b><i>config.sample.php</i></b> to start with.</p>';
    die( $html );
}

/** 
 * Here we make sure that every constant needed gets set,
 * even if not set in the user's config file
 */
$cfgDflt = [];
$cfgDflt["ALLOWED_REQUEST_METHODS"] = [
    "GET",
    "POST",
    "OPTIONS"
];
$cfgDflt["ALLOWED_REQUEST_CONTENT_TYPES"] = [
    "application/x-www-form-urlencoded",
    "multipart/form-data",
    "application/json"
];
$cfgDflt["ALLOWED_ACCEPT_HEADERS"] = [
    "application/json",
    "application/xml",
];
$cfgDflt["DEFAULT_MIME_TYPE"] = "application/json";
$cfgDflt["RELAX_FOR_TEXT_TYPE_REQUESTS"] = true;
$cfgDflt["ALLOWED_RESPONSE_TYPES"] = [
    "JSON",
    "XML",
];
$cfgDflt["CACHE_FOLDER_NAME"] = "apiCache";
$cfgDflt["CACHE_DURATION"] = "MONTH";
$cfgDflt["ALLOWED_ORIGINS"] = [ "*" ];
$cfgDflt["ALLOWED_REFERERS"] = [ "*" ];
$cfgDflt["FORCE_AJAX_REQUEST"] = false;
$cfgDflt["GITHUB_REPO_USER"] = "JohnRDOrazio";
$cfgDflt["GITHUB_REPO_NAME"] = "SimpleAPI";

foreach( $cfgDflt as $key => $value ) {
    if(!defined($key) ) {
        define( $key, $value );
    }
}
