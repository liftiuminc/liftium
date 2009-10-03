<?php $LiftiumOptions = array('pubid' => 1046); ?>
<?php require 'header.php'?>
This page is for testing rejection time, part 1:<br />
Clear tag stats and do initial fill: first tag will hop, second will always fill.
<p>
<script>
// Clear the existing stats for idempotency
Liftium.tagStats = '';
</script>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 300px; height: 250px;">
	<script>Liftium.callAd("300x250")</script> 
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
output.match(/This is a hop/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
output.match(/This is an always fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
