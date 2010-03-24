<?php $LiftiumOptions = array("pubid"=>1048);?>
<?php require 'header.php'?>
This page is for testing key value targeting
<p>

This ad should be tag #150, because it is hub=gaming.
<div id="slotx" class="adunit" style="width:300px; height: 250px;">
	<script>LiftiumOptions.kv_hub = 'gaming'</script>
        <script>Liftium.callAd("300x250")</script>
</div>
<p>
This ad should be tag #151, because it is hub=entertainment
<div id="slotz" class="adunit" style="width:300px; height: 250px;">
	<script>LiftiumOptions.placement = 'entertainment'</script>
        <script>Liftium.callAd("300x250")</script>
</div>
<script>
Liftium._("slotx").innerHTML.match(/Targetted to hub=gaming/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("slotz").innerHTML.match(/NOT targetted/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

