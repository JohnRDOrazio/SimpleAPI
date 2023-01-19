<?php
/**
 * This is an example implementation of the SimpleAPI class,
 *   to build your own API
 * This is not your API endpoint: your API endpoint or endpoints will include this class
 *   and probably instruct this class to behave as the endpoint expects the API to behave
 * Change this in your own project to fine tune the SimpleAPI for your own implementation
 * The SimpleAPI does not implement on it's own any features for translation,
 *   these should be taken care of by your own API implementation here
 * The SimpleAPI is transparent when it comes to accepted parameters:
 *   your API implementation should define the accepted parameters,
 *   we have included an APIParams class to assist with this
 * The Github Repo user and name are useful for things like ICAL output...
 *   You may not therefore need these, but you may find it useful for other purposes.
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once( 'src/SimpleAPI.php' );
require_once( "src/includes/APIParams.php" );

//include any tranforms you may need, based on your supported response content types
require_once( "src/includes/transforms/XmlTransform.php" );
require_once( "src/includes/transforms/IcsTransform.php" );

class SampleAPI {
    const API_VERSION                               = '0.1';
    private SimpleAPI $SimpleAPI;
    private APIParams $APIParams;

    private array $responseData                     = [];

    public function __construct(){
        $this->SimpleAPI                            = new SimpleAPI();
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
                $this->APIParams = new APIParams( $data );
            }
        } else {
            switch( $this->SimpleAPI->getRequestMethod() ) {
                case RequestMethod::POST:
                    $this->APIParams = new APIParams( $_POST );
                    break;
                case RequestMethod::GET:
                    $this->APIParams = new APIParams( $_GET );
                    break;
                case RequestMethod::OPTIONS:
                    //continue
                    break;
                default:
                    header( $_SERVER[ "SERVER_PROTOCOL" ]." 405 Method Not Allowed", true, 405 );
                    $errorMessage = '{"error":"You seem to be forming a strange kind of request? Allowed Request Methods are ';
                    $errorMessage .= implode( ' and ', $this->SimpleAPI->getAllowedRequestMethods() );
                    $errorMessage .= ', but your Request Method was ' . $this->SimpleAPI->getRequestMethod() . '"}';
                    die( $errorMessage );
            }
        }
        if( $this->APIParams->ResponseType !== null ) {
            $this->SimpleAPI->validateResponseTypeParam( $this->APIParams->ResponseType );
        } else {
            $responseType = $this->SimpleAPI->getResponseTypeFromResponseContentType();
            $this->APIParams->setResponseType( $responseType );
        }
    }


    private function generateResponse() : string {
        $response               = '';
        $ResponseObj            = new stdClass();

        //elaborate your Response further here!
        //the final output of your API response will be whatever properties and valued you put in this $ResponseObj

        //For example, if we want to echo back the parameters received in the request,
        //or the params that have been elaborated throughout the script in any case,
        //we can put them in an APIParams property
        $ResponseObj->APIParams = new stdClass();
        foreach( $this->APIParams as $key => $value ) {
            $ResponseObj->APIParams->{$key} = $value;
        }

        //We can put the main response data calculated by our API
        //and stored in the responseData class variable
        //into a property named Data
        $ResponseObj->Data = $this->responseData;

        //We can let the client know that it is actually receiving a response
        //from the version of the API that it was expecting
        $ResponseObj->ApiVersion                    = self::API_VERSION;
        $ResponseObj->ResponseType                  = $this->APIParams->ResponseType;
        $ResponseObj->ResponseContentType           = $this->SimpleAPI->getResponseContentType();
        $ResponseObj->RequestContentType            = $this->SimpleAPI->getRequestContentType();
        $ResponseObj->CacheFile                     = $this->SimpleAPI->getCacheFile();
        $ResponseObj->SimpleAPICfgs = new stdClass();
        $ResponseObj->SimpleAPICfgs->AllowedAcceptHeaders           = $this->SimpleAPI->getAllowedAcceptHeaders();
        $ResponseObj->SimpleAPICfgs->AllowedResponseTypes           = $this->SimpleAPI->getAllowedResponseTypes();
        $ResponseObj->SimpleAPICfgs->AllowedOrigins                 = $this->SimpleAPI->getAllowedOrigins();
        $ResponseObj->SimpleAPICfgs->AllowedReferers                = $this->SimpleAPI->getAllowedReferers();
        $ResponseObj->SimpleAPICfgs->AllowedRequestMethods          = $this->SimpleAPI->getAllowedRequestMethods();
        $ResponseObj->SimpleAPICfgs->AllowedRequestContentTypes     = $this->SimpleAPI->getAllowedRequestContentTypes();
        $ResponseObj->SimpleAPICfgs->CacheDuration                  = $this->SimpleAPI->getCacheDuration();
        $ResponseObj->SimpleAPICfgs->CacheFolderName                = $this->SimpleAPI->getCacheFolderName();
        $ResponseObj->SimpleAPICfgs->DefaultResponseContentType     = $this->SimpleAPI->getDefaultResponseContentType();
        $ResponseObj->SimpleAPICfgs->RelaxForTextTypeRequests       = $this->SimpleAPI->getRelaxForTextTypeRequests();
        $ResponseObj->SimpleAPICfgs->EnforceAjaxRequests            = $this->SimpleAPI->getForceAjaxRequest();
        $ResponseObj->SimpleAPICurrentVersion                       = SimpleAPI::VERSION; 

        $GithubReleasesAPI = "https://api.github.com/repos/JohnRDOrazio/SimpleAPI/releases/latest";
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $GithubReleasesAPI );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'JohnRDOrazio/SimpleAPI' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $latestReleaseJSON = curl_exec( $ch );
        curl_close( $ch );
        $latestReleaseJSON = json_decode( $latestReleaseJSON );
        $ResponseObj->SimpleAPILatestVersion                = str_replace( 'v', '', $latestReleaseJSON->tag_name );

        //This object will be transformed into the JSON or XML or ICS response, or whatever response type was requested
        //here you may define your own cases to handle each response type supported by your API
        switch ( $this->APIParams->ResponseType ) {
            case ResponseType::JSON:
                //if a JSON resource was requested, we transform our response to JSON
                $response = json_encode( $ResponseObj );
                break;
            case ResponseType::XML:
                //if an XML resource was requested, we transform our response to XML
                $response = XmlTransform::ObjectToXml( $ResponseObj );
                break;
            case ResponseType::ICS:
                $infoObj = IcsTransform::getGithubReleaseInfo();
                if( $infoObj->status === "success" ) {
                    //The ObjectToIcs method takes for granted that the $ResponseObj has a "Cal" property,
                    //which contains the calendar event objects
                    //In the basic ObjectToIcs implementation, each event object should have the following properties:
                    //  name, description, date
                    //  The first two are string properties, the last one is a PHP DateTime object
                    $response = IcsTransform::ObjectToIcs( $ResponseObj, $infoObj->obj );
                }
                else{
                    die( '{"Error": "Error receiving or parsing info from github about latest release: '.$infoObj->message . '"}' );
                }
                break;
            default:
                $response = json_encode( $ResponseObj );
                break;
        }

        $this->SimpleAPI->writeResponseToCacheFile( $response );
        return $response;
    }

    private static function outputResponse( string $responseContents ) {
        $responseHash = md5( $responseContents );
        header("Etag: \"{$responseHash}\"");
        if (!empty( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] === $responseHash) {
            header( $_SERVER[ "SERVER_PROTOCOL" ] . " 304 Not Modified" );
            header('Content-Length: 0');
        } else {
            echo $responseContents;
        }
        die();
    }

    /**
     * Your SampleAPI will only work once you call the public Init() method
     */
    public function Init(){
        $this->SimpleAPI->Init();
        $this->initParameterData();

        //we don't set the response content type header in the SimpleAPI->Init() itself
        //we wait until parameters are loaded, just in case a parameter determines the response content type
        $this->SimpleAPI->setResponseContentTypeHeader();

        //if you need to intervene setting further APIParams based on parameters, etc.,
        //you should do so HERE, since any cache files will be determined based on APIParams

        $responseContents = $this->SimpleAPI->getCacheFileIfAvailable( $this->APIParams, self::API_VERSION );
        if( null === $responseContents ) {
            //This is where the main calculations of your API take place
            //For example you can populate the $this->responseData array with the API results

            //Here is an example that populates the responseData array
            //with the string in APIParams->Param1 for as many times
            //as indicated by APIParams->Param2 
            for( $i = 0; $i < $this->APIParams->Param2; $i++ ) {
                $this->responseData[] = $this->APIParams->Param1;
            }

            //once your response is ready, we can do any last elaboration to the final output
            //and transform it based on the requested content type
            $responseContents = $this->generateResponse();
        }
        $this->outputResponse( $responseContents );
    }

}
