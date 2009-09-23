<?php require 'header.php'?>
This page is for testing that varnish is caching properly
<p>
<?php 
if (empty($_SERVER['HTTP_X_VARNISH'])){
	echo "<b>Script not being called through varnish.</b>";
	echo "<script>LiftiumTest.testPassed();</script>\n";
	require 'footer.php';
	return;
}
?>
<p>
Cookie:
<xmp><?php print_r($_COOKIE)?></xmp>
<hr>
<br />
echo date: <?php echo date('r')?><br />
rand: <?php echo mt_rand()?>
<script>
var _COOKIE = <?php echo json_encode($_COOKIE)?>;
var _SERVER = <?php echo json_encode($_SERVER)?>;
if (Liftium.e(_COOKIE)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
