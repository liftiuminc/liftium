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
<body>
<?php
echo "<!-- Begin Tag #{$_GET['tag_id']} -->\n";
echo $data['tag'];
?>
</body>
</html>
