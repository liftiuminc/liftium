<?php

class AdNetworkGoogle extends AdNetwork {

	private $default_pub_id = "pub-4086838842346968";
	private $default_ad_type = "text_image";

        public function getTagOptions(){ return array('channel', 'pub_id', 'ad_slot', 'ad_type' ); }

        public function getAd($slotname, $size, $network_options = array()){
                $dim=Athena::getHeightWidthFromSize($size);

		if (empty($network_options['pub_id'])) {
			$network_options['pub_id'] = $this->default_pub_id;
		}
		if (empty($network_options['ad_type'])) {
			$network_options['ad_type'] = $this->default_ad_type;
		}

                $out = '<script type="text/javascript">
			if ( top != self ){
				// In an iframe
				var Athena = top.Athena;
				var AdEngine =top.AdEngine;
			}
                        google_ad_client    = "' . $network_options['pub_id'] . '";
                        google_ad_width     = "' . $dim['width'] . '";
			google_ad_height    = "' . $dim['height'] . '";
                        google_ad_format    = google_ad_width + "x" + google_ad_height + "_as";
                        google_ad_type      = "' . $network_options['ad_type'] . '"

			// If being loaded in a url via an iframe tag, check if its INCONTENT
			if (Athena.getRequestVal("slotname", "").match(/INCONTENT/)){
				g_slot = Athena.getRequestVal("slotname"); // not to be confused with g_spot
			} else {
				g_slot = "' . addslashes($slotname) . '";
			}
			var g_slot_channels = {
				"TOP_RIGHT_BOXAD": 9100000030,	
				"LEFT_SKYSCRAPER_1": 9100000031,	
				"LEFT_LEADERBOARD": 9100000032,	
				"INCONTENT_BOXAD_1": 9100000033,	
				"INCONTENT_BOXAD_2": 9100000034,	
				"INCONTENT_BOXAD_3": 9100000035,	
				"INCONTENT_BOXAD_4": 9100000036,	
				"INCONTENT_BOXAD_5": 9100000037,	
				"INCONTENT_LEADERBOARD_1": 9100000038,	
				"INCONTENT_LEADERBOARD_2": 9100000039,	
				"INCONTENT_LEADERBOARD_3": 9100000040,	
				"INCONTENT_LEADERBOARD_4": 9100000041,	
				"INCONTENT_LEADERBOARD_5": 9100000042,
				"PREFOOTER_LEFT_BOXAD": 9100000043,
				"PREFOOTER_RIGHT_BOXAD": 9100000044
			};
			if (!Athena.e(g_slot_channels[g_slot])){
				google_ad_channel = g_slot_channels[g_slot];
			} else {
				google_ad_channel = "";
			}
			
				
			if (typeof AdEngine != "undefined"){
			  google_color_border = AdEngine.getAdColor("bg");
                          google_color_bg     = AdEngine.getAdColor("bg");
                          google_color_link   = AdEngine.getAdColor("link");
                          google_color_text   = AdEngine.getAdColor("text");
                          google_color_url    = AdEngine.getAdColor("url");
			}' . "\n";

		if (!empty($network_options['channel'])){
			$out.= 'google_ad_channel      += ",' . addslashes($network_options['channel']) . '";' . "\n";
		}
		if (!empty($network_options['ad_slot'])){
			$out.= 'google_ad_slot      = "' . addslashes($network_options['ad_slot']) . '";' . "\n";
		}
		/* Channel is how we do bucket tests.
		 * Testing the effectiveness of google_page_url and google_keywords here
		 * The first test showed that hints performed better than the control and page_url
		 * Now testing control vs hints alone vs hints + page_url
		 */
                $out .= '
			if (typeof Athena != "undefined" && typeof Athena.getPageVar != "undefined"){
			  google_page_url = "http://" + Athena.getPageVar("hostname") + Athena.getPageVar("request");
			} else if (top != self && typeof document.referrer != "undefined"){
			  google_page_url = document.referrer;
			}';

		$out .= '</script>';
		$out .= $this->loadScript("http://pagead2.googlesyndication.com/pagead/show_ads.js");
                return $out;
        }



	// https://www.google.com/adsense/support/bin/answer.py?hl=en&answer=9727
	public function getSupportedLanguages(){
		return array('ar', 'bg', 'zh', 'hr', 'cs', 'da', 'nl', 'en', 'fi', 'fr', 'de', 'el', 'he',
			     'hu', 'it', 'ja', 'ko', 'no', 'pl', 'pt', 'ro', 'ru', 'sr', 'sk', 'es', 'sv', 'tr');
	}

}
