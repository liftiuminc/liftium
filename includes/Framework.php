<?php
// F U Symfony
define('MYSQL_DATE_FORMAT', 'Y-m-d H:i:s');
date_default_timezone_set('UTC'); // This suppresses E_STRICT notices when strtotime is called

// Be strict and loud in dev environments, and prudent in production
if (in_array(getHostname(), $DEV_HOSTS)){
	error_reporting(E_STRICT | E_ALL);
	// error_reporting(E_ALL);
	ini_set('display_errors', true);
} else {
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', false);
}
ini_set('log_errors', true);
$DEV_HOSTS = array();

class Framework {

	// I'd like to see a SERVICECLASS variable here instead of hard coded hostnames. Later.

	static public function getDB ($connType = "master"){
		global $DEV_HOSTS;
		if (in_array(getHostname(), $DEV_HOSTS)){
			$masterhost = 'localhost';
			$slavehosts = array('localhost');
			$username = 'liftiumdev';
			$password = 'monkey';
		} else {
			$masterhost = 'liftiumdb1';
			$slavehosts = array('liftiumdb1', 'liftiumdb2');
			$username = 'liftiumprod';
			$password = 'gorilla';
		}

		// Randomly choose between slave hosts
		shuffle($slavehosts);
		$slavehost = $slavehosts[0];

		switch ($connType){
		  case 'master': $dsn = "mysql:dbname=liftium;host=$masterhost"; break;
		  case 'slave': $dsn = "mysql:dbname=liftium;host=$slavehost"; break;
		  default: return false;
		}

		static $cons;
		if (empty($cons[$connType])){
			$cons[$connType] = new PDO($dsn, $username, $password);
			$cons[$connType]->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$cons[$connType]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}

		return $cons[$connType];
	}

	// We can't always count on ENV('HOSTNAME') being set
	static public function getHostname(){
		static $hostname;
		if (!empty($hostname)){
			return $hostname;
		} else {
			$hostname = trim(`hostname`);
			return $hostname;
		}
	}


	static public function pretty_array($array, $length = null, $separator = ', '){
		$h = '';
		if (!is_array($array)){
			// This isn't going to work out
			return false;
		} else if (isset($array[0])){
			// Numeric array, just list with implode
			$h = implode($separator, $array);
		} else {
			foreach($array as $key => $value){
				$h = "$key : " . self::pretty_array($value, $length, $separator) . "<br />";
			}
		}
		if (!empty($length) && strlen($h) > $length){
			$h = substr($h, 0, $length) . '...';
		}
		return $h;
	}

	static public function cacheDump($filename, $host,$port = 11211, $pattern = null) {
	      $fd = fopen($filename,"w+");
	      $sock = stream_socket_client("tcp://$host:$port");

	      fwrite($sock,"stats items\r\n");
	      $items = array();
	      $totalitems = 0;
	      while($buffer=fgets($sock,4096)){
		if (preg_match("/^STAT items:(\d*):number (\d*)/",$buffer, $args)) {
		  $items[$args[1]] = $args[2];
		  $totalitems += $args[2];
		}
		if ($buffer === "END\r\n")
		  break;
	      }
	      ksort($items);

	      foreach ($items as $bucket => $value) {
		fwrite($sock,"stats cachedump $bucket $items[$bucket]\r\n");
		// build key list
		$keyexp = array();
		while($buffer=fgets($sock,4096)){
		  if (preg_match("/^ITEM (.*) \[.* (\d+) s\]/",$buffer, $args)) {
		    $keyexp[$args[1]] = $args[2];
		  }
		  if ($buffer == "END\r\n")
		    break;
		}
		// loop through keys
		foreach ($keyexp as $key => $expire) {
		  fwrite($sock,"get $key\r\n");
		  $value = stream_get_line($sock,4096, "\r\n");
		  // does key exist? could of expired...
		  if ($value == "END")
		  {
		    continue;
		  }

		  preg_match("/VALUE (.*) (\d+) (\d+)/",$value,$args);
		  $flags = $args[2];
		  $vlen = $args[3];
		  if ($vlen > 0)
		  {
		    $data = fread($sock,$vlen);
		  }
		  $end = fread($sock,7);
		  if($vlen > 0) fwrite($fd,"add $key $flags $expire $vlen\r\n$data\r\n");
		}
	      }
	      fclose($fd);
	}

	static public function getIp(){
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if (!empty($_SERVER['REMOTE_ADDR'])){
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return false;
		}
	}

	static public function getTimeAgo($eventTime){
		$secondsAgo = strtotime('now') - strtotime($eventTime);
		if ($secondsAgo < 120){
			return "$secondsAgo seconds ago";
		} else if ($secondsAgo < (150 * 60)){
			return round($secondsAgo/60) .  " minutes ago";
		} else if ($secondsAgo < (60 * 60 * 36)){
			return "About " . round($secondsAgo / 3600) . " hours ago";
		} else {
			return "About " . round($secondsAgo / 86400) . " days ago";
		}

	}

	static public function getBrowser($ua = null){
		if (is_null($ua)){
			$ua = $_SERVER['HTTP_USER_AGENT'];
		}

		if (preg_match("#Firefox/3#", $ua)){
			return "Firefox 3";
		} else if (preg_match("#Firefox/2#", $ua)){
			return "Firefox 2";
		} else if (preg_match("/MSIE 8/", $ua)){
			return "IE 8";
		} else if (preg_match("/MSIE 7/", $ua)){
			return "IE 7";
		} else if (preg_match("/MSIE 6/", $ua)){
			return "IE 6";
		} else if (preg_match("/Chrome/", $ua)){
			// Note that Chrome user agent contains Safari, so keep this above the safari check
			return "Chrome";
		} else if (preg_match("/Safari/", $ua)){
			return "Safari";
		} else if (preg_match("/Gecko/", $ua)){
			return "Mozilla";
		} else {
			return "Other";
		}
	}

}

