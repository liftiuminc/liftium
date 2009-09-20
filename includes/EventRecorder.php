<?php
/* Store events in a temporary, high performance store, to be aggregated later.
 * Currently using memcache for this later. Maybe someday the message queue?
 *
 * Example usage:
 * EventRecorder::record(array('Ad Delivered', 'AdBrite', 'US'), "minute");
 */

class EventRecorder {

	static public function record($event, $rotation = "hour", $timeout = null){
		$cache = LiftiumCache::getInstance();
		if (!is_array($event)){
			trigger_error('$event is not an array for EventRecorder::record', E_USER_WARNING);
			return false;
		}

		$key = self::serializeKey($event, $rotation);

		$ret = $cache->increment($key);
		if ($ret === false){
			// Increment does not create the key if it does not exist. Retarded.
			if ($timeout === null){
				$timeout = self::getTimeOut($rotation);
			}
			$cache->set($key, 0, 0, $timeout);
			$ret = $cache->increment($key);
		}

		return $ret;
	}

	static public function serializeKey($event, $rotation = "hour", $time = null){
		$key = "";
		foreach ($event as $e){
			$key .= urlencode($e) . ':';
		}
		if (is_null($time)){
			$time = time();
		}
		return $key . self::getTimeFloor($rotation, $time);
	}

	public function unserializeKey($key){
		$pieces = explode(':', $key);
		if (empty($pieces)){
			return false;
		}

		$time = array_pop($pieces);
		$events = array();
		foreach ($pieces as $piece){
			$events[] = urldecode($piece);
		}
		
		$out = array(
			'events'=> $events,
			'time' => $time,
			'rotation' => self::getRotation($time)
		);
	}

	static public function getTimeFloor($rotation, $time){
		switch($rotation){
		  case 'minute': return date('Ymd_Hi00', $time);
		  case 'hour': return date('Ymd_H', $time);
		  case 'day': return date('Ymd', $time);
		  default: return null;
		}
	}	

	public function getRotation($time){
		switch(strlen($time)){
		  case 15: return 'minute';
		  case 11: return 'hour';
		  case 8: return 'day';
		  default: return null;
		}
	}

	public static function getTimeOut($rotation){
		switch($rotation){
		  case 'minute': return 360;
		  case 'hour': return 86400;
		  case 'day': return 86400 * 7;
		  default: return 300;
		}
	}	


	public function getEventsByType($eventType){
		// Pull a list of keys from memcached
		$cache = LiftiumCache::getInstance();
		$keylist = getCacheKeyList();
	
		$out = array();
		for ($i = 0 ; $i<sizeof($keylist); $i++){
			$data = self::unserializeKey($keylist[$i]);
			if ($data['events'][0] == $mainevent){
				$out = $data;
			}
		}	
		return $out;
	}
}
?>
