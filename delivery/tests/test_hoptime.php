<?php $LiftiumOptions = array('pubid' => 1048); ?>
<?php require 'header.php'?>
This page is for testing hoptime:<br />
<p>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 336px; height: 280px;">
	<script>Liftium.callAd("336x280")</script>
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
output.match(/This is a slow hop/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!output.match(/This is a normal hop/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
output.match(/This is an always_fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
