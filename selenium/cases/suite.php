<?php
/* This dynamically generates a list of test cases to run through */
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta content="text/html; charset=UTF-8" http-equiv="content-type" />
  <title>Liftium Test Suite</title>
</head>
<body>
<table id="suiteTable" cellpadding="1" cellspacing="1" border="1" class="selenium"><tbody>
<tr><td><b>Liftium Delivery Test Suite</b></td></tr>
<?php
$generics = glob($_SERVER['DOCUMENT_ROOT'] . '/tests/test_*');
$excludes = array(
	"test_ad_networks.php",
	"test_adunits.php",
	"test_tag.php",
	"test_google.php"
);

// Certain tests need to be run in a new window
$newWindows = array(
	"test_ad_bad_chain.php",
	"test_ad_calls.php",
	"test_ad_psa.php",
	"test_ad_single_chain.php",
	"test_ads_in_content.php",
	"test_adunits.php",
	"test_cross_domain.php",
	"test_hop.php",
	"test_hopping.php",
	"test_leaderboard.php",
	"test_top_right_boxad.php",
	"test_stats.php",
	"test_iframe_clearing.php",
	"test_stats_noiframe.php"
);
foreach ($generics as $generic ){
	$g = basename($generic);
	if (in_array($g, $excludes)){
		continue;
	}

	if (in_array($g, $newWindows)){
		echo '<tr><td><a href="LiftTestNewWindow?file=' . basename($g) . '">' . basename($g) . '</a></td></tr>' . "\n";
	} else {
		echo '<tr><td><a href="LiftTest?file=' . basename($g) . '">' . basename($g) . '</a></td></tr>' . "\n";
	}
}
?>
</tbody></table>
</body>
</html>

