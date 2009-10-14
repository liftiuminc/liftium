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
	"test_ad_iframe_hop",
	"test_cross_domain.php",
	"test_frequency_part1.php",
	"test_frequency_part2.php",
	"test_frequency_part3.php",
	"test_rejection_part1.php",
	"test_rejection_part2.php",
	"test_rejection_part3.php",
);

// Certain tests need to be run in a new window
$newWindows = array(
	"test_cross_domain.php",
	"test_geo_targeting.php",
	"test_iframe_clearing.php",
	"test_slow_config.php",
	"test_no_config.php",
	"test_stats_noiframe.php",
);

foreach ($generics as $generic ){
	$g = basename($generic);
	if (in_array($g, $excludes)){
		continue;
	}

	if (in_array($g, $newWindows) || preg_match("/test_ad_/", $g)){
		echo '<tr><td><a href="LiftTestNewWindow?file=' . basename($g) . '">' . basename($g) . '</a></td></tr>' . "\n";
	} else {
		echo '<tr><td><a href="LiftTest?file=' . basename($g) . '">' . basename($g) . '</a></td></tr>' . "\n";
	}
}
?>
</tbody></table>
</body>
</html>

