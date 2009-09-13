<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
if (empty($wgDBname)){
        $wgDBname = "athenatest";
}
?>
<html>
<head>
<title>Liftiumium Test</title>
<?php
if (!empty($refresh)){
        echo "<meta http-equiv='refresh' content='$refresh;url={$_SERVER['REQUEST_URI']}'/>\n";
}
?>
</head>
<?php
if (empty($_GET['liftium_debug'])){
?>
  <body>
<?php } else { ?>
  <!-- Pull in YUI for the logging console -->
  <body class="yui-skin-sam">
  <script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/utilities/utilities.js"></script>
  <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.6.0/build/logger/assets/skins/sam/logger.css"/>
  <script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/logger/logger-min.js"></script>
<?php } ?>
<script type="text/javascript" src="../Liftium.js?<?php echo mt_rand()?>"></script>
<script type="text/javascript" src="LiftiumTest.js"></script>
<style type="text/css">
.adunit {
	border: 1px dashed black;
        margin: 10px;
}       
</style>
<div id="test_results" style="display:none">Running tests...</div>
