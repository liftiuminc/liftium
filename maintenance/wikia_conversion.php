<?php
require_once '../includes/Framework.php';
$db = Framework::getDB("master");
?>
TRUNCATE TABLE publishers; 
INSERT INTO publishers VALUES (999, 'Wikia', 'http://www.wikia.com/', 3, 2700, NOW(), NOW(), 1, '/liftium_iframe.html', NULL, 1, '', '', NULL);

TRUNCATE TABLE users; 
INSERT INTO users VALUES (42, 'nick@liftium.com', 1, 1, 'a5182110acf4a2224c7361fb2ff237e63c8583b2907463fed895c73bbef11cb2981cd74ce9857e9d98cf6212a661915c3dc5a79ba731e28ac286341d7052d7aa', '5TdqNuHG4dBtTppTJjEv', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NOW(), NOW(), 'Pacific Time (US & Canada)', 1);

TRUNCATE TABLE ad_formats; 
INSERT INTO ad_formats SELECT * from liftium.ad_formats;

TRUNCATE TABLE networks; 
INSERT INTO networks SELECT * from liftium.networks;

TRUNCATE TABLE network_tag_options; 
INSERT INTO network_tag_options SELECT * from liftium.network_tag_options;

CREATE TABLE IF NOT EXISTS network_map (athena_id int, liftium_id int);
TRUNCATE TABLE network_map; 
INSERT INTO network_map VALUES 
	(1, 104), /* DART */
	(3, 4), /* AdBrite */
	(5, 6), /* GAO */
	(6, 1), /* AdSense */
	(8, 45), /* ValueClick */
	(11, 42), /* Test */
	(12, 105), /* NOad */
	(13, 106), /* RightMedia */
	(15, 2), /* TF */
	(17, 42), /* Wiki specific, mapped to test */
	(19, 76), /* Natural Path */
	(22, 107), /* Zujo */
	(23, 108), /* OpenX Exchange */
	(24, 52), /* 24/7 */
	(34, 68), /* Specific */
	(37, 109), /* AdJug */
	(41, 110), /* Premium Access */
	(42, 111), /* Olive */
	(44, 46), /* VIdeoEGG */
	(48, 96); /* Technorati */

TRUNCATE TABLE tags; 
<?php
function getTier($athena_tier){
	switch ($athena_tier) {
	  case 10: return 1;
	  case 9: return 2;
	  case 8: return 3;
	  case 7: return 4;
	  case 6: return 5;
	  case 5: return 6;
	  case 4: return 7;
	  case 3: return 8;
	  case 2: return 9;
	  default : return 10;
	}
}
$db->query("USE liftiumwikia;");
$st = $db->prepare("SELECT tag.*, network_map.liftium_id
	FROM athena.tag
	LEFT OUTER JOIN network_map ON tag.network_id = liftiumwikia.network_map.athena_id
	WHERE enabled = 'Yes'");
$st->execute(); 
$tagid=0;
while($row = $st->fetch(PDO::FETCH_ASSOC)){
	$tagid++; 
	if (empty($row['liftium_id'])){
		echo "/* No Network id found for {$row['network_id']}*/\n";
		continue;
	}

	echo "INSERT INTO tags VALUES(" . $tagid . "," .
		$db->quote($row['tag_name']) . "," .
		$db->quote($row['liftium_id']) . "," .
		$db->quote(999) . "," .
		$db->quote($row['estimated_cpm'] + $row['threshold']) . "," . 
		$db->quote(1) . "," .
		$db->quote($row['guaranteed_fill'] == "Yes" ? 1 : 0) . "," .
		$db->quote($row['sample_rate']) . "," .
		$db->quote(getTier($row['tier'])) . "," .
		$db->quote($row['freq_cap']) . "," .
		$db->quote(empty($row['rej_cap']) ? $row['rej_time'] : 42) . "," .
		$db->quote('3x5') . "," .
		$db->quote($row['tag']) . "," . 
		"NOW(), NOW(), 'No', NULL" .
	");\n";
		
}
?>

