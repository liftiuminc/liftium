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
		$criteria['brand_safety_level_check'] = true;
		foreach ($AdTag->getSizes() as $size){
			$criteria['size'] = $size;
			$tags = AdTag::searchTags($criteria, false);
			foreach($tags as $tag_id){
				$tag = $this->loadTagFromId($tag_id);
				if (AdTag::isUnderDailyLimit($tag_id, @$tag['max_daily_impressions'])){
					$object->sizes[$size][] = $tag;
				} else {
					if (!empty($_GET['debug'])){
						echo "tag #$tag_id skipped because it's over daily limit";
					}
				}
			}
		}

		// Pull publisher level info
		$dbr = Framework::getDB("slave");
		$sql = "SELECT hoptime, site_name, brand_safety_level,
			beacon_throttle FROM publishers WHERE id = ?";
		$sth = $dbr->prepare($sql);
		$sth->execute(array($criteria['pubid']));
		$publisher = $sth->fetchObject();
		unset($sth);
		$object->max_hop_time = $publisher->hoptime;
		$object->throttle = $publisher->beacon_throttle;
		$object->brand_safety_level = $publisher->brand_safety_level;
		$object->site_name = $publisher->site_name;

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
		static $sth;
		if (empty($sth)){
		  $sql = "SELECT networks.network_name, tags.id AS tag_id, tags.network_id,
			tags.tag, tags.always_fill, tags.sample_rate,
			tags.frequency_cap AS freq_cap, tags.size,
			tags.rejection_time as rej_time, tags.tier, tags.value, tags.floor,
			networks.tag_template, networks.pay_type
			FROM tags
			INNER JOIN networks ON tags.network_id = networks.id
			WHERE tags.id = ? LIMIT 1";
		  $sth_t = $dbr->prepare($sql);
		}
		$sth_t->execute(array($tag_id));
		$out = $sth_t->fetch(PDO::FETCH_ASSOC);

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

		static $sth_o;
		if (empty($sth_o)){
                  $sql = "SELECT option_name, option_value FROM tag_options WHERE tag_id = ?";
                  $sth_o = $dbr->prepare($sql);
		}
                $sth_o->execute(array($tag_id));
                while($row = $sth_o->fetch(PDO::FETCH_ASSOC)){
                        $tag_options[$row['option_name']]=$row['option_value'];
                }


		// Abbreviate pay_type
		switch ($out['pay_type']){
		  case "Per Impression" : $out['pay_type'] = "CPM"; break;
		  case "Per Click" : $out['pay_type'] = "CPC"; break;
		  case "Affliate" : $out['pay_type'] = "CPA"; break;
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
		// Cheap and easy: Remove the leading white space. That's never needed.
		$out['tag'] = preg_replace('/^[ \t]+/m', '', $out['tag']);

		// Get the Targeting criteria
		$out['criteria'] = TargetingCriteria::getCriteriaForTag($tag_id);

		if (!empty($_GET['debug'])){
			print_r($out);
		}

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

	static public function getLastUpdated() {
		$db = Framework::getDB("slave");
		$result = $db->query("SELECT UNIX_TIMESTAMP(MAX(updated_at)) as lastmod FROM tags;");
		$row = $result->fetch();
		return intval($row[0]);
	}
}
?>
