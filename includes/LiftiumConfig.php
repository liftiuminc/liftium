<?php

class LiftiumConfig{

	const cacheTimeout = 15;
	const cacheTimeout_tag = 30;

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
				$object->sizes[$size][] = $this->loadTagFromId($tag_id);
			}
		}

		// Pull beacon throttle
		$dbr = Framework::getDB("slave");
		$sql = "SELECT beacon_throttle FROM publishers WHERE id = ?";
		$sth = $dbr->prepare($sql);
		$sth->execute(array($criteria['pubid']));
		list($throttle) = $sth->fetch();
		unset($sth);
		$object->throttle = $throttle;

		// Store in memcache for next time
		$cache->set($cacheKey, $object, 0, self::cacheTimeout);

		return $object;
	}

	public function loadTagFromId($tag_id){
                $cache = LiftiumCache::getInstance();
		$cacheKey = __CLASS__ . ':' . __METHOD__ . ':' . self::getCacheVersion() . ":$tag_id";

		$out = $cache->get($cacheKey);
		if (!empty($out) && empty($_GET['purge'])){
			return $out;
		}

		// Cache miss, get from DB
		$dbr = Framework::getDB("slave");
		// TODO: Make these prepared statements for performance
		$sql = "SELECT networks.network_name, tags.id AS tag_id, tags.network_id,
			tags.tag, tags.always_fill, tags.sample_rate,
			tags.frequency_cap AS freq_cap, tags.size,
			tags.rejection_time as rej_time, tags.tier, tags.value,
			networks.tag_template
			FROM tags
			INNER JOIN networks ON tags.network_id = networks.id
			WHERE tags.id = ? LIMIT 1";
		$sth = $dbr->prepare($sql);
		$sth->execute(array($tag_id));
		$out = $sth->fetch(PDO::FETCH_ASSOC);
		unset($sth);

		if ($out === false){
			return false;
		}

                // Get the tag options
		$dim = AdTag::getHeightWidthFromSize($out['size']);
		$tag_options = array(
			'size' => $out['size'],
			'width' => $dim['width'],
			'height' => $dim['height']
		);
                $sql = "SELECT option_name, option_value
                        FROM tag_options WHERE tag_id = ?";
                $sth = $dbr->prepare($sql);
                $sth->execute(array($tag_id));
                while($row = $sth->fetch(PDO::FETCH_ASSOC)){
                        $tag_options[$row['option_name']]=$row['option_value'];
                }


		/*
		// Get the slot names
		$sql = "SELECT slot FROM ad_slot
			INNER JOIN tag_slot_linking ON ad_slot.as_id = tag_slot_linking.as_id
			WHERE tag_slot_linking.tag_id = ?";
		$sth = $dbr->prepare($sql);
		$sth->execute(array($tag_id));
		$out['slotnames'] = Array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			$out['slotnames'][] = $row['slot'];
		}
		*/

		if (!empty($_GET['debug'])){
			print_r($out);
			print_r($tag_options);
		}

		// If the tag exists already, pass it through the expander 
		if (!empty($out['tag'])){
			$out['tag'] = AdTag::expandMacros($out['tag'], $tag_options);
			unset($out['tag_template']);
		} else if (!empty($out['tag_template'])){
			$out['tag'] = AdTag::expandMacros($out['tag_template'], $tag_options);
			unset($out['tag_template']);
		} else {
			$class = AdNetwork::getNetworkClass($out['network_id']);
			if ($class === false){
				$msg = "Error finding Network Class and no tag for tagid #{$out['tag_id']}";
				trigger_error($msg, E_USER_WARNING);
				$out['tag'] = $msg;
			} else {
				$AN = new $class();
				$out['tag'] = $AN->getAd($slotname, $size, $tag_options);
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
			ORDER BY length(target_keyvalue), target_keyvalue";
		$sth = $dbr->prepare($sql);
		$sth->execute();
		$out = array();
                while($row = $dbr->fetch(PDO::FETCH_ASSOC)){
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
		if (Framework::isDev()) {
			return mt_rand();
		} else {
			return "1.0r";
		}
	}
}
?>
