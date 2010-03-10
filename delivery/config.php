<?php
// Have the JSON still try to execute if we get errors
ini_set('display_errors', true);
ini_set('error_prepend_string', '/*');
ini_set('error_append_string', '*/');
ini_set('html_errors', false);

require dirname(__FILE__) . '/../includes/Framework.php';

Framework::httpCache(LiftiumConfig::getLastUpdated());

$LiftiumConfig = new LiftiumConfig();

$pubid = Framework::getRequestVal("pubid", null, FILTER_VALIDATE_INT);
if (empty($pubid)){
	echo "/*";
	trigger_error("Missing or invalid pubid from " . @$_SERVER['HTTP_REFERER'], E_USER_WARNING);
	echo "*/";
	$config = array('error'=>"Missing pubid");
} else {
	$config = $LiftiumConfig->getConfig($_GET);
	if (empty($config->sizes)){
		$config = array('error'=>"No tags for this publisher");
	}
}

// Check to see if we can use the Etag to return a 304.
$checksum = md5(serialize($config));
header("ETag: $checksum");
if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $checksum){
	// Nothing has changed since the last time they asked.
	// NOTE THAT VARNISH HANDLES THIS, so we don't really need to here.
	header("HTTP/1.0 304 Not Modified");	
	exit;
}

$format = Framework::getRequestVal("format", "json", FILTER_SANITIZE_STRING);
if ($format == 'text'){
	header('Content-Type: text/plain');
	print_r($config);
} else {
	header('Content-Type: application/x-javascript');
    echo 'Liftium.config = ' . json_encode($config) . ';';

    // esi or inline call? -- all servers should have a local geoipd.
    // disabled for now, as there's a requirement to compress this
    // response, and that will not work with esis in varnish right now
    // uncomment this code when compression & varnish work well together
    // --Jos
//     echo 'Liftium.geo = ';
//     $url = "/__geoip";
//     if ( Framework::isDev() ) {
//         echo file_get_contents( 'http://localhost:9042' . $url);
//     } else {
// 	    ### src HAS to be in double quotes (")
//         echo "<!--esi <esi:include src=\"$url\" /> -->";
//     }

	if (Framework::getRequestVal('liftium_debug', 0, FILTER_VALIDATE_INT)){
		echo '/*' . print_r($_GET, true) . '*/' . "\n";
	}
}

$config_delay = Framework::getRequestVal("config_delay", null, FILTER_VALIDATE_INT);
if (!empty($config_delay)){
	trigger_error("Config artificially delayed $config_delay seconds", E_USER_NOTICE);
	sleep(min($config_delay, 5)); // max of 5 to prevent silliness
}
?>
