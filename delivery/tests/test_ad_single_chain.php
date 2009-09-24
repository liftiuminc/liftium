<?php $pubid = 1046; ?>
<?php require 'header.php'?>
This page is for testing Liftium ad calls with a single always_fill tag in the chain.
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
if (document.getElementById("slot1").innerHTML.toString().match(/This is a fill/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
