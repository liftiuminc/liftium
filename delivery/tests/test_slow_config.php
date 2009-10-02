<?php $LiftiumOptions = array('config_delay' => 1) ?>
<?php require 'header.php'?>
This page is for testing Liftium ad calls, pretending that the config services takes a while to make sure timing issues are handled.
<p>

<!-- sample page -->
<div class="pagesection" style="width:950px;">
        <div id="slot2" class="adunit" style="width:728px; height: 90px;">
	<script>Liftium.callAd("728x90")</script>
	</div>
<script>
if (document.getElementById("slot2").innerHTML.toString().match(/This is a fill/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
