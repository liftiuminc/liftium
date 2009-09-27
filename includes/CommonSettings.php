<?php

// *** Auto load set up
function __autoload($class_name){
	global $IP;
	if (preg_match("/^AdNetwork/", $class_name)){
		require $IP . "AdNetworks/" . $class_name . ".php";
	} else {
		require $IP . $class_name . ".php";
	}
}

// *** Error handling. Be strict and loud in dev environments, and prudent in production
$DEV_HOSTS = array("beer", "testubuntu.liftium.com");
if (Framework::isDev()){
        error_reporting(E_STRICT | E_ALL);
        ini_set('display_errors', true);
} else {
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', false);
}
ini_set('log_errors', true);

// Misc global settings
define('MYSQL_DATE_FORMAT', 'Y-m-d H:i:s');
date_default_timezone_set('UTC'); // This suppresses E_STRICT notices when strtotime is called
$IP = dirname(__FILE__) . '/';


