<?php
if (empty($_GET['tag_id'])){
	echo "tag_id is required";
	exit;
}
require dirname(__FILE__) . "/../includes/Framework.php";

$LiftiumConfig = new LiftiumConfig();
$data = $LiftiumConfig->loadTagFromId($_GET['tag_id']);
?>
<html>
<head>
<style type="text/css">
body { 
	background: transparent;
	margin: 0px;
}
</style>
</head>
<script>
var LiftiumOptions = window.LiftiumOptions || top.LiftiumOptions;
var Liftium = window.Liftium || top.Liftium;
</script>
<body>
<?php
if ($data['network_name'] == 'DART'){
	echo "<script src='/js/Wikia.js?1'></script>\n";
	echo "<script>var ProviderValues = window.ProviderValues || top.ProviderValues;</script>\n";
}
echo "<!-- Begin Tag #{$_GET['tag_id']} -->\n";
echo $data['tag'];
?>
</body>
</html>
