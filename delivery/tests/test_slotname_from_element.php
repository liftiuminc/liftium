<?php require 'header.php'?>
This page is for testing Liftium.getSlotnameFromElement
<p>

<!-- Simulate the chain being built -->
<script>Liftium.slotnames = ["Liftium_728x90", "Liftium_728x90_2"];</script>
<!-- One test inside the div -->
<div id="Liftium_728x90">
<span id="element1">Hi</span>
<script>
if (Liftium.getSlotnameFromElement(Liftium._("element1")) == "Liftium_728x90"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>
</div>

<!-- Another test called after the div -->
<div id="Liftium_728x90_2">
	<iframe id="element2" height="0" width="0"></iframe>
</div>

<script>
if (Liftium.getSlotnameFromElement(Liftium._("element2")) == "Liftium_728x90_2"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<!-- Negative tests -->
<script>
if (Liftium.getSlotnameFromElement("string") === false){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<div id="randomdiv"></div>
<script>
if (Liftium.getSlotnameFromElement(Liftium._("randomdiv")) === false){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>
<?php require 'footer.php'?>

