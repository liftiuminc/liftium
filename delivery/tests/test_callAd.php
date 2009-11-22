<?php $dontCallLiftiumJs = true ?>
<?php require 'header.php'?>
<?php
if (empty($_REQUEST['html'])){
	$html = '<script src="/callAd?pubid=1042&slot=728x90"></script>';
} else {
	$html = $_REQUEST['html'];
} 
?>
This page is for testing tags and how they work when they are run through document.write (as if being delivered through an ad server) 
<p>
<form>
<textarea id="html" name="html" rows="5" cols="50"><?php echo htmlspecialchars($html)?></textarea>
<br />
<input type="submit">
</form>

<div id="ad1" class="adunit" style="width:728px;height:90px;"><script>document.write(document.getElementById("html").value);</script></div>

<script>
if(document.getElementById("ad1").innerHTML.match(/This is a fill/)) {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

