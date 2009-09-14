/* Unit test helper */

var LiftiumTest = {
        testsRun : 0,
        testsPassed : 0,
        testsFailed : 0,
	errors : []
};

LiftiumTest.testPassed = function(){
        if (this.testsFailed === 0){
                document.body.bgColor = "green";
        }
        this.testsRun++;
        this.testsPassed++;
        this.updateTestStatus();
};

LiftiumTest.testFailed = function(){
        document.body.bgColor = "red";
        this.testsRun++;
        this.testsFailed++;
        this.updateTestStatus();
};

LiftiumTest.updateTestStatus = function(){
        window.Liftium._("test_results").innerHTML = this.testsRun + " tests ran, " +
		this.testsPassed + " tests passed, " +
		this.testsFailed + " tests failed";
        window.Liftium._("test_results").style.display = "block";
};


window.onerror = function (e){
	try {
		alert("Error running test: " + window.Liftium.print_r(e));
		LiftiumTest.errors.push(e);
		LiftiumTest.testFailed();
	} catch (e){
		// Avoid infinite recursion
		alert("Yikes. Error function is producing an error");
	}
};
