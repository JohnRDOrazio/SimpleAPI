# SimpleAPI
Jumpstart your API (whether RESTful or not) with a base class that you can fine tune for your own API implementation.

There are so many discussions about what is truly RESTful and what is not, this project doesn't pretend to be truly RESTful out of the box.

The SimpleAPI simply aims to remove some of the hassle of the initial setup of an API in PHP, without depending on any frameworks.

It aims to be extensible so as to cater to various use cases, and to various kinds of APIs.

## What the SimpleAPI is NOT
The SimpleAPI does not create or handle routes (for now at least). This should be handled by your own API implementation.

The SimpleAPI does not handle internationalization or localization of your response:
this should be handled by your own API implementation, using the included SampleAPI middleware.

## What the SimpleAPI IS
The SimpleAPI simply handles detection of Request methods and Accept headers, 
and takes care of preparing the Response accordingly, with the correct Response headers and Content type.

It will assist you in defining the accepted parameters that the request can or should include,
and in generating the correct response based on those parameters.
It will generate response headers for both valid and invalid requests,
so as to assist the requesting party in making the correct request to your API.

The SimpleAPI offers a sample middleware, that can help define your own API implementation,
with the SampleAPI class. You should adapt the SampleAPI class so as to create your own API.

The SampleAPI class is not an API endpoint: API endpoints should implement the SampleAPI middleware,
and each endpoint should set the SimpleAPI to behave according to its own needs through the SampleAPI middleware.

## Minimum PHP version
The SimpleAPI requires a minimum PHP version of 7.4, seeing that it makes use of typed properties
in order to have consistency throughout the codebase.

The SimpleAPI defines faux `enum`s using simple classes, so as not to require PHP 8.1;
in the future it may implement true enums requiring a minimum PHP version of 8.1.
