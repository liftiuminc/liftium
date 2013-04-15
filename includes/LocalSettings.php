<?php
$CONFIG['db'] = array(
	'masterhost' => 'liftium-s4',
	'slavehosts' => array('liftium-s4','liftium-s5'),
	'username' => 'rails_dashboard',
	'password' => 'Zy9X5arf',
	'dbname' => "liftium"
);

$CONFIG['memcached'] = array(
	// Array of memcached hosts
        '0' => array(
                'host' => 'liftium-s4',
                'port' => '11211',
        ),
        '1' => array(
                'host' => 'liftium-s5',
                'port' => '11211',
        ),
);

// Define dev boxes
$DEV_HOSTS = array("localhost", "test-liftium");

if (file_exists(dirname(__FILE__) . '/LocalSettings-thisbox.php')){
	include dirname(__FILE__) . '/LocalSettings-thisbox.php';
}

// Different settings if on a dev box
if (Framework::isDev()){
	// Turn up error reporting on dev
        error_reporting(E_STRICT | E_ALL);
        ini_set('display_errors', true);
}

// Wikia uses UTC
date_default_timezone_set('UTC');
