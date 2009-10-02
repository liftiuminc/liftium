<?php require 'header.php'?>
This page is for testing Liftium.getLanguage()
<p>
<hr>
Navigator object:
<pre>
<script>
for (var prop in window.navigator){
	if (typeof window.navigator[prop] == "string"){
		document.write(prop + "= '" + window.navigator[prop] + "'<br />");
	}
}
</script>
</pre>
<script>
document.write("language:" + Liftium.getBrowserLang() + "<br/>");
if (Liftium.getBrowserLang() == "en"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
