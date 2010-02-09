<?php $LiftiumOptions = array("pubid"=>1048);?>
<?php require 'header.php'?>
This page is for testing placement targeting
<p>

This ad should be tag #130, because it is placement 'placement1'.
<div id="slotx" class="adunit" style="width:234px; height: 60px;">
	<script>LiftiumOptions.placement = 'placement1'</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<p>
This ad should be tag #131, because it's placement is blank
<div id="sloty" class="adunit" style="width:234px; height: 60px;">
	<!-- Intentionally don't clear the placement to make sure that the code is doing it after the call above -->
        <script>Liftium.callAd("234x60")</script>
</div>
<p>
This ad should be tag #131, because it is placement is 'placement2'
<div id="slotz" class="adunit" style="width:234px; height: 60px;">
	<script>LiftiumOptions.placement = 'placement2'</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<script>
Liftium._("slotx").innerHTML.match(/Targetted to placement1/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("sloty").innerHTML.match(/NOT targetted to a/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("slotz").innerHTML.match(/NOT targetted to a/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

