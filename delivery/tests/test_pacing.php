<?php $LiftiumOptions = array("pubid" => 1043)?>
<?php require 'header.php'?>
This page is for testing Pacing. We have that is Paced at 30%. Here are the results for calling <code>Liftium.isValidPacing(tag);</code> repeatedly:
<p>
<div id="stats"></div>
<p>
<script>
try {
  var tries = 2500;
  var sampleSuccess = 0, sampleFalse = 0;

  for (var i = 0; i < tries; i++){
	  if (Liftium.isValidPacing(Liftium.config["300x250"][0])){
		  sampleSuccess++;
	  } else {
		  sampleFalse++;
	  }
  };

  var samplePercent = (sampleSuccess / tries) * 100;

  document.getElementById("stats").innerHTML = "<b>Results</b><br >" +
	  "Tries: " + tries + "<br />" + 
	  "Tag #160 selected: " + sampleSuccess + "<br />" + 
	  "Not selected: " + sampleFalse + "<br />" + 
	  "% tag #160 sampled:: " + samplePercent + "<br />"; 
	  
  if (samplePercent > 27 && samplePercent < 33){
	  LiftiumTest.testPassed();
  } else {
	  LiftiumTest.testFailed();
  }
} catch (e){
  alert ("Error: " + Liftium.print_r(e));
  LiftiumTest.testFailed();
}

</script>

<div id="TOP_RIGHT_BOXAD_load" class="adunit" style="width: 300px; height: 250px">
        <script type="text/javascript">Liftium.callAd("300x250");</script>
</div>

<?php require 'footer.php'?>
