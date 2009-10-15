<?php
require dirname(__FILE__) . '/../includes/Framework.php';

// Parse incoming
$type = Framework::getRequestVal("type", "UnknownType");
$lang = Framework::getRequestVal("lang", "UnknownLang");
$pubid = Framework::getRequestVal("pubid", "UnknownPubid");
$browser = Framework::getBrowser();
$ip = Framework::getIp();
$msg = Framework::getRequestVal("msg");
if (preg_match("/error on line #([0-9]+) of (https*:\/\/[^ :]+)/", $msg, $match)){
	$line = $match[1];
	$url = trim($match[2]);
} else {
	$line = "-1";
	$url = "UnkwownUrl";
}

// Debug
if (!empty($_GET['debug'])){
	echo "<pre>";
	print_r($_GET);
	echo "type = $type\n";
	echo "lang = $lang\n";
	echo "pubid = $pubid\n";
	echo "browser = $browser\n";
	echo "ip = $ip\n";
	echo "msg = $msg\n";
	echo "</pre>";

	phpinfo(INFO_VARIABLES);
}

$logit = true;
$statit = true;
$emailto = array("nick@liftium.com");


// Triage
if ($type == "tag"){
	$emailto[] = "veronica@liftium.com";
	$emailto[] = "jennie@liftium.com";
} else if ($lang != "en" ){
	// Can't read these anyway
	$emailto = false;
} else if (empty($line)){
	// If line is 0, we won't be able to debug.
	$emailto = false;
} else if (!strstr($url, "liftium.com")){
	// Not our site. Log it, no e-mail and no stats
	$statit = false;
	$emailto = false;
}

// Create message
$message = "$ip|Pubid:$pubid|$msg|" . @$_SERVER['HTTP_REFERER'] . "|$browser";

// Log the message
if ($logit) {
	// Write to a log file
	ini_set('error_log', '/home/tempfiles/10days/jserrors.' . @$_GET['type'] . '.' . date('Y-m-d'));
	error_log($message);
}

// Send e-mail
if (!Framework::isDev() && !empty($emailto)){
	mail(implode(",", $emailto), "Liftium Javascript Error - @{$_GET['type']}", $message);
}

// Record in memcache for stats
if ($statit) {
	EventRecorder::record(array('JavascriptErrors', Framework::getBrowser()), "minute");
	EventRecorder::record(array('JavascriptErrors'), "minute");
	if (@$_GET['type'] == 'tag') {
		EventRecorder::record(array('TagErrors'), "minute");
	} else {
			EventRecorder::record(array('JavascriptErrors_' . $type), "minute");
	}
}
?>
