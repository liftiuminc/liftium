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
