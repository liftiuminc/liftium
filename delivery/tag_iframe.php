<?php
/* This file is how we deliver a tag through an iframe */
if (empty($_GET['pubid']) || empty($_GET['size'])){
	echo "pubid and size is required";
	exit;
}
require dirname(__FILE__) . "/../includes/Framework.php";
?>
<html>
<head>
<style type="text/css">
body { 
	background: transparent;
	margin: 0px;
}
</style>
<script><?php echo "LiftiumOptions = " . json_encode(array('pubid'=> $_GET['pubid']))?></script>
<script src="/js/Liftium.js"></script>
</head>
<body>
<script>Liftium.callAd("<?php echo htmlspecialchars($_GET['size'])?>");</script>
</body>
</html>
