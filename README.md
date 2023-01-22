# SimpleAPI
[![CodeFactor](https://www.codefactor.io/repository/github/johnrdorazio/simpleapi/badge)](https://www.codefactor.io/repository/github/johnrdorazio/simpleapi)

Jumpstart your API (whether RESTful or not) and fine tune it for your own API implementation.

There are so many discussions about what is truly RESTful and what is not, this project doesn't pretend to be truly RESTful out of the box.

The **SimpleAPI** simply aims to remove some of the hassle of the initial setup of an API in PHP, without depending on any frameworks.

It aims to be extensible so as to cater to various use cases, and to various kinds of APIs. In building your own API, you can decide whether it should be fully RESTful or not, based on what you are trying to achieve.

## What the SimpleAPI is NOT
The **SimpleAPI** does not create or handle routes (for now at least). This should be handled by your own API implementation.

The **SimpleAPI** does not handle internationalization or localization of your response:
this should be handled by your own API implementation, an example of which is provided in the **SampleAPI** class (`SampleAPI.php`).

The **SimpleAPI** does not create any kind of client implementation of the API.
It simply helps you jumpstart your API, to handle requests from clients and produce responses.
Client implementations should be created separately.

## What the SimpleAPI IS
The **SimpleAPI** simply handles detection of `Request` methods and `Accept` headers, 
and takes care of preparing the `Response` accordingly, with the correct `Response` headers and `Content type`.

It will assist you in defining the accepted parameters that the `Request` can or should include,
in sanitizing the values of `Request` parameters based on the type you have defined for each parameter,
and in generating the correct `Response` based on those parameters.
It will generate `Response` headers for both valid and invalid requests,
so as to assist the requesting party in making the correct `Request` to your API.

The **SimpleAPI** offers a sample implementation in the `SampleAPI` class, which can assist you in defining your own API implementation.
Go ahead and adapt the `SampleAPI` class to fit your own API implementation.

The `SampleAPI` class is not an API endpoint, it merely determines the behaviour of your API.
The `SampleAPI` (or any API implementation you may create) should define configurations for the `SimpleAPI` package with a `config.php` based on `config.sample.php`.

An example endpoint is included as the file `endpoint.php`: this simply instantiates the `SampleAPI` class, which in turn instantiates the `SimpleAPI` class using the configurations in `config.sample.php`.

## Why the SimpleAPI ?
Many API implementations nowadays are handled with [Ruby on Rails](https://rubyonrails.org/), or [Python](https://www.python.org/) + [Flask](https://flask.palletsprojects.com/), or with complex frameworks such as [Laravel](https://laravel.com/) that have a lot of functionality.

However sometimes you may need to just create a simple API service without a lot of overhead, and without needing to learn a whole new language or the complexities of a framework.

And perhaps you are familiar with [PHP](https://www.php.net/) and would like to stick to a simple PHP implementation.

The **SimpleAPI** aims to jumpstart your simple API implementation, removing the initial overhead of defining the basics of handling request headers and response headers.

The **SimpleAPI** is compliant with the HTTP2 protocol.

## Minimum PHP version
The **SimpleAPI** requires a minimum PHP version of [7.4](https://www.php.net/releases/7_4_0.php),
seeing that it makes use of typed properties in order to have consistency throughout the codebase.

The **SimpleAPI** defines faux `enum`s using simple classes, so as not to require PHP 8.1;
in the future it may implement true enums requiring a minimum PHP version of 8.1.

## Create your API implementation in 3 steps ##
Here are a few simple steps to get you started in creating your own API:

1) Create your API folder and require the SimpleAPI package
   > *This step takes for granted that you have `composer` already installed in your system.*
   ```console
   mkdir MyApi
   cd MyApi
   composer require johnrdorazio/simpleapi
   ```
   
2) Create your own API definition
   
   On a Linux system:
   ```console
   touch MyApi.php
   touch config.php
   ```
   
   On a Windows system:
   ```console
   copy nul MyApi.php
   copy nul config.php
   ```
   
   Paste the contents of [SampleAPI.php](SampleAPI.php) into `MyApi.php` and adapt to your needs (changing the class name for starters),
   or simply write from scratch your own API implementation using the `SimpleAPI` package, defining your API parameters (if any):
   ```php
   <?php
   use JohnRDOrazio\SimpleAPI\SimpleAPI;
   use JohnRDOrazio\SimpleAPI\Enums\ResponseType;
   use JohnRDOrazio\SimpleAPI\Enums\RequestMethod;
   use JohnRDOrazio\SimpleAPI\Enums\RequestContentType;

   require __DIR__ . '/vendor/autoload.php';

   class MyApi {
       private SimpleAPI $SimpleAPI;
       public function __construct() {
           $this->SimpleAPI = new SimpleAPI();
       }

       public function Init() {

            //Initialize the SimpleAPI, which will take care of detecting request and setting response headers
            $this->SimpleAPI->Init();

            //define your API's accepted parameters and expected type (defining parameters is optional: you might not have any parameters...)
            $this->SimpleAPI->defineParameter( 'PARAM_ONE', ParamType::STRING );
            $this->SimpleAPI->defineParameter( 'PARAM_TWO', ParamType::INTEGER );
            $this->SimpleAPI->defineParameter( 'RESPONSETYPE', ParamType::RESPONSETYPE );
            //TODO: move as much from SampleAPI->initParameterData() as possible to SimpleAPI
            $this->initParameterData(); //For now you have to create your own initParameterData() function...
       }
   }
   ```
   
   Similarly paste the contents of [config.sample.php](config.sample.php) into `config.php` and adapt to your needs.
      
3) Create your endpoint:
   
   *on a Linux system*
   ```console
   touch endpoint.php
   ```
   
   *on a Windows system*
   ```console
   copy nul endpoint.php
   ```
   
   Include your API definition into your endpoint, for example:
   ```php
   <?php
   
   include_once( 'MyApi.php' );
   
   $MyApi = new MyApi();
   $MyApi->Init();
   ```
   
   Now when you access `localhost/MyApi/endpoint.php` (or wherever you are hosting your API folder), you should get your first response!
