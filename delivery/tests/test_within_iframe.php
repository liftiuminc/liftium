<html>
<head>
<title>Liftium Test</title>
</head>
<body>
<script type="text/javascript" src="LiftiumTest.js"></script>
<style type="text/css">
.adunit {
	border: 1px dashed black;
        margin: 10px;
}       
</style>
<div id="test_results" style="display:none">Running tests...</div>

<a href="/tests/">View all tests</a>
<br />
This page is for testing Liftium as called from inside an iframe, and then hop is called from an iframe.
<p>
<iframe src="iframecall.html?liftium_debug=3" width=728 height=90></iframe>
<script>
var somethingGood = true;
if(somethingGood) {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<div id="page_loaded"></div>
<script>
(function(){
	var lastOnload = window.onload ; 
	window.onload = function () {
		document.getElementById('page_loaded').innerHTML = 'Page Loaded';
		if (lastOnload) {
			lastOnload();
		}
	}
})();
</script>
</body>
</html>
