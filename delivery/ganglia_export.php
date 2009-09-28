<?php
/* Simple script to dump exports that Cacti will come along and harvest for logging/graphing */
header('Content-Type: text/plain');
require_once dirname(__FILE__) . '/../includes/Framework.php';


switch (@$_GET['list_type']){
	case 'network': echo getTagsByNetwork(@$_GET['network_id']); break;
	case 'misc': echo getMiscStats(); break;
	case 'browser_fills': echo getStatsByBrowser(); break;
	default: echo getUrlList();
}

function getUrlList(){
	$networks = AdNetwork::searchNetworks(array('enabled'=>1));
	$base = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";
	foreach($networks as $network){
		echo "$base?list_type=network&network_id=" . $network->network_id . "\n";
	}
//	echo "$base?list_type=browser_fills&network_id=browsers\n";
	
}

function getStatsByBrowser(){
	$cache = LiftiumCache::getInstance();
	global $BROWSERS;
	foreach ($BROWSERS as $browser) {
		$key = EventRecorder::serializeKey(array('Attempt', $browser), "minute", strtotime("-61 seconds"));
		$attempts = intval($cache->get($key));
		if (empty($attempts)){
			continue;
		}
		$key = EventRecorder::serializeKey(array('Load', $browser), "minute", strtotime("-61 seconds"));
		$loads = intval($cache->get($key));
		$key = EventRecorder::serializeKey(array('Reject', $browser), "minute", strtotime("-61 seconds"));
		$rejects = intval($cache->get($key));
		$safe_browser = preg_replace('/[^A-Za-z0-9]/', '_', $browser);
		echo "tag_id:Browser_$safe_browser" . 
			" tag_name:Browser_$safe_browser" .
			" attempts: $attempts" .
			" loads: $loads" .
			" rejects: $rejects" . "\n";
	}

}

function getTagsByNetwork($network_id){

	$tags = AdTag::searchTags(array('enabled'=>1, 'network_id'=>$network_id));
	foreach ($tags as $tag){
		$stats = $tag->getFillStats(array('minute'=>strtotime('-90 seconds')));
		$safe_tag_name = preg_replace('/[^A-Za-z0-9]/', '_', $tag->tag_name);
		echo "tag_id:{$tag->tag_id} tag_name:$safe_tag_name" .
			" attempts: {$stats['attempts']}" .
			" loads: {$stats['loads']}" .
			" rejects: {$stats['rejects']}" . "\n";
	}
}


function getMiscStats(){
	$cache = LiftiumCache::getInstance();

	// Number of beacon calls per minute
	$key = EventRecorder::serializeKey(array('BeaconCalls'), "minute", strtotime("-61 seconds"));
	echo "BeaconCalls: " . intval($cache->get($key)) . "\n";

	// Remnant Revenue
	$remnantRevenue = getRemnantRevenue();
	echo "RemnantRevenue: $remnantRevenue\n";
        // Set a memcache key for use by the dashboard
        $cache->set("RemnantECPM", $remnantRevenue, 300);

	// Total Ads Served
	$key = EventRecorder::serializeKey(array('TotalAdsServed'), "minute", strtotime("-61 seconds"));
	$tas = intval($cache->get($key));
	echo "TotalAdsServed: " . $tas . "\n";

	// Total Hops
	$key = EventRecorder::serializeKey(array('TotalHops'), "minute", strtotime("-61 seconds"));
	$hops = intval($cache->get($key));
	echo "TotalHops: " . $hops . "\n";

	// Fill/reject ratio
	if ($tas < 1){
		$ratio = 0;
	} else {
		$ratio = round ($hops/$tas, 2);
	}
	echo "HopsPerAd: " . $ratio . "\n";
	
	echo "AdTime: " . getAdTime() . "\n";

	echo "PageTime: " . getPageTime() . "\n";

	$key = EventRecorder::serializeKey(array('JavascriptErrors'), "minute", strtotime("-61 seconds"));
	$errors = intval($cache->get($key));
	echo "JavascriptErrors: " . $errors . "\n";

	$key = EventRecorder::serializeKey(array('TagErrors'), "minute", strtotime("-61 seconds"));
	$errors = intval($cache->get($key));
	echo "TagErrors: " . $errors . "\n";

//	adNetworkRespTimes();

	// Slot Timeouts. I hear there are a lot of sluts in Los Vegas. Huh huh. Huh huh.
	$key = EventRecorder::serializeKey(array('SlotTimeouts'), "minute", strtotime("-61 seconds"));
	$timeouts = intval($cache->get($key));
	echo "SlotTimeouts: " . $timeouts . "\n";

	//getBrowserErrors();
}


function getBrowserErrors(){
	global $BROWSERS;

	$cache = LiftiumCache::getInstance();
	foreach ($BROWSERS as $browser) {
		$name = "JavascriptErrors_$browser";
		$key = EventRecorder::serializeKey(array($name), "minute", strtotime("-61 seconds"));
		$errors = intval($cache->get($key));
		echo "$name: " . $errors . "\n";
	}
}


function getRemnantRevenue(){
	$tags = AdTag::searchTags(array('enabled'=>1));
	$totalDollars = 0; $totalLoads = 0;
	foreach($tags as $tag){
		$stats = $tag->getFillStats(array('minute'=>strtotime('-90 seconds')));
		if ($tag->value < 6 && $tag->value > .05 && $tag->network_id != 1 ){ // Exclude experiments
                        // Add up the totals to get a guess at CPM
                        $totalDollars += $stats['loads'] * $tag->value;
                        $totalLoads += $stats['loads'];
                }
	}
	if ($totalLoads == 0){
		return 0;
	} else {
		return round($totalDollars/$totalLoads, 2);
	}
}

// A histogram would be cooler than average, but this is better than nothing
/* No longer done this way, but left here for reference
function getAverageAdsPerPage(){
	$cache = LiftiumCache::getInstance();

	$i = 1; $totalPages = 0; $totalAds = 0;
	while ($i < 10){
		$i+=0.1;
		$key = EventRecorder::serializeKey(array('AdsPerPage', $i), "minute", strtotime("-61 seconds"));
		$val = intval($cache->get($key));
		
		if ($val > 0){
			$totalPages += $val; 	
			$totalAds += $val * $i; 	
		}
	}

	if ($totalPages > 0){
		$avg = round($totalAds / $totalPages, 1);
		echo "AdsPerPage: " . $avg . "\n";
	}

}
*/

function getAdTime(){
	$cache = LiftiumCache::getInstance();
	$i = 0; $totalPages = 0; $totalTimes = 0;
	while ($i < 30){
		$i+=0.1;
		$key = EventRecorder::serializeKey(array('AdTime', $i), "minute", strtotime("-61 seconds"));
		$val = intval($cache->get($key));
		
		if ($val > 0){
			$totalPages += $val; 	
			$totalTimes += $val * $i; 	
		}
	}

	if (!empty($totalPages)) {
		$avg = round($totalTimes / $totalPages, 2);
	} else {
		$avg = 0;
	}

	return $avg;
}

function getPageTime(){
	$cache = LiftiumCache::getInstance();
	$i = 0; $totalPages = 0; $totalTimes = 0;
	while ($i < 30){
		$i+=0.1;
		$key = EventRecorder::serializeKey(array('PageTime', $i), "minute", strtotime("-61 seconds"));
		$val = intval($cache->get($key));

		if ($val > 0){
			$totalPages += $val;
			$totalTimes += $val * $i;
		}
	}

	if (!empty($totalPages)) {
		$avg = round($totalTimes / $totalPages, 2);
	} else {
		$avg = 0;
	}

	return $avg;
}

function adNetworkRespTimes(){
	$Athena = new Athena();
	$networks = AdNetwork::searchNetworks(array('enabled'=>1));
	foreach ($networks as $network){
		if (AdNetwork::loadNetworkClass($network->network_name) === false){
			// Not a supported Ad Network
			continue;
		}
		echo "RespTime_{$network->network_name}: " . getAdNetworkAverage($network->network_name) . "\n";
	}
}

function getAdNetworkAverage($network_name){
	$cache = LiftiumCache::getInstance();
	$i = 0; $totalPages = 0; $totalTimes = 0;
	while ($i < 30){
		$i+=0.1;
		$key = EventRecorder::serializeKey(array('AdNetworkRespTime', $network_name, $i), "minute", strtotime("-61 seconds"));
		$val = intval($cache->get($key));

		if ($val > 0){
			$totalPages += $val;
			$totalTimes += $val * $i;
		}
	}

	if (!empty($totalPages)) {
		$avg = round($totalTimes / $totalPages, 2);
	} else {
		$avg = 0;
	}

	// Set this variable for use by the config
        $cache->set("RespTime_$network_name", $avg, 600);

	return $avg;
}
?>
