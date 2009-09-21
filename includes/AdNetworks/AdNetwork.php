<?php

class AdNetwork {

	public $network_id;
	public $network_name, $notes, $supports_threshold, $pay_type, $guaranteed_fill, $enabled;
	public $webui_login_url, $webui_username, $webui_password;


	public function __construct($network_id=null){
		if (!is_null($network_id)){
			$this->loadFromId($network_id);
		} else if (!empty($this->network_id)){
			$this->loadFromId($this->network_id);
		}
	}

	public function getNetworkName($network_id){
		static $networks = array();
		if (!empty($networks[$network_id])){
			return $networks[$network_id];
		}

		$AdNetwork = new AdNetwork($network_id);
		$networks[$network_id] = $AdNetwork->network_name;

		return $networks[$network_id];
	}

	public function loadFromId($network_id){
		$dbr = Framework::getDB("slave");
		$sql = "SELECT *, id AS network_id FROM networks WHERE id=" . $dbr->quote($network_id);
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			foreach($row as $column => $data){
				$this->$column = $data;
			}
		}
	}

	public function loadFromName($network_name){
		$dbr = Framework::getDB("slave");
		$sql = "SELECT * FROM networks WHERE network_name=" . $dbr->quote($network_name);
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			foreach($row as $column => $data){
				$this->$column = $data;
			}
		}
	}


	public function save(){
		if (empty($this->network_name)){
			trigger_error("Network Name must be supplied", E_USER_WARNING);
			return false;
		}

		$before = new AdNetwork($this->network_id);

		$columns = array('network_name', 'notes', 'enabled', 'supports_threshold', 'pay_type',
			'guaranteed_fill', 'webui_login_url', 'webui_username', 'webui_password');
		$set = '';
		$dbw = Framework::getDB("master");
		foreach ($columns as $col){
			if ($set != ''){
				$set .=",\n";
			}
			$set .= "\t$col = " . $dbw->quote($this->$col);
		}


		if (!empty($this->network_id)){
			$doUpdate = true;
		} else {
			// Idempotent
			$this->loadFromName($network_name);
			if (!empty($this->network_id)){
				$doUpdate = true;
			} else {
				$doUpdate = false;
			}
		}

		if ($doUpdate){
			$sql = "UPDATE network SET $set WHERE network_id = " . $dbw->quote($this->network_id);
			$change_type = "Update";
			$change_desc = "Ad Network #" . $this->network_id . ' Updated';

		} else {
			$sql = "INSERT INTO network SET network_id = NULL, $set";
			$change_type = "Create";
			$change_desc = "Ad Network Created";
		}

		$ret = $dbw->exec($sql);
		$this->loadFromName($this->network_name);

                // Change log
                $ChangeLog = new ChangeLog();
                $ChangeLog->setUser();
                $diff = $ChangeLog->getDiff($before, $this);
                if (!empty($diff)){
                        $ChangeLog->recordChange($change_type, 'Network', $this->network_id,
                                json_encode($diff), $change_desc);

                }

		Athena::clearCache();
		return $ret;

	}

	public function delete(){
		$dbw = Framework::getDB('master');
		$sql = "DELETE FROM networks WHERE network_id = " . $dbw->quote($this->network_id);
		$ret = $dbw->exec($sql);

                // Change log
                $ChangeLog = new ChangeLog();
                $ChangeLog->setUser();
                $diff = $ChangeLog->getDiff($this, new AdNetwork());
                if (!empty($diff)){
                        $ChangeLog->recordChange('Delete', 'Network', $this->network_id,
                                json_encode($diff), 'Ad Network Deleted');

                }

		return $ret;
	}


	static public function searchNetworks($criteria=array()){
		$dbr = Framework::getDB("slave");
		$sql = "SELECT id AS network_id FROM networks WHERE 1=1";
		if (!empty($criteria['name_search'])){
			$search = '%' . $criteria['name_search'] . '%';
			$sql.= "\n\tAND network_name like " . $dbr->quote($search) . " ";
		}

		if (!empty($criteria['enabled'])){
			$sql.= "\n\tAND enabled = " . $dbr->quote($criteria['enabled']) . " ";
		}

		$sql.= " ORDER BY network_name";

		$out = array();
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			$out[] = new AdNetwork($row['network_id']);
		}

		return $out;
	}


        /* Poor man's auto-loader, with error checking. This may be a performance challenge. Verify. */
        static public function loadNetworkClass($network){
                if (empty($network)){
                        // Huh?
                        return false;
                }

                $class = 'AdNetwork' . $network;
                if (class_exists($class)){
                        // Already loaded
                        return $class;
                }

		global $IP;
                $file = $IP . '/AdNetworks/' . $class . '.php';
                if (!file_exists($file)){
                        return false;
                } else {
                        return $class;
                }
        }


	/*** Functions for displaying tags ***/

	public $slotsToCall;

        public function getAd($slotname, $size, $network_options=array()){
		return "class does not implement getAd";
	}

        public function addSlotToCall($slotname) { 
		$this->slotsToCall[] = $slotname;
	}

	public function iframeHtml($url, $width, $height){
		// Note that Athena.js regexp matches the src here. Be careful of changes
               return '<iframe width="' . intval($width) . '" height="' . intval($height) . '"
		noresize="true" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"
		src="' . htmlspecialchars($url) . '" style="border:none" target="_blank"></iframe>';
	}

	public function loadScript($url) {
		return '<script type="text/javascript" src="' . htmlspecialchars($url) . '"></script>';
	}

	public function loadScriptJs($url){
		return 'document.write("<script src=' . $url . '><\/script>");';
	}

        public function batchCallAllowed() { return false; }
        public function getBatchCallHtml() { return null; }
        public function getSetupHtml() { return null; }
        public function getTagOptions() { return array(); }
	public function needsIframeWrapper(){ return true; }


	/*** Functions for scraping/interacting with api ***/

	public $isLoggedIn = false;

	public function getReportedCpmForTag($AdTag){
		return false;
	}

	public function getReportedFillStatsForTag($AdTag){
		return false;
	}

	public function login(){
		return false;
	}

	public function updateEcpms(){
		$this->login();

		$network = new AdNetwork($this->network_id);
		$tags = AdTag::searchTags(array('network_id'=>$this->network_id, 'enabled'=>1));

		$updates = 0;
		foreach ($tags as $tag){
			$real_ecpm = $this->getReportedCpmForTag();
			if (!empty($real_ecpm) && $tag->auto_update_ecpm == 'Yes'){
				$tag->real_ecpm = $real_ecpm;
				if ($network->supports_threshold == 'Yes'){
					$tag->threshold = $tag->real_ecpm;
				} else {
					$tag->estimated_cpm = $tag->real_ecpm;
				}
				$updates += $tag->save();
			}
		}
	}


	public function fetchPage($url, $method = "GET", $params = array()){
		static $ch;
		if (empty($ch)){ 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.7) Gecko/2009021906 Firefox/3.0.7");

			// cookie file 
			$cookieFile = tempnam(sys_get_temp_dir(), 'AdNetworkCookies');
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);

			// We don't care about ssl certs
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
				
		}

		if ($method == "POST"){
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_URL, $url);
		} else {
			curl_setopt($ch, CURLOPT_POSTFIELDS, '');
			curl_setopt($ch, CURLOPT_POST, false);
			if (!empty($params)){
				$url .=  '?' . http_build_query($params);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
		}
	
		$ret = curl_exec($ch);
		if (empty($ret)){
			return curl_error($ch);
		} else {
			return $ret;
		}
	}
		
}

?>
