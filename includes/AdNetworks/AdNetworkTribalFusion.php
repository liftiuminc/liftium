<?php

class AdNetworkTribalFusion extends AdNetwork {

	public $network_id = 15;

        public function getAd($slotname, $size, $network_options = array()){

		if ($size == "728x90"){ $size = "728x90,468x60"; }

		$out = "<script type=\"text/javascript\">
			e9 = new Object();    e9.size = \"$size\";   e9.noAd = 1;
			var hub = 'ROS';
			if (typeof Athena == 'undefined' && typeof top.Athena != 'undefined') {
			  var Athena = top.Athena;
			  hub = Athena.getPageVar('hub');
			  if (hub != 'Gaming' && hub != 'Entertainment') {
			    hub = 'ROS';
		  	  }
			}
			document.write('<script type=\"text/javascript\" src=\"http://tags.expo9.exponential.com/tags/WikiaInc/'+hub+'/tags.js\"><\\/script>');
			</script>";
 
                return $out;
        }

	public function login(){
		return false; // not working 

		$this->fetchPage($this->webui_login_url);
		$login = $this->fetchPage("https://www.tribalfusion.com/main/newSiteTryLogin", "POST",
			array(
				'errorUrl' => '/SmartAdvertisers/loginFail.html',
				'loginName' => $this->webui_username,
				'password' => $this->webui_password,
			)
		);

		$tod = new DateTime();
		$d->setTimezone("PDT");
		echo $d->format(MYSQL_DATE_FORMAT);

		$url = "https://www.tribalfusion.com/adapp/tf/converttocsv/dashboard_report_" . $d->format('%Y-%m-%d') . "_to_" . $d->format('%Y-%m-%d') . '_' . $d->format('%U') . "123.xls";
		echo $url;

		parse_str("reportdata=%09Name%09%09Views%09Paid%20Views%09eCPM%09%25%20Fill%09Gross%20Revenue%09Net%20Revenue%0DWikia%20Inc%2E%09%09%091%2C178%2C407%09151%2C154%09%241%2E99%0912%2E83%25%09%24300%2E76%09%24165%2E42%0D%09ROS%09%091%2C177%2C629%09150%2C947%09%241%2E99%0912%2E82%25%09%24299%2E91%09%24164%2E95%0D%09%09Leaderboard%09338%2C296%0923%2C697%09%242%2E38%097%2E00%25%09%2456%2E29%09%2430%2E96%0D%09%09Skyscraper%09477%2C810%0959%2C806%09%242%2E06%0912%2E52%25%09%24123%2E33%09%2467%2E83%0D%09%09Rectangle%09313%2C409%0919%2C092%09%243%2E10%096%2E09%25%09%2459%2E14%09%2432%2E53%0D%09%09Banner%0948%2C502%0948%2C502%09%241%2E27%09100%2E00%25%09%2461%2E44%09%2433%2E79%0D%09%09Other%09661%090%09%240%2E00%0900%2E00%25%09%240%2E00%09%240%2E00%0D%09Entertainment%09%0966%0914%09%242%2E62%0921%2E21%25%09%2400%2E04%09%2400%2E02%0D%09%09Rectangle%0917%093%09%242%2E82%0917%2E65%25%09%2400%2E01%09%2400%2E00%0D%09%09Skyscraper%0927%093%09%242%2E83%0911%2E11%25%09%2400%2E01%09%2400%2E00%0D%09%09Banner%094%094%09%241%2E22%09100%2E00%25%09%2400%2E00%09%2400%2E00%0D%09%09Leaderboard%0918%094%09%243%2E70%0922%2E22%25%09%2400%2E01%09%2400%2E01%0D%09Gaming%09%09712%09193%09%244%2E22%0927%2E11%25%09%2400%2E81%09%2400%2E45%0D%09%09Skyscraper%09277%0975%09%244%2E87%0927%2E08%25%09%2400%2E37%09%2400%2E20%0D%09%09Leaderboard%09161%0945%09%242%2E93%0927%2E95%25%09%2400%2E13%09%2400%2E07%0D%09%09Rectangle%09252%0951%09%245%2E70%0920%2E24%25%09%2400%2E29%09%2400%2E16%0D%09%09Banner%0922%0922%09%241%2E22%09100%2E00%25%09%2400%2E03%09%2400%2E01", $p);

		$report = $this->fetchPage($url,  "POST", $p);
		echo $report;
		exit;
	}


}
