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
function checkIframe(again) {
	var iframes = document.getElementsByTagName("iframe");
	for (var i = 0; i < iframes.length; i++ ){
		if (iframes[i].src.match(/\/tag\/\?tag_id=51/)){
			return LiftiumTest.testPassed();
		}
	}
	// The window may not have loaded (Safari), try 1 more time.
	if (!again){
		window.setTimeout("checkIframe(true);", 250);
	}  else {
		return LiftiumTest.testFailed();
	}
}
window.onload=checkIframe;
</script>

<?php require 'footer.php'?>
