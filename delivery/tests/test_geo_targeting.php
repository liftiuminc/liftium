<?php $LiftiumOptions=array(
	"pubid" => 1046, 
	//"geoUrl" => "http://geoiplookup.wikia.com/"
	//"geoUrl" => "http://badurl"
); ?>
<?php require 'header.php'?>
This page is for testing geotargeting. There are two ads that run. If you are in the us,
it should figure that out, and return the us ad in the chain for 200x200. Otherwise,
it should deliver the row ad.
<p>
Note that you can change the country by passing <code>liftium_country=xx</code> in the url. 
<p>

<!-- sample page -->
<div class="pagesection" style="width:950px;">
	<div id="slot1" class="adunit" style="width:200px; height: 200px;">
		<script>Liftium.callAd("200x200")</script> 
	</div>
</div>
<script>
var ad = document.getElementById("slot1").innerHTML.toString()
if (ad.toString().match(/This is a fill/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

if (Liftium.getCountry() == "us" && ad.match(/US only/)){
	LiftiumTest.testPassed();
} else if (Liftium.getCountry() != "us" && ad.match(/anywhere/)){
	LiftiumTest.testPassed();
} else {
	//alert("Liftium.getCountry()=" + Liftium.getCountry() + ", but wrong add shown");
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
