<?php $LiftiumOptions = array('pubid' => 1046); ?>
<?php require 'header.php'?>
This page is for testing rejection time, part 2:<br />
First tag will be skipped because it was rejected in part 1. Second tag will always fill.
<p>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 300px; height: 250px;">
	<script>Liftium.callAd("300x250")</script> 
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
!output.match(/This is a hop/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
output.match(/This is an always fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
