<?php require 'header.php'?>
This page is for testing Liftium ad calls, emulating a customer with 2 tags, with the same size.
<p>

<!-- sample page -->
<div class="pagesection" style="width:950px;">
  	<div id="slot1" class="adunit" style="width:728px; height: 90px;">
		<script>Liftium.callAd("728x90")</script> 
	</div>
  	<div id="slot2" class="adunit" style="width:728px; height: 90px;">
		<script>Liftium.callAd("728x90")</script> 
	</div>
</div>
<script>
if (document.getElementById("slot1").innerHTML.toString().match(/This is a fill/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
if (document.getElementById("slot2").innerHTML.toString().match(/This is a fill/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
