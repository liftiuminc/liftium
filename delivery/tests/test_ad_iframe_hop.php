<?php $LiftiumOptions=array("pubid" => 1047); ?>
<?php require 'header.php'?>
This page is for testing hopping inside an iframe, one of the more difficult tasks that Liftium does
<p>

<!-- sample page -->
<div class="pagesection" style="width:950px;">
	<div id="slot1" class="adunit" style="width:336px; height: 280px;">
		<script>Liftium.callAd("336x280")</script> 
	</div>
</div>
<script>
function checkIframe() {
	var ad = document.getElementById("slot1").innerHTML.toString();
	if (ad.toString().match(/This is a fill/)){
		LiftiumTest.testPassed();
	} else {
		LiftiumTest.testFailed();
	}
}
window.setTimeout("checkIframe();", 500);
</script>

<?php require 'footer.php'?>
