<?php $pubid = 1046; ?>
<?php require 'header.php'?>
This page is for testing frequency capping, part 2:<br />
First tag will be skipped because it was alread filled in part 1 and the second tag will always fill.
<p>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 336px; height: 280px;">
	<script>Liftium.callAd("336x280")</script> 
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
!output.match(/This is a fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
output.match(/This is an always_fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
