<?php
/**
 * This is an example implementation of the SimpleAPI class
 * Change this in your own project to fine tune the SimpleAPI for your own implementation
 * The SimpleAPI does not implement on it's own any features for translation,
 *   these should be taken care of by your own API implementation
 * The SimpleAPI is transparent when it comes to accepted parameters:
 *   your API implementation should define the accepted parameters
 * The Github Repo user and name are useful for things like ICAL output...
 *   You may not therefore need this, but you may find it useful for other purposes.
*/

//TODO: see if it's possible to move the cache definitions to the SimpleAPI class

include_once( 'includes/APICore.php' );
include_once( 'includes/enums/CacheDuration.php' );
include_once( "includes/APIParams.php" );

class SampleAPI {
    const API_VERSION                               = '3.7';
    public SimpleAPI $SimpleAPI;
    private string   $CacheDuration                 = "";
    private string   $CACHEFILE                     = "";
    private string   $CACHEPATH                     = "";
    private array    $GithubRepo                    = [ "User" => "", "Name" => "" ];
    private array    $AllowedResponseTypes;

    public function __construct(){
        $this->APICore                              = new APICore();
        $this->CacheDuration                        = "_" . CacheDuration::MONTH . date( "m" );
        //define your own cachepath here, it will be relative to the path of the SampleAPI script...
        $this->CACHEPATH                            = "engineCache";    //change this to the path where you woud like to store your API's cache files!
        $this->GithubRepo["User"]                   = "JohnRDOrazio";   //change this to your repo user!
        $this->GithubRepo["Name"]                   = "SampleAPI";      //change this to your repo name!
    }

    private function initParameterData() {
        if ( $this->SimpleAPI->getRequestContentType() === RequestContentType::JSON ) {
            $json = file_get_contents( 'php://input' );
            $data = json_decode( $json, true );
            if( NULL === $json || "" === $json ){
                header( $_SERVER[ "SERVER_PROTOCOL" ]." 400 Bad Request", true, 400 );
                die( '{"error":"No JSON data received in the request: <' . $json . '>"' );
            } else if ( json_last_error() !== JSON_ERROR_NONE ) {
                header( $_SERVER[ "SERVER_PROTOCOL" ]." 400 Bad Request", true, 400 );
                die( '{"error":"Malformed JSON data received in the request: <' . $json . '>, ' . json_last_error_msg() . '"}' );
            } else {
                $this->APISettings = new APIParams( $data );
            }
        } else {
            switch( $this->APICore->getRequestMethod() ) {
                case RequestMethod::POST:
                    $this->LitSettings = new APIParams( $_POST );
                    break;
                case RequestMethod::GET:
                    $this->LitSettings = new APIParams( $_GET );
                    break;
                case RequestMethod::OPTIONS:
                    //continue
                    break;
                default:
                    header( $_SERVER[ "SERVER_PROTOCOL" ]." 405 Method Not Allowed", true, 405 );
                    $errorMessage = '{"error":"You seem to be forming a strange kind of request? Allowed Request Methods are ';
                    $errorMessage .= implode( ' and ', $this->APICore->getAllowedRequestMethods() );
                    $errorMessage .= ', but your Request Method was ' . $this->APICore->getRequestMethod() . '"}';
                    die( $errorMessage );
            }
        }
        if( $this->APIParams->ResponseType !== null ) {
            if( in_array( $this->APIParams->ResponseType, $this->AllowedResponseTypes ) ) {
                $this->SimpleAPI->setResponseContentType( $this->SimpleAPI->getAllowedAcceptHeaders()[ array_search( $this->APIParams->ResponseType, $this->AllowedResponseTypes ) ] );
            } else {
                header( $_SERVER[ "SERVER_PROTOCOL" ]." 406 Not Acceptable", true, 406 );
                $errorMessage = '{"error":"You are requesting a content type which this API cannot produce. Allowed content types are ';
                $errorMessage .= implode( ' and ', $this->AllowedResponseTypes );
                $errorMessage .= ', but you have issued a parameter requesting a Content Type of ' . strtoupper( $this->APIParams->ResponseType ) . '"}';
                die( $errorMessage );
            }
        } else {
            if( $this->SimpleAPI->hasAcceptHeader() ) {
                if( $this->SimpleAPI->isAllowedAcceptHeader() ) {
                    $this->APIParams->ResponseType = $this->AllowedResponseTypes[ $this->SimpleAPI->getIdxAcceptHeaderInAllowed() ];
                    $this->SimpleAPI->setResponseContentType( $this->SimpleAPI->getAcceptHeader() );
                } else {
                    //Requests from browser windows using the address bar will probably have an Accept header of text/html
                    //In order to not be too drastic, let's treat text/html as though it were application/json
                    $acceptHeaders = explode( ",", $this->SimpleAPI->getAcceptHeader() );
                    if( in_array( 'text/html', $acceptHeaders ) || in_array( 'text/plain', $acceptHeaders ) || in_array( '*/*', $acceptHeaders ) ) {
                        $this->APIParams->ResponseType = ReturnType::JSON;
                        $this->APICore->setResponseContentType( AcceptHeader::JSON );
                    } else {
                        header( $_SERVER[ "SERVER_PROTOCOL" ]." 406 Not Acceptable", true, 406 );
                        $errorMessage = '{"error":"You are requesting a content type which this API cannot produce. Allowed Accept headers are ';
                        $errorMessage .= implode( ' and ', $this->APICore->getAllowedAcceptHeaders() );
                        $errorMessage .= ', but you have issued an request with an Accept header of ' . $this->APICore->getAcceptHeader() . '"}';
                        die( $errorMessage );
                    }

                }
            } else {
                $this->APIParams->ResponseType = $this->AllowedResponseTypes[ 0 ];
                $this->SimpleAPI->setResponseContentType( $this->SimpleAPI->getAllowedAcceptHeaders()[ 0 ] );
            }
        }
    }

    /**
     * 
    */
    private function cacheFileIsAvailable( string $path ) : bool {
        $cacheFilePath = "{$path}/v" . str_replace( ".", "_", self::API_VERSION ) . "/";
        $cacheFileName = md5( serialize( $this->APIParams) ) . $this->CacheDuration . "." . strtolower( $this->APIParams->ResponseType );
        $this->CACHEFILE = $cacheFilePath . $cacheFileName;
        return file_exists( $this->CACHEFILE );
    }

    private function getGithubReleaseInfo() : stdClass {
        [ "User" => $repoUser, "Name" => $repoName ] = $this->GithubRepo;
        $returnObj = new stdClass();
        $GithubReleasesAPI = "https://api.github.com/repos/{$repoUser}/{$repoName}/releases/latest";
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $GithubReleasesAPI );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'LiturgicalCalendar' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $currentVersionForDownload = curl_exec( $ch );

        if ( curl_errno( $ch ) ) {
          $returnObj->status = "error";
          $returnObj->message = curl_error( $ch );
        }
        curl_close( $ch );

        $GitHubReleasesObj = json_decode( $currentVersionForDownload );
        if( json_last_error() !== JSON_ERROR_NONE ){
            $returnObj->status = "error";
            $returnObj->message = json_last_error_msg();
        } else {
            $returnObj->status = "success";
            $returnObj->obj = $GitHubReleasesObj;
        }
        return $returnObj;
    }

    private function generateResponse() {
        $Response                          = new stdClass();
        //elaborate your Response further here!

        //make sure we have a Cache folder for the current Version
        if( realpath( "{$this->CACHEPATH}/v" . str_replace( ".", "_", self::API_VERSION ) ) === false ) {
            mkdir( "{$this->CACHEPATH}/v" . str_replace( ".", "_", self::API_VERSION ), 0755, true );
        }

        switch ( $this->APIParams->ResponseType ) {
            case ResponseType::JSON:
                $response = json_encode( $Response );
                break;
            case ResponseType::XML:
                $jsonStr = json_encode( $Response );
                $jsonObj = json_decode( $jsonStr, true );
                $xml = new SimpleXMLElement ( "<?xml version=\"1.0\" encoding=\"UTF-8\"?" . "><LiturgicalCalendar xmlns=\"https://www.bibleget.io/catholicliturgy\"/>" );
                LitFunc::convertArray2XML( $jsonObj, $xml );
                $response = $xml->asXML();
                break;
            case ReturnType::ICS:
                $infoObj = $this->getGithubReleaseInfo();
                if( $infoObj->status === "success" ) {
                    $response = $this->produceIcal( $Response, $infoObj->obj );
                }
                else{
                    die( '{"Error": "Error receiving or parsing info from github about latest release: '.$infoObj->message . '"}' );
                }
                break;
            default:
                $response = json_encode( $Response );
                break;
        }
        file_put_contents( $this->CACHEFILE, $response );
        $responseHash = md5( $response );
        header("Etag: \"{$responseHash}\"");
        if (!empty( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] === $responseHash) {
            header( $_SERVER[ "SERVER_PROTOCOL" ] . " 304 Not Modified" );
            header('Content-Length: 0');
        } else {
            echo $response;
        }
        die();
    }

    public function setCacheDuration( string $duration ) : void {
        switch( $duration ) {
            case CacheDuration::MINUTE:
                $this->CacheDuration = "_" . $duration . date( "i" ); //The minute of the current hour of the current day of the request
                break;
            case CacheDuration::HOUR:
                $this->CacheDuration = "_" . $duration . date( "G" ); //The hour of the current day of the request
                break;
            case CacheDuration::DAY:
                $this->CacheDuration = "_" . $duration . date( "z" ); //The day of the year of the request ( starting from 0 through 365 )
                break;
            case CacheDuration::WEEK:
                $this->CacheDuration = "_" . $duration . date( "W" ); //ISO-8601 week number of year (weeks starting on Monday) of the request
                break;
            case CacheDuration::MONTH:
                $this->CacheDuration = "_" . $duration . date( "m" ); //Numeric representation of the month (with leading zeros) of the request
                break;
            case CacheDuration::YEAR:
                $this->CacheDuration = "_" . $duration . date( "Y" ); //A full numeric representation of the year (4 digits) of the request
                break;
        }
    }

    public function setAllowedResponseTypes( array $responseTypes ) : void {
        $this->AllowedResponseTypes = array_values( array_intersect( ResponseType::$values, $responseTypes ) );
    }

    /**
     * Your SampleAPI will only work once you call the public Init() method
     */
    public function Init(){
        $this->SimpleAPI->Init();
        $this->initParameterData();
        //you may have your own methods to load and calculate data here,
        //perhaps automating settings based on parameters, etc.

        //once you have taken care of getting parameters and calculations done,
        //we can set our response header
        $this->SimpleAPI->setResponseContentTypeHeader();

        if( $this->cacheFileIsAvailable() ){
            //If we already have done the calculation
            //and stored the results in a cache file
            //then we're done, just output this and die
            //or better, make the client use it's own cache copy!
            $response = file_get_contents( $this->CACHEFILE );
            $responseHash = md5( $response );
            header("Etag: \"{$responseHash}\"");
            if (!empty( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] === $responseHash) {
                header( $_SERVER[ "SERVER_PROTOCOL" ] . " 304 Not Modified" );
                header('Content-Length: 0');
            } else {
                echo $response;
            }
            die();
        } else {
            //do your API stuff here, any calculations to prepare your response

            //once your response is ready, we can now send it
            $this->generateResponse();
        }
    }

}
