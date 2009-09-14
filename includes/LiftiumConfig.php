<?php

class LiftiumConfig{

	const cacheTimeout = 15;
	const cacheTimeout_tag = 30;

	function __construct(){
		AdNetwork::loadNetworkClass("Null"); // Set up the error handling class
	}

	public function getConfig($criteria = array()){

		$cache = LiftiumCache::getInstance();
		$AdTag = new AdTag();
		$cacheKey = __CLASS__ . ':' . __METHOD__ . ":" . md5(serialize($criteria)) . self::getCacheVersion();

		$object = $cache->get($cacheKey);
		if (!empty($object) && empty($_GET['purge'])){
			return $object;
		}

		// Cache miss, get from DB
		$object = new stdClass();

		// Pull tags
		$criteria['enabled'] = 1;
		foreach ($AdTag->getSizes() as $size){
			$criteria['size'] = $size;
			$tags = AdTag::searchTags($criteria, false);
			foreach($tags as $tag_id){
				$object->sizes[$size][] = $this->loadTagFromId($tag_id, $size);
			}
		}

		// Store in memcache for next time
		$cache->set($cacheKey, $object, 0, self::cacheTimeout);

		return $object;
	}

	public function loadTagFromId($tag_id, $size, $slotname = null){
                $cache = LiftiumCache::getInstance();
		$cacheKey = __CLASS__ . ':' . __METHOD__ . ':' . self::getCacheVersion() . ":$tag_id:$size:$slotname";

		$out = $cache->get($cacheKey);
		if (!empty($out) && empty($_GET['purge'])){
			return $out;
		}

		// Cache miss, get from DB
		$dbr = Framework::getDB("slave");
		// TODO: Make these prepared statements for performance
		$sql = "SELECT networks.network_name, tags.id AS tag_id, tags.network_id, tags.tag,
			tags.always_fill, tags.sample_rate, tags.frequency_cap AS freq_cap, tags.rejection_cap AS rej_cap,
			tags.rejection_time as rej_time, tags.tier, tags.value
			FROM tags
			INNER JOIN networks ON tags.network_id = networks.id
			WHERE tags.id = " . $dbr->quote($tag_id) . " LIMIT 1;";
		foreach ($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			$out = $row;
		}
		if (empty($out)){
			return false;
		}

		// Get the network options
		/*
		$sql = "SELECT option_name, option_value
			FROM tag_option WHERE tag_id =" . $dbr->quote($tag_id) . ";";
		$network_options = array();
		foreach ($dbr->query($sql) as $row){
			$network_options[$row['option_name']]=$row['option_value'];
		}
		*/

		/*
		// Get the slot names
		$sql = "SELECT slot FROM ad_slot
			INNER JOIN tag_slot_linking ON ad_slot.as_id = tag_slot_linking.as_id
			WHERE tag_slot_linking.tag_id =" . $dbr->quote($tag_id) . ";";
		$out['slotnames'] = Array();
		foreach ($dbr->query($sql) as $row){
			$out['slotnames'][] = $row['slot'];
		}
		*/

		$out['size'] = $size;
		$out['value'] = round($out['value'], 2);

		// Fill in the tag if it is one of the pre-defined networks
		if (empty($out['tag']) && !empty($out['network_id'])){

			$class = AdNetwork::loadNetworkClass($out['network_name']);
			if ($class === false){
				$out['tag'] = "<!-- Error loading Network class for {$out['network_name']} -->";
			} else {
				$AN = new $class();
				$out['tag'] = $AN->getAd($slotname, $size, $network_options);
			}
		}

		// Make the 'tag' smaller. Someday: Pack the javascript
		// Cheap and easy: Remove the leading/trailing white space. That's never needed.
		$out['tag'] = preg_replace('/^[ \t]+/m', '', $out['tag']);

		// Get the Targeting criteria
		//$out['criteria'] = TargetingCriteria::getThinCriteriaForTag($tag_id);


		// Slim it down for compactness
		$slimout = array();
		foreach ($out as $key => $value){
			if (!empty($value)){
				$slimout[$key] = $value;
			}
		}

		// Store in memcache for next time
		$cache->set($cacheKey, $slimout, 0, self::cacheTimeout_tag);

		return $slimout;
	}

	/* Pull the current list of networks
	 */
	public function getNetworkList(){
                $cache = LiftiumCache::getInstance();
		$cacheKey = __CLASS__ . ':' . __METHOD__ . ":" . self::cacheVersion . ":";

		$out = $cache->get($cacheKey);
		if (!empty($out) && empty($_GET['purge'])){
			return $out;
		}

		// Cache miss, get from DB
		$dbr = Framework::getDB("slave");
		$networks = AdNetwork::searchNetworks(array('enabled'=>'Yes'));

		$out = array();
		foreach($networks as $network){
			$out[] = array(
				'network_id' => $network->network_id,
				'network_name' => $network->network_name
			);
		}


		// Store in memcache for next time
		$cache->set($cacheKey, $out, 0, self::cacheTimeout);

		return $out;
	}



	static public function getCountryList(){
                $cache = LiftiumCache::getInstance();
		$cacheKey = __CLASS__ . ':' . __METHOD__ . ":" . self::cacheVersion . ":";

		$out = $cache->get($cacheKey);
		if (!empty($out) && empty($_GET['purge'])){
			return $out;
		}

		// Cache miss, get from DB
		$dbr = Framework::getDB("slave");
		$sql = "SELECT target_keyvalue FROM target_value WHERE
			target_key_id = (SELECT target_key_id FROM target_key WHERE target_keyname = 'Geography')
			ORDER BY length(target_keyvalue), target_keyvalue;";
		$out = array();
                foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
                        $out[] = $row['target_keyvalue'];
                }

		// Store in memcache for next time
		$cache->set($cacheKey, $out, 0, 99);

		return $out;
		
	}

	public static function clearCache(){
		trigger_error("Cache not implented", E_USER_WARNING);
		//file_get_contents("http://athena-ads.wikia.com/athena/config/?purge=1&cb=" . mt_rand());
	}


	static private function getCacheVersion(){
		return mt_rand();
		return "1.0r";
	}
}
?>
