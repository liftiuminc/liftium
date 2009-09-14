<?php
/* Provider class for Google Ad Manager.
 * Documentation:
 * https://www.google.com/admanager/help/en_US/tips/tagging.html
 *
 * Debug: Try adding ?google_debug to the url.
 *
 */

class AdNetworkGAM extends AdNetwork {

	//private $adManagerId = "ca-pub-3862144315477646"; gorillamania@gmail.com account
	private $pub_id = "ca-pub-4086838842346968"; // Wikia account

	public $batchHtmlCalled = false;

	private $sites = array(	'Auto' => 'auto',
				'Creative' => 'crea',
				'Education' => 'edu',
				'Entertainment' => 'ent',
				'Finance' => 'fin',
				'Gaming' => 'gaming',
				'Green' => 'green',
				'Humor' => 'humor',
				'Lifestyle' => 'life',
				'Music' => 'music',
				'Philosophy' => 'phil',
				'Politics' => 'poli',
				'Science' => 'sci',
				'Sports' => 'sports',
				'Technology' => 'tech',
				'Test Site' => 'test',
				'Toys' => 'toys',
				'Travel' => 'travel');

        public function getTagOptions(){ return array('channel', 'pub_id'); }

	public function batchCallAllowed(){
		return true;
	}

        public function getSetupHtml(){
		static $called = false;
		if ($called){
			return false;
		}
		$called = true;

		if (!empty($network_options['pub_id'])){
			$this->pub_id = $network_options['pub_id'];
		}

		// Download the necessary required javascript
		$out = $this->loadScript("http://partner.googleadservices.com/gampad/google_service.js") . "\n" .
			'<script type="text/javascript">' . "\n" . 
			'GS_googleAddAdSenseService("' . addslashes($this->pub_id) . '");' . "\n" . 
			'GS_googleEnableAllServices();' . "\n" .
			'</script>' . "\n" .
			// Note that Google specifically recommends a second script tag here
			'<script type="text/javascript">' . "\n" . 
			// Always pass the hub as a key value
			'/*GA_googleAddAttr("hub", top.Athena.getPageVar("hub"));' . "\n" .
			'GA_googleAddAttr("skin", top.Athena.getPageVar("skin"));' . "\n" .
			// And languages
			'GA_googleAddAttr("cont_lang", top.Athena.getPageVar("cont_lang"));' . "\n" .
			'GA_googleAddAttr("user_lang", top.Athena.getPageVar("user_lang"));' . "\n";

		// ###### Ad Sense attributes
               	if ( $_GET['skin'] == 'monaco' ){
			// This is only available in monaco
			$out .= 'GA_googleAddAdSensePageAttr("google_color_border", top.AdEngine.getAdColor("bg"));' . "\n";
			$out .= 'GA_googleAddAdSensePageAttr("google_color_bg", top.AdEngine.getAdColor("bg"));' . "\n";
			$out .= 'GA_googleAddAdSensePageAttr("google_color_link", top.AdEngine.getAdColor("link"));' . "\n";
			$out .= 'GA_googleAddAdSensePageAttr("google_color_text", top.AdEngine.getAdColor("text"));' . "\n";
			$out .= 'GA_googleAddAdSensePageAttr("google_color_url", top.AdEngine.getAdColor("url"));' . "\n";
		}

		if (!empty($network_options['channel'])){
			$out .= 'GA_googleAddAdSensePageAttr("google_ad_channel", "' . addslashes($network_options['channel']) . '");' . "\n";
		}
		$out .= 'GA_googleAddAdSensePageAttr("google_page_url", "http://" + top.Athena.getPageVar("hostname") + top.Athena.getPageVar("request"));' . "\n";
		$out .= 'GA_googleAddAdSensePageAttr("google_language", top.Athena.getPageVar("cont_lang"));*/' . "\n";

		$out .= '</script>' . "\n";
		
		return $out;	
	}



	public function getAd($slotname, $size, $network_options=array()){

		// First time the ad is called, call all the batch code, if it hasn't already been called.
		$out = self::getSetupHtml();

		$out .= '<script type="text/javascript">' . "\n" .
			'GA_googleAddSlot("' . $this->pub_id . '","' . $slotname . '");' . "\n" .
			'GA_googleFetchAds();</script>' . "\n";

		return $out . '<script type="text/javascript">GA_googleFillSlot("' . $slotname . '")</script>';
	}


}
