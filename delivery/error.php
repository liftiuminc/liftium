<?php
require dirname(__FILE__) . '/../includes/Framework.php';

echo "<pre>";
print_r($_GET);
echo "</pre>";

$browser = Framework::getBrowser();

ini_set('error_log', '/home/tempfiles/10days/jserrors.' . @$_GET['type'] . '.' . date('Y-m-d'));
error_log("Pubid: " .  @$_GET['pubid'] . "|" . $_GET['msg'] . '|' . @$_SERVER['HTTP_REFERER'] . '|' . $browser);

EventRecorder::record(array('JavascriptErrors', $browser), "minute");
EventRecorder::record(array('JavascriptErrors'), "minute");
if (@$_GET['type'] == 'tag') {
        EventRecorder::record(array('TagErrors'), "minute");
} else {
	EventRecorder::record(array('JavascriptErrors_' . $_GET['type']), "minute");
}
?>
