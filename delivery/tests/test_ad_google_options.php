<?php $LiftiumOptions=array(
	"pubid" => 1045,
	"google_ad_client" => "pub-4338851863733765",
	"google_color_bg" => "008000",
); ?>
<?php require 'header.php'?>
This page is for testing Liftium ad calls with  google_* options passed in the LiftiumOptions
<p>

<!-- sample page -->
<div class="pagesection" style="width:950px;">
	<div style="position: absolute; padding:5px; margin-left: 200px">
		<div id="slot1" class="adunit" style="width:728px; height: 90px;">
			<script>Liftium.callAd("728x90")</script> 
		</div>
	</div>
</div>
<script>
if (document.getElementById("slot1").innerHTML.toString().match(/google_ads/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
if (document.getElementById("slot1").innerHTML.toString().match(/008000/)){
	LiftiumTest.testPassed();
} else {
	alert("google_color_bg=" + google_color_bg);
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
