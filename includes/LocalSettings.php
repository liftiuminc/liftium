<?php

$localconf = yaml_parse_file( "/usr/wikia/docroot/Settings.yml" );

$CONFIG['db'] = array(
	'masterhost' => '10.8.42.24',
	'slavehosts' => array('10.8.42.24'/*, '10.8.40.24'*/),
	'username' => 'rails_dashboard',
	'password' => 'Peshduk0',
	'dbname' => "liftium"
);

$CONFIG['memcached'] = array(
	// Array of memcached hosts
        '0' => array(
                'host' => '10.8.42.24',
                'port' => '11211',
        ),
        '1' => array(
                'host' => '10.8.40.24',
                'port' => '11211',
        ),
);

$CONFIG['db'] = $localconf[ "wgLiftiumDBServers" ];
$CONFIG['memcached'] = $localconf[ "wgLiftiumMemCachedServers" ];

$a = $CONFIG['memcached'];
$a2 = array();
foreach ($a as $line) {
        if (!is_array($line)) {
                $line2 = explode(':', $line);
                $a2[] = array('host' => $line2[0], 'port' => $line2[1]);
        } else {
                $a2[] = $line;
        }
}
$CONFIG['memcached'] = $a2;

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
