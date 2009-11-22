<?php
ini_set('display_errors', true);
ini_set('error_prepend_string', '/*');
ini_set('error_append_string', '*/');
ini_set('html_errors', false);
header('Content-Type: application/x-javascript');

if (empty($_GET['pubid']) || empty($_GET['slot'])){
	echo "Missing pubid or slot(size)\n";
	exit;
}

require dirname(__FILE__) . '/../includes/Framework.php';
/* Browsers get all flaky when code is executed through document.write
 * Specifically, they don't block the execution of tags properly in the correct order.
 * Use PHP to pull these together into one combined call
 */
echo "LiftiumOptions = " . json_encode(array("pubid" => intval($_GET['pubid']), "autoInit" => false )) . ";\n";
echo "\n/* Begin Liftium.js */\n";
echo file_get_contents("js/Liftium.js");

echo "\n/* Begin geoip.liftium.com */\n";
echo file_get_contents("http://geoip.liftium.com/?" . Framework::getIP());

if (Framework::isDev()){
	$u = "http://" . $_SERVER['HTTP_HOST'];
} else { 
	$u = "http://delivery.liftium.com";
}
$u .= "/config?" . http_build_query(array('pubid'=> intval($_GET['pubid']), 'v' => 1.2));

echo "\n/* Begin $u */\n";
echo file_get_contents($u);

echo "\nLiftium.init();";

echo "\n" . 'Liftium.callAd("' . addslashes($_GET['slot']) . '");' . "\n";
?>
