<?php $LiftiumOptions = array('pubid' => 1046); ?>
<?php require 'header.php'?>
This page is for testing frequency capping, part 1:<br />
Clear tag stats and do initial fill: first tag will fill.
<p>
<script>
// Clear the existing stats for idempotency
Liftium.tagStats = '';
</script>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 336px; height: 280px;">
	<script>Liftium.callAd("336x280")</script> 
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
output.match(/This is a fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!output.match(/This is an always_fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
