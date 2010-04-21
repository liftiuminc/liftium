<?php
$origdb = "liftium_orig";
$destdb = "liftium";
require_once '../includes/Framework.php';
$db = Framework::getDB("master");
?>
USE <?php echo $destdb ?>;
TRUNCATE TABLE publishers; 
INSERT INTO publishers VALUES (999, 'Wikia', 'http://www.wikia.com/', 3, 2700, NOW(), NOW(), 1, '/liftium_iframe.html', NULL, 1, '', '', NULL);

INSERT IGNORE INTO users VALUES (42, 'nick@liftium.com', 1, 1, 'a5182110acf4a2224c7361fb2ff237e63c8583b2907463fed895c73bbef11cb2981cd74ce9857e9d98cf6212a661915c3dc5a79ba731e28ac286341d7052d7aa', '5TdqNuHG4dBtTppTJjEv', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NOW(), NOW(), 'Pacific Time (US & Canada)', 1);

TRUNCATE TABLE ad_formats; 
INSERT INTO ad_formats SELECT * from <?php echo $origdb ?>.ad_formats;

TRUNCATE TABLE networks; 
INSERT INTO networks SELECT * from <?php echo $origdb ?>.networks;

TRUNCATE TABLE network_tag_options; 
INSERT INTO network_tag_options SELECT * from <?php echo $origdb ?>.network_tag_options;

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
	(27, 112), /* GAM */
	(34, 68), /* Specific */
	(37, 109), /* AdJug */
	(41, 110), /* Premium Access */
	(42, 111), /* Olive */
	(44, 46), /* VIdeoEGG */
	(47, 5), /* Adsdaq = context web */
	(48, 96), /* Technorati */
	(52, 115), /* Barons */
	(53, 84); /* Vibrant */

TRUNCATE TABLE tags; 
TRUNCATE TABLE tag_options; 
TRUNCATE TABLE tag_targets; 
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
function getTargets($tagid) {
	// Tag Targets
	static $stt;
	if (empty($stt)){
		global $db;
		$stt = $db->prepare("SELECT target_keyname, target_keyvalue FROM athena.target_key
			INNER JOIN athena.target_value ON athena.target_key.target_key_id = athena.target_value.target_key_id
			INNER JOIN athena.target_tag_linking ON athena.target_tag_linking.target_value_id = athena.target_value.target_value_id AND tag_id=?;");
	}
	$stt->execute(array($tagid)); 
	$targets = array();
	while($targetrow = $stt->fetch(PDO::FETCH_ASSOC)){
		$value = $targetrow['target_keyvalue'];
		switch ($targetrow['target_keyname']) {
			case 'Geography': $name = 'country'; break;
			case 'browser': 
				$name = 'browser';
				if ($value =='ie') {
					$value = 'Explorer';
				}
				break;
			default: $name = 'kv_' . $targetrow['target_keyname'];
		}
		$targets[$name][] = $value;
	}
	return $targets;
}

function getSize($tagid){
	static $stt;
	if (empty($stt)){
		global $db;
		$stt = $db->prepare("SELECT DISTINCT size FROM athena.ad_slot
			INNER JOIN athena.tag_slot_linking ON athena.tag_slot_linking.as_id=athena.ad_slot.as_id
			INNER JOIN athena.tag ON athena.tag_slot_linking.tag_id = athena.tag.tag_id and athena.tag.tag_id=?;");
	}
	$stt->execute(array($tagid)); 
	while($row = $stt->fetch(PDO::FETCH_ASSOC)){
		return $row['size'];
	}
}

function getSizesAndSlots($tagid){
	static $stt;
	if (empty($stt)){
		global $db;
		$stt = $db->prepare("SELECT size, slot FROM athena.ad_slot
			INNER JOIN athena.tag_slot_linking ON athena.tag_slot_linking.as_id=athena.ad_slot.as_id
			INNER JOIN athena.tag ON athena.tag_slot_linking.tag_id = athena.tag.tag_id and athena.tag.tag_id=?;");
	}
	$out = array();
	$stt->execute(array($tagid)); 
	while($row = $stt->fetch(PDO::FETCH_ASSOC)){
		$out[$row['size']][] = $row['slotname'];
	}
	return $out;
}

$db->query("USE $destdb;");
$st = $db->prepare("SELECT tag.*, network_map.liftium_id
	FROM athena.tag
	LEFT OUTER JOIN network_map ON tag.network_id = network_map.athena_id
	WHERE enabled = 'Yes' AND
	network_id IN (SELECT network_id from athena.network where enabled='Yes')
	AND network_id NOT IN (45,30,46,31,49,50,51)");
$st->execute(); 

$sto = $db->prepare("SELECT * FROM athena.tag_option WHERE tag_id = ?");
$stp = $db->prepare("SELECT slot FROM athena.tag_slot_linking AS t
                        INNER JOIN athena.ad_slot AS a ON a.as_id = t.as_id
                         WHERE tag_id = ?");


$tagid=0;
while($row = $st->fetch(PDO::FETCH_ASSOC)){
  $tagid++; 
  if (empty($row['liftium_id'])){
	echo "/* No Network id found for {$row['network_id']}*/\n";
	exit;
  }

  $sizesAndSlots = getSizesAndSlots($tagid);

  foreach ($sizesAndSlots as $size => $slots) {
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
		$db->quote($size) . "," .
		$db->quote($row['tag']) . "," . 
		"NOW(), NOW(), 'No', NULL, NULL" .
	");\n";

	// Tag options
	$sto->execute(array($row['tag_id'])); 
	while($optionrow = $sto->fetch(PDO::FETCH_ASSOC)){
		$name = $optionrow['option_name'];
		echo "\tINSERT INTO tag_options VALUES(NULL, $tagid, " . $db->quote($name) . "," . $db->quote($optionrow['option_value']) . ");\n";
	}
	switch($row['liftium_id']){
		case '6':
		  switch ($size){
		    case '728x90': $id = 435; break;
		    case '300x250': $id = 391; break;
		    case '160x600': $id = 774; break;
		    case '120x600': $id = 436; break;
		    default: echo "Unrecognized size for GAO ($size)";
		  } 
		  echo "\tINSERT INTO tag_options VALUES(NULL, $tagid, 'id', '$id');\n";
		  break;
		case '106':
		  echo "\tINSERT INTO tag_options VALUES(NULL, $tagid, 'section', '415419');\n";
		  break;
		case '1':
		  echo "\tINSERT INTO tag_options VALUES(NULL, $tagid, 'pubid', 'pub-4086838842346968');\n";
		  break;
		case '4':
		  switch ($size){
		    case '300x250': $zs= '3330305f323530';break;
		    case '160x600': $zs= '3136305f363030';break;
		    case '728x90': $zs= '3732385f3930';break;
		    default: echo "Unrecognized size for AdBrite ($size)";
	  	  }
		  echo "\tINSERT INTO tag_options VALUES(NULL, $tagid, 'zs', '$zs');\n";
		  break;

	}
	


	foreach (getTargets($row['tag_id']) as $name => $value){
		echo "\tINSERT INTO tag_targets VALUES (NULL, $tagid, " . $db->quote($name) . "," .
			$db->quote(implode(',', $value)) . ");\n"; 
	}


	if (!empty($slots)){
		  echo "\tINSERT INTO tag_targets VALUES (NULL, $tagid, 'placement', '" . implode(',', $slots) . "');\n"; 
	}
	

	echo "\n";	
  }
		
}
?>
-- we called it accountid instead of sid
UPDATE networks SET tag_template='<script src="http://media.fastclick.net/w/get.media?sid=%@sid@%&m=%height%&tp=%@width@%&d=j&t=n"></script>' where id=45;

-- Set up dart's tag template
UPDATE networks SET tag_template='<script>
var creative = LiftiumDART.callAd(LiftiumOptions.placement, "%@size@%");
document.write(creative);
</script>' where id=104;

-- PNL
INSERT INTO publisher_network_logins SELECT NULL, liftium_id, 999, webui_username,
	 webui_password, now(), now() FROM Athena.network
	INNER JOIN network_map ON athena.network.network_id = network_map.athena_id
	WHERE webui_username !='' AND webui_password !='' AND network_id !=27;



UPDATE tags set sample_rate = NULL where sample_rate = 0;
UPDATE tags set frequency_cap = NULL where frequency_cap = 0;
UPDATE tags set rejection_time = NULL where rejection_time = 0;
/* Done */
