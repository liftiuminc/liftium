<?php
require dirname(__FILE__) . '/../includes/Framework.php';

// Parse incoming
$type = Framework::getRequestVal("type", "UnknownType");
$lang = Framework::getRequestVal("lang", "UnknownLang");
$pubid = Framework::getRequestVal("pubid", "UnknownPubid");
$browser = Framework::getBrowser();
$ip = Framework::getIp();
$msg = Framework::getRequestVal("msg");
$tag_id = Framework::getRequestVal("tag_id");
if (preg_match("/Error on line #([0-9]+) of (https*:\/\/[^ :]+)/", $msg, $match)){
	$line = $match[1];
	$url = trim($match[2]);
} else {
	$line = "-1";
	$url = "UnknownUrl";
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
	echo "tag_id = $tag_id\n";
	echo "url = $url\n";
	echo "line = $line\n";
	echo "</pre>";

	phpinfo(INFO_VARIABLES);
}

$logit = true;
$statit = true;
$emailto = array("nick@liftium.com");


// Triage
if (preg_match("/xdm_iframe_path/", $msg)){
	$emailto = false;
} else if ($type == "tag"){
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

// Turn it off, too noisy to be useful.
$emailto=false;

// Create message
$message = "$ip|Pubid:$pubid|$msg|" . @$_SERVER['HTTP_REFERER'] . "|$browser";

// Log the message
if ($logit) {
	$load = sys_getloadavg();
	if ($load[0] > 5){
		// Write to a log file
		ini_set('error_log', '/home/tempfiles/10days/jserrors.' . @$_GET['type'] . '.' . date('Y-m-d'));
		error_log($message);
	} else {
		$justMsg = trim(preg_replace("/Error on line #([0-9]+) of (https*:\/\/[^ :]+)/", "", $msg));
		$db = Framework::getDB("master");
		$db->exec("INSERT INTO javascript_errors VALUES(NULL, NOW(), " . $db->quote($pubid) . "," .
			$db->quote($tag_id) . "," . $db->quote($type) . "," . $db->quote($lang) . "," . $db->quote($browser) .
			"," . $db->quote($ip) . "," . $db->quote($justMsg). "," . $db->quote($url) . "," . $db->quote($line) . ");");
	}
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
