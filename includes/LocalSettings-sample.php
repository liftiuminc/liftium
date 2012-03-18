<?php
$CONFIG['db'] = array(
	'masterhost' => 'masterdb',
	'slavehosts' => array('masterdb', 'slavedb1'),
	'username' => 'your_username',
	'password' => 'your_password',
	'dbname' => "liftium"
);

$CONFIG['memcached'] = array(
	// Array of memcached hosts
	'0' => array(
		'host' => 'memcached1',
		'port' => '11211',
	),
	'1' => array(
		'host' => 'memcached2',
		'port' => '11211',
	)
);

$CONFIG['delivery_url'] = "http://delivery.liftium.com";
$CONFIG['geoip_url'] = "http://geoip.liftium.com";
$CONFIG['homepage_url'] = "http://www.liftium.com/";

// Different settings if on a dev box
$DEV_HOSTS = array("localhost", "your.dev.server");
if (Framework::isDev()){
	// Turn up error reporting on dev
        error_reporting(E_STRICT | E_ALL);
        ini_set('display_errors', true);

	$CONFIG['db'] = array(
		'masterhost' => 'localhost',
                'slavehosts' => array('localhost'),
		'username' => 'your_username',
		'password' => 'your_password',
		'dbname' => 'liftium_dev'
	);

	$CONFIG['memcached'] = array(
		// Array of memcached hosts
		'0' => array(
			'host' => 'localhost',
			'port' => '11211',
		)
	);
}

