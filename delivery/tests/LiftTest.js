/* Unit test helper */

var LiftTest = {
        testsRun : 0,
        testsPassed : 0,
        testsFailed : 0
};

LiftTest.testPassed = function(){
        if (this.testsFailed === 0){
                document.body.bgColor = "green";
        }
        this.testsRun++;
        this.testsPassed++;
        this.updateTestStatus();
};

LiftTest.testFailed = function(){
        document.body.bgColor = "red";
        this.testsRun++;
        this.testsFailed++;
        this.updateTestStatus();
};

LiftTest.updateTestStatus = function(){
        window.Lift._("test_results").innerHTML = this.testsRun + " tests ran, " +
		this.testsPassed + " tests passed, " +
		this.testsFailed + " tests failed";
        window.Lift._("test_results").style.display = "block";
};


window.onerror = function (e){
	alert("Error running test: " + window.Lift.print_r(e));
	LiftTest.testFailed();
};
