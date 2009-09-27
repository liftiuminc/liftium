<?php
require dirname(__FILE__) . '/../includes/Framework.php';
if (empty($_GET['beacon'])){
	$beacon = array();
	$beacon['events'] = $_GET['events'];
} else {
	$beacon = json_decode($_GET['beacon'], true);
}

$counter = 0;
$browser = str_replace(' ', '_', Framework::getBrowser());

if (!empty($_GET['debug'])){
	header('Content-Type: text/plain');
	print_r($beacon);
}

foreach (explode(',', $beacon['events']) as $event){
	// Record each event
	if (!preg_match('/([lr])([0-9]+)pl([0-9]+)/', $event, $matches)){
		continue;
	}

	if ($matches[1] == 'l'){
		$type = 'Load';
	} else if ($matches[1] == 'r'){
		$type = 'Reject';
	} else {
		trigger_error("Invalid event type (" . $matches[1] . ")", E_USER_WARNING);
		continue;
	}

	$e = array($type, $matches[2]);
	EventRecorder::record($e, "minute");
	// For every load/reject, there is an attempt
	EventRecorder::record(array('Attempt', $matches[2]), "minute");
	EventRecorder::record(array('Attempt', $browser), "minute");
	$counter++;

	// Keep track of rejections based on the number of previous loads to reverse engineer
	// frequency capping
	if (empty($matches[3])){
		$loads = 0;
	} else if ($matches[3] > 5){
		$loads = 5;
	} else {
		$loads = $matches[3];
	}

	if ($type == 'Reject'){
		EventRecorder::record(array('Reject', $browser), "minute");

		$e2 = array("LoadInfoReject", $loads, $matches[2]);
		EventRecorder::record($e2, "minute");
		$counter++;

		EventRecorder::record(array('TotalHops'), "minute");
		$counter++;

		$e2 = array('RejectWithPl', $matches[2], $loads);
		EventRecorder::record($e2, "minute");
		$counter++;

	} else if ($type == 'Load'){


		if ($matches[2] != "113") { // noad
			EventRecorder::record(array('Load', $browser), "minute");
			$counter++;
			EventRecorder::record(array('TotalAdsServed'), "minute");
			$counter++;
		}

		$e2 = array('LoadWithPl', $matches[2], $loads);
		EventRecorder::record($e2, "minute");
		$counter++;

	}

} // foreach


// Record the call
EventRecorder::record(array('BeaconCalls'), "minute");
$counter++;

// Ad Time
if (isset($beacon['adTime'])){
	if (@$beacon['adTime'] > 30){
		$beacon['adTime'] = 30;
	}
	EventRecorder::record(array('AdTime', $beacon['adTime']), "minute");
	$counter++;
}

// Page load time
if (!empty($beacon['pageTime'])){ // not available for every skin
	if ($beacon['pageTime'] > 30){
		$beacon['pageTime'] = 30;
	}
	EventRecorder::record(array('PageTime', $beacon['pageTime']), "minute");
	$counter++;
}

// Page load time
if (!empty($beacon['pageTime'])){ // not available for every skin
	if ($beacon['pageTime'] > 30){
		$beacon['pageTime'] = 30;
	}
	EventRecorder::record(array('PageTime', $beacon['pageTime']), "minute");
	$counter++;
}

// Network response time
if (!empty($beacon['respTimes'])){
	foreach($beacon['respTimes'] as $rt){
		$pieces = explode('=', $rt);
		// Normalize the time
		if ($pieces[1] > 30000){
			$pieces[1] = 30000;
		} else if ($pieces[1] < 0){
			// I have no idea how this happens, but it does
			continue;
		}
		$pieces[1] = round($pieces[1]/1000,1);
		EventRecorder::record(array('AdNetworkRespTime', $pieces[0], $pieces[1]), "minute");
		$counter++;
	}
}

// Slot Timeouts
if (!empty($beacon['slotTimeouts'])){
	for ($i = 0 ; $i < $beacon['slotTimeouts']; $i++){
		// Record one for each timeout
		EventRecorder::record(array('SlotTimeouts'), "minute");
		$counter++;
	}
}

echo $counter;
?>
