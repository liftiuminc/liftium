<?php $LiftiumOptions = array("pubid" => 1043) ?>
<?php require 'header.php'?>
This page is for testing LiftiumOptions.adjustValue
<p>

<div id="slot1" class="adunit" style="width:125px; height: 125px;">
        <script>Liftium.callAd("125x125")</script>
</div>

<pre>
<div id="stuff" style="font-size: smaller;">
</div>

</pre>
<script>
// Test isn't much of a test. What's more important is an environment where I can watch tag stats and see the values fluctuate
Liftium._("slot1").innerHTML.match(/This is a/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>
<script>
LiftiumTest.afterBeacon = function (){
	// Adjusted values
      	Liftium._("stuff").innerHTML = "<br />Chain:<br />";
	for (var i = 0; i < Liftium.chain["Liftium_125x125"].length; i++){
		var t = Liftium.chain["Liftium_125x125"][i];
        	Liftium._("stuff").innerHTML += "<br />" + Liftium.print_r({
			tag_id: t["tag_id"], 
        		loaded: t["loaded"],
        		pay_type: t["pay_type"], 
        		value: t["value"], 
        		adjusted_value: t["adjusted_value"],
        		attempts: Liftium.getTagStat(t["tag_id"], "a")
		});
	}
};
</script>


<?php require 'footer.php'?>

