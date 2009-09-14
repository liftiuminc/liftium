<?php

class AdNetworkRightMedia extends AdNetwork {

	public $network_id = 13;
	public $RMR;

	function needsIframeWrapper(){
		return false;
	}

        public function getAd($slotname, $size, $network_options = array()){

		$dim=Athena::getHeightWidthFromSize($size);

		$url = "http://ads.bluelithium.com/st?ad_type=iframe&ad_size=$size&section=415419";

                return $this->iframeHtml($url, $dim['width'] , $dim['height']);
        }

	public function  login(){
		return false; // Disabled for now

		if (empty($this->RMR)){
			$this->RMR = new RightMediaReporting();
		}
		return $this->RMR->login($this);
	}

        public function getReportedCpmForTag($AdTag){
		return $this->RMR->getReportedCpmForTag($this, $AdTag);
	}
}

// Several of the Ad Networks use Right Media. Separate the class so they can reuse
class RightMediaReporting {

	public $isLoggedIn = false;

        public function login($AdNetwork){
                if ($AdNetwork->isLoggedIn){
                        return true;
                }
                $AdNetwork->fetchPage("https://my.yieldmanager.com/");
                $login = $AdNetwork->fetchPage("https://my.yieldmanager.com/index.php", "POST",
                        array(
                                'username' => $AdNetwork->webui_username,
                                'password' => $AdNetwork->webui_password,
                        )
                );
                $AdNetwork->isLoggedIn = true;
		return $AdNetwork->isLoggedIn;
        }


        public function getReportedCpmForTag($AdNetwork, $AdTag){
		$this->getResults($AdNetwork, $AdTag);
	}

        public function getResults($AdNetwork, $AdTag){
		static $results;
		if (!empty($results)){
			return $results;
		}
		
		$est = new DateTimeZone('EST');
		$start = new DateTime("-4 hours", $est);
		$end = new DateTime("-1 hour", $est);
		$start_hour = $start->format('G');
		if ($start_hour > 20){
			$end_hour = $start_hour - 20;
		} else {
			$end_hour = $start_hour + 4;
		}

		$params = array (
		    'from_report_page' => '1',
		    'savesettings' => '0',
		    'total_max_rows' => '0',
		    'quick_date' => 'custom',
		    'interval' => 'none',
		    'timezone' => '1',
		    'start_date' => $start->format('m/d/Y'),
		    'start_hour' => $start_hour,
		    'end_date' => $end->format('m/d/Y'),
		    'end_hour' => $end_hour,
		    'metricsOption' => 'default',
		    'grouping_size_id' => 'on',
		    'filtering_size_id' => 'on',
		    'submit_report_x' => '38',
		    'submit_report_y' => '10',
		    'submit_report' => 'Run report',
		    'inc' => '11',
		    'tab_id' => '1',
		    'rand' => mt_rand(),
		    'report_ready' => '1',
		    'report_url' => '_B0cLZWMLvOi9PPSs3kghlIuvT3puHTeW6WvhdEyzAV91o5gz9Ppqk9Sr1ghMT5jsP8nuGdsx3wAzZaYEQla.hksXae0jSEdzZjYt5I-',
		    'tstamp' => $start->format('U')
		);
		$params = array(
		    'from_report_page' => '1',
		    'report_ready' => '0',
		    'savesettings' => '0',
		    'total_max_rows' => '0',
		    'submit_report_x' => '34',
		    'submit_report_y' => '16',
		    'submit_report' => 'Run report',
		    'quick_date' => 'custom',
		    'interval' => 'none',
		    'timezone' => '1',
		    'start_date' => $start->format('m/d/Y'),
		    'start_hour' => $start_hour,
		    'end_date' => $end->format('m/d/Y'),
		    'end_hour' => $end_hour,
		    'metricsOption' => 'default',
		    'grouping_size_id' => 'on',
		    'filtering_size_id' => 'on',
		    'inc' => '11',
		    'tab_id' => '1',
		    'rand' => mt_rand()
		  );
		print_r($params);
		echo $AdNetwork->fetchPage("https://my.yieldmanager.com/tab.php", "POST", $params);
		exit;
        }

}
