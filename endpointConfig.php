<?php

$cfg = []; //do not touch this!

/**
 * ALLOWED_REQUEST_METHODS
 * Set here the request methods that your API will support
 * For any request that uses a non-supported method,
 * the SimpleAPI will return a "405 Method Not Allowed" http status header
 */
$cfg["ALLOWED_REQUEST_METHODS"] = [
    "GET",      //generally used to read an existing resource;
                //  formed by encoding the request parameters in key => value pairs joined by an ampersand
                //  and joining the resulting string to the URL with a "?" question mark
    "POST",     //generally used to either create a new resource, or read an existing resource;
                //  request parameters are set in the body of the request, often with encoding application/x-www-form-urlencoded
                //  however an encoding of application/json can also be explicitly set in the request when using this method
    "OPTIONS",  //used in http2 prefetch requests, for detecting supported request methods
                //  perhaps it isn't even needed here as a "supported" method since it's transparent
                //  however there's no harm in keeping it here
    //"PATCH",    //generally used to update/modify an existing resource
    //"PUT",      //generally used to update/replace an existing resource
    //"DELETE",   //generally used to delete a resource
    //"HEAD",     //The HEAD method asks for a response identical to a GET request, but without the response body
    //"CONNECT",  //The CONNECT method establishes a tunnel to the server identified by the target resource
    //"TRACE"     //The TRACE method performs a message loop-back test along the path to the target resource
                  //  The TRACE method is not currently supported by any browser! See https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
];

/**
 * ALLOWED_REQUEST_CONTENT_TYPES
 * Set here the supported or expected encodings for the request
 * For any request using a non-supported encoding,
 * the SimpleAPI will return a "415 Unsupported Media Type" http status header
 */
$cfg["ALLOWED_REQUEST_CONTENT_TYPES"] = [
    "application/x-www-form-urlencoded",//most common request encoding, used by html forms that use the POST method; 
                                        //  default encoding for ajax / fetch requests that use the POST method
    "multipart/form-data",              //request encoding used by html forms that explicitly set this encoding, 
                                        // in order to include for example a file upload along with the form data
    "application/json",                 //useful encoding for exchanging data in JSON format, must be set explicitly in ajax / fetch requests
                                        //  and the JSON data must be stringified in the request body
    //"text/html",
    //"text/plain"
];

/**
 * ALLOWED_ACCEPT_HEADERS
 * The client formulating the request can let the endpoint know which resource MIME type it would like in the response,
 * by sending Accept header in the request
 * Set here the MIME types that the API will support, and that clients will be able to request
 * For any request that includes a non supported Accept header,
 * the SimpleAPI will return a "406 Not Acceptable" http status header
 * Even if the response type is set via a parameter, this array will still define
 *  the supported response types
 */
$cfg["ALLOWED_ACCEPT_HEADERS"] = [
    // "application/octet-stream",
    "application/json",
    "application/xml",
    // "application/pdf",
    // "text/html",
    // "text/calendar",
    // "text/plain",
    // "text/csv",
    // "text/css",
    // "text/javascript",
    // "audio/mpeg",
    // "audio/vorbis",
    // "audio/ogg",
    // "audio/webm",
    // "image/jpeg",
    // "image/png",
    // "image/apng",
    // "image/avif",
    // "image/gif",
    // "image/svg+xml",
    // "image/webp",
    // "video/mp4",
    // "video/ogg",
    // "video/webm"
];

/**
 * DEFAULT_MIME_TYPE
 * If the client does not send an Accept header, and the MIME type of the response is not set in a request parameter,
 *   then this will be the default Content type for the Response generated by the endpoint
 */
$cfg["DEFAULT_MIME_TYPE"] = "application/json";

/**
 * RELAX_FOR_TEXT_TYPE_REQUESTS
 * When directly accessing an endpoint from a browser, a GET request will be formed with an Accept header of text/html
 * (actually it will be much longer and more complex, but it's basically a text/html header)
 * Setting this option to true will allow such requests to be made, while the Response will still have the default Content type
 */
$cfg["RELAX_FOR_TEXT_TYPE_REQUESTS"] = true;

/**
 * CACHE_DURATION
 * Possible values are "MINUTE", "HOUR", "DAY", "WEEK", "MONTH", "YEAR", "INFINITE", null
 * This will determine when the API should write a new cache file with the response data
 * If set to a value of "INFINITE", the API will always serve the Cached reponse data;
 * if set to a null value the API will never write or serve cached response files
 */
$cfg["CACHE_DURATION"] = "MONTH";

/**
 * CACHE_FOLDER_NAME
 * If CACHE_DURATION is set to a null value, this will be ignored
 * The folder will be created in the same path as (or relative to) your API script
 */
$cfg["CACHE_FOLDER_NAME"] = "apiCache";

/**
 * ALLOWED_ORIGINS
 * If you would like to restrict which clients can make requests to your API
 *   based on the Origin header (scheme, hostname, and port),
 *   replace the asterisk with a list of allowed origins
 * Remember that the origin header can be null or not set, for example for simple GET requests
 *   (such as from the browser navigation bar) there is no Origin header,
 *   whereas there may be a Referer header...
 * You can however force the client to declare the Origin in order for the request to be successful
 *   by explicitly setting allowed origins here
 * If the Origin header does not have a supported value,
 *   the SimpleAPI will return a "401 Unauthorized" header
 * Leaving a single value of an asterisk as in the default will allow any origin
 */
$cfg["ALLOWED_ORIGINS"] = [ "*" ];

/**
 * ALLOWED_REFERERS
 * You can further restrict access to the API by specifying a list of allowed Referers
 * The Referer header includes not only the Origin but also the path from which the request originated
 * The Referer header also might not be set, for example on localhost
 * A simple GET request from a browser navigation bar will set the referer based on the browser,
 *   for example on Chrome it will be https://www.google.com/
 *   and on Edge it will be https://www.bing.com/
 * If the Referer header does not have a supported value,
 *   the SimpleAPI will return a "401 Unauthorized" header
 * Leaving a single value of an asterisk as in the defaults will allow any referer
 */
$cfg["ALLOWED_REFERERS"] = [ "*" ];

/**
 * FORCE_AJAX_REQUEST
 * If you would like to restrict requests to your API
 *   only to AJAX requests (requests that have the X-Requested-With header with a value of xmlhttprequest)
 * Note that fetch requests do not set this header! So do not enforce if you want to support fetch requests
 * If you set this to true, and a request does not have the X-Requested-With header with a value of xmlhttprequest,
 *   the SimpleAPI will return a "418 I'm a teapot" header
 */
$cfg["FORCE_AJAX_REQUEST"] = false;

/**
 * ENFORCE_PARAMETER_CASE
 * Whether you want to enforce strict equality of parameter casing
 * (i.e. must match upper or lower or mixed case exactly as you defined each parameter)
 * For example, you define a parameter as Param1:
 *   should a request with 'param1' work? or a request with 'PARAM1' work?
 *   or should only 'Param1' work?
 */
$cfg["ENFORCE_PARAMETER_CASE"] = true;

/**
 * Github Repo of your final API
 * You may not even need this
 * It can come in handy for things like outputting ICS calendar type data,
 *   which may depend on the latest release of the API
 * These configurations will be used to detect the latest release, if there is one...
 */
$cfg["GITHUB_REPO_USER"] = "JohnRDOrazio";
$cfg["GITHUB_REPO_NAME"] = "SimpleAPI";

//STOP EDITING HERE! DO NOT TOUCH ANYTHING BELOW THIS LINE
foreach( $cfg as $key => $value ) {
    define( 'JohnRDOrazio\\SimpleAPI\\' . $key, $value );
}
