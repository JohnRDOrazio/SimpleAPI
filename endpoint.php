<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use JohnRDOrazio\SampleAPI;

//if not using composer to autoload the SampleAPI class,
//uncomment the following line
include_once( "SampleAPI.php" );

$SampleAPI = new SampleAPI();
$SampleAPI->Init();
