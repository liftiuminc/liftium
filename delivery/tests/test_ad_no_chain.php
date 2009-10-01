<?php $LiftiumOptions = array ("pubid" => 1); ?>
<?php require 'header.php'?>
This page is for testing Liftium ad calls with no chain, ie. an invalid publisherid, or a publisher who hasn't been set up. 
<p>

<div id="slot1" class="adunit" style="width:728px; height: 90px;">
	<script>Liftium.callAd("728x90")</script> 
</div>
<script>
if (document.getElementById("slot1").innerHTML.toString().match(/Public Service Announcement/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
