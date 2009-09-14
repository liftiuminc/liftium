<?php

class AdNetworkContextWeb extends AdNetwork {

	private $cwpid = 504082; // Context Web Account id

	private $slippage = .028; // % of the ad calls we expect to fail without knowing what happened to them

	private $nextHopValue = .50;

	public $chainSize = 3; 

        public function getAd($slotname, $size, $network_options=array()){

                $dim = AdTag::getHeightWidthFromSize($size);
		$slotgroup = Athena::getSlotGroup($size);
		$chain = $this->getCWChain($slotgroup);

		$out = "<script type=\"text/javascript\">" . 
	
			"AQ.chain.value = \"" .addslashes($chain['value']) . "\";\n" . 
			"AQ.chain.$slotgroup = " . json_encode($chain['tags']) . ";\n" . 
			"AQ.allTags.$slotgroup = " . json_encode($this->getTagsForSlot($slotgroup)) . ";\n" . 
			"</script>";
		
                $out .= "<script type=\"text/javascript\">
				if (Math.random() < 0.1){
					//Sampling to get stats.
					AQ.randomTag('$slotgroup', self);
				} else if (AQ.chain.value < AQ.priceFloor){
					top.Athena.d('AQ Chain value ' + AQ.chain.value + ' is less than ' + AQ.priceFloor + ', skipping ContextWeb', 3);
					top.Athena.hop();
				} else {
					AQ.tag('$slotgroup', '{$chain['tags'][0]['id']}', self);
				}
			</script>";

		return $out;
        }


	function getValue($cwtag){
		$config = $this->configArray();
		foreach ($config as $tag){
			if ($tag[0] == $cwtag){
				return $tag[1];
			}
		}
		return false;
	}


	public function getCWChain($slotgroup){
		$tags = $this->getTagsForSlot($slotgroup);
		$tagsLength = sizeof($tags);

		$chain = array();
		for ($i = 0; $i < $tagsLength; $i++){
			for ($j = 0; $j < $tagsLength; $j++){
				if ($i == $j){
					continue;
				}
				for ($k = 0; $k < $tagsLength; $k++){
					if ($i == $k || $j == $k){
						continue;
					}
					$value = round($this->calculateChainValue($tags[$i], $tags[$j], $tags[$k]) - $this->nextHopValue, 2);
					$chain[] = array('tags'=> array($tags[$i], $tags[$j], $tags[$k]), 'value'=> max($value, 0));
				}
			}
		}

		usort($chain, 'AdNetworkContextWeb::valueSort');
		return $chain[0];
	}


	public function calculateChainValue($tag1, $tag2, $tag3){
		$slip = $this->slippage;

		$tag1Value = $tag1['fValue'] * $tag1['cFill'];
		$tag2Value = max((1 - $tag1['cFill'] - $slip),0) * $tag2['fValue'] * $tag2['cFill'];
		$tag3Value = max((1 - $tag1['cFill'] - $slip),0) * max((1 - $tag2['cFill'] - $slip),0) * $tag3['fValue'] * $tag3['cFill'];
		$hopValue  = max((1 - $tag1['cFill'] - $slip),0) * max((1 - $tag2['cFill'] - $slip),0) * max((1 - $tag3['cFill'] - $slip),0) * $this->nextHopValue;

		return $tag1Value + $tag2Value + $tag3Value + $this->nextHopValue;	
	}

	public function getTagsForSlot($slotgroup){
		$c = $this->configArray();	

		$out = array();
		foreach($c as $cwtag){
			if ($cwtag[2] == $slotgroup){
				$cFill = $this->getCurrentFillRate($cwtag[0]);

				if ($cFill === null){
					// Assume a default fill rate if we don't have stats yet
					$cFill = .13333;
				}

				$out[] = array(
					'id'=> $cwtag[0],
					'cFill'=> $cFill,
					'fValue' => $cwtag[1]
				);
			}
		}
		return $out;
	}

	public function configArray() {
		return array(
			// LB
		      array(56051,0.75,"LB"),
		      array(56052,1.00,"LB"),
		      array(56053,1.50,"LB"),

			// WS
		      array(56054,0.75,"WS"),
		      array(56055,1.00,"WS"),
		      array(56056,1.50,"WS"),
		      array(47474,2.74,"WS"),
	
			// MR
			/* My range
		      array(48892,0.74,"MR"),
		      array(47307,0.88,"MR"),
		      array(19798,0.99,"MR"),
		      array(56266,1.11,"MR"),
		      array(47311,1.22,"MR"),
		      array(56269,1.33,"MR"),
		 //     array(47313,1.44,"MR"),
		      array(56271,1.55,"MR"),
		      array(19801,1.72,"MR"),
		      array(47317,1.94,"MR"),
		  //    array(56274,2.22,"MR"),
		      array(47318,2.44,"MR"),
		  //    array(19802,2.95,"MR"),
		  //    array(47319,3.20,"MR"),
		  //    array(42797,4.00,"MR")
		  /*/
// Michael's range
			array(47305,0.74,"MR"),
			array(47306,0.74,"MR"),
			array(47307,0.88,"MR"),
			array(47308,0.88,"MR"),
			array(47309,0.97,"MR"),
			array(47310,0.97,"MR"),
			array(47311,1.22,"MR"),
			array(47312,1.22,"MR"),
			array(47313,1.44,"MR"),
			array(47314,1.44,"MR"),
			array(47315,1.74,"MR"),
			array(47316,1.74,"MR"),
	//		array(47317,1.94,"MR"),
	//		array(47318,2.44,"MR"),
	//	      array(19802,2.95,"MR"),
	//	      array(47319,3.20,"MR"),
		 //     array(42797,4.00,"MR")
/* Tests
                      array(56048,0.75,"MR"),
                      array(56049,1.00,"MR"),
                      array(56050,1.50,"MR"),
*/

		);
	}


	private function getCurrentFillRate($cwtagid){
		// In the first few seconds of the minute, use the last minute. After that, use the current minute
		if (intval(date('s')) < 30){
			$begin = "-1 minute";
		} else {
			$begin = "-1 second";
		}

		$attempts = $this->fetchFillStat($cwtagid, "attempt", $begin);
                if ($attempts === 0){

			if ($begin == "-1 second"){
				// Go back further
				$begin = "-1 minute";	
				$attempts = $this->fetchFillStat($cwtagid, "attempt", $begin);
			}
			if ($attempts === 0){
				return null;
			}
                }

		$loads = $this->fetchFillStat($cwtagid, "load", $begin);

		return round($loads/$attempts, 3);
	}

        private function fetchFillStat($cwtagid, $action, $begin, $end = "now"){
                $params = array(
                        'event'=>'contextWebBeacon',
                        'cwtagid'=> $cwtagid,
                        'action'=> $action,
                        'begin'=> $begin,
                        'end' => $end
                );
                $url = "http://" . $_SERVER['HTTP_HOST'] . "/athena/event/get?" . http_build_query($params);
                if (!empty($_GET['debug'])){ echo "Fetching $url:<br />\n"; } 

                $html = file_get_contents($url);
                if (!empty($_GET['debug'])){ echo "$html:<br />\n"; } 

                return intval($html);
        }


	private function getCurrentValue($cwtagid, $fullValue, $fillRate){
		return $fillRate * $fullValue * 1-$this->slippage; 
	}


	static public function valueSort($a, $b){
		return $a['value'] < $b['value'];
	}
}
