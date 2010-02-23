<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('memory_limit', "200M");
require dirname(__FILE__) . '/../../includes/Framework.php';
require dirname(__FILE__) . '/gapi/liftium.gapi.class.php';

$ga = new liftium_gapi();
/*
CREATE TABLE google_tag_stats (
	tag_id int(11) NOT NULL,
	stat_date date,
	views int(11) unsigned not null,
	PRIMARY KEY (tag_id, stat_date)
);
*/

// TODO: Only pull last 7 days worth of data instead of all of it
$results = $ga->getPageViewsByDate(ga_launch_date, null, "pagePath=~/tags/");
$db = Framework::getDB("master");

/* output:
  [/1052/tags/118/attempt] => Array
        (
            [20091229] => 5255
            [20091230] => 3556
            [20091231] => 6
	    ...
  ...
*/

$recordsUpdated=0;
foreach($results as $page => $pagedata) {
	if (!preg_match('#/([0-9]+)/tags/([0-9]+)/attempt#', $page, $match)){
		echo "$page not a tag request\n";
		continue;
	}
	foreach ($pagedata as $date => $pageviews ){
		$sql = "INSERT INTO google_tag_stats VALUES('{$match[2]}', '" . date('Y-m-d', strtotime($date)) . "', '$pageviews') 
			ON DUPLICATE KEY UPDATE views='$pageviews'";
       		if ($db->exec($sql)){
                	$recordsUpdated++;
		}
        }

}

echo "Done. $recordsUpdated records updated\n";

