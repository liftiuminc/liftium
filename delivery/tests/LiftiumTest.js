/* Unit test helper */

// Tell Liftium to throw a LiftiumTest.testFailed when it has a js error
window.failTestOnError = true;

var LiftiumTest = {
	testsRun : 0,
	testsPassed : 0,
	testsFailed : 0,
	errors : []
};

LiftiumTest.testPassed = function(){
	if (this.testsFailed === 0){
		document.body.style.backgroundColor = "green";
	}
	this.testsRun++;
	this.testsPassed++;
	this.updateTestStatus();
};

LiftiumTest.testFailed = function(){
	document.body.style.backgroundColor = "red";
	this.testsRun++;
	this.testsFailed++;
	this.updateTestStatus();
};

LiftiumTest.updateTestStatus = function(){
	var test_results = document.getElementById("test_results");
	if (test_results === null) {
		document.write('<div id="test_results"></div>');
	}
	test_results.innerHTML = this.testsRun + " tests ran, " +
		this.testsPassed + " tests passed, " +
		this.testsFailed + " tests failed";
	test_results.style.display = "block";
};

LiftiumTest.getTagStats = function(tag_id){
	var text = "Raw Tag Stats: " + window.Liftium.tagStats + "<br />";
	text += "Stats for tag_id #" + tag_id + ":<br />";
	text += "&nbsp; - Loads = " + window.Liftium.getTagStat(tag_id, "l") + "<br />";
	text += "&nbsp; - Rejects = " + window.Liftium.getTagStat(tag_id, "r") + "<br />";
	text += "&nbsp; - Attempts = " + window.Liftium.getTagStat(tag_id, "a") + "<br />";
	text += "&nbsp; - Last Rejection = " + window.Liftium.getMinutesSinceReject(tag_id) + " minutes ago<br />";
	return text;
};
