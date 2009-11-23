<?php $LiftiumOptions = array("pubid" => 1043)?>
<?php require 'header.php'?>
This page is for testing Sampling. We have that is sampled at 50%. Here are the results for calling <code>Liftium.getSampledAd("180x150");</code> repeatedly:
<p>
<div id="stats"></div>
<p>
<script>
try {
  var tries = 2500;
  var sampleSuccess = 0, sampleFalse = 0, sampleOther = 0;

  for (var i = 0; i < tries; i++){
	  var s = Liftium.getSampledAd("180x150");
	  if (s === false){
		  sampleFalse++;
	  } else if (s["tag_id"] == "92"){
		  sampleSuccess++;
	  } else if (!Athena.e(s["tag_id"])){
		  sampleOther++;
	  } else {
		  throw ("Unexpected return from Liftium.getSampledAd " + Liftium.print_r(s));
	  }
  };

  samplePercent = (sampleSuccess / tries) * 100;

  document.getElementById("stats").innerHTML = "<b>Results</b><br >" +
	  "Tries: " + tries + "<br />" + 
	  "Tag #92 Sampled: " + sampleSuccess + "<br />" + 
	  "No Ad Sampled: " + sampleFalse + "<br />" + 
	  "Other Ad Sampled: " + sampleOther + "<br />" + 
	  "% tag #92 sampled:: " + samplePercent + "<br />"; 
	  
  if (samplePercent > 47 && samplePercent < 53){
	  LiftiumTest.testPassed();
  } else {
	  LiftiumTest.testFailed();
  }
} catch (e){
  alert ("Error: " + Liftium.print_r(e));
  LiftiumTest.testFailed();
}

</script>

<div id="TOP_RIGHT_BOXAD_load" class="adunit" style="width: 180px; height: 150px">
        <script type="text/javascript">Liftium.callAd("180x150");</script>
</div>

<?php require 'footer.php'?>
