<?php require 'header.php'?>
This page is for testing Liftium.clone
<p>
<script>
// Simple string
var str = "Hello World!";
if ( Liftium.clone(str) === "Hello World!"){
        LiftiumTest.testPassed();
} else {
	alert("str test failed");
        LiftiumTest.testFailed();
}

// Simple integer
var num = 1
if ( Liftium.clone(num) === 1){
        LiftiumTest.testPassed();
} else {
	alert("num test failed");
        LiftiumTest.testFailed();
}

// Bool
var z = true;
if ( Liftium.clone(z) === true){
        LiftiumTest.testPassed();
} else {
	alert("bool test failed");
        LiftiumTest.testFailed();
}

// Empty 
var emptyObj = {}; 
var emptyArray = []; 
if ( typeof Liftium.clone(emptyObj) == "object" && Liftium.e(Liftium.clone(emptyObj)) && Liftium.e(Liftium.clone(emptyArray))){
        LiftiumTest.testPassed();
} else {
	alert("empty test failed");
        LiftiumTest.testFailed();
}

// Test to see if passes by reference by default
var oneTwoThree = [1, 2, 3];
var x = oneTwoThree;
x[0] = "x"; 
if ( oneTwoThree[0] === "x") {
        LiftiumTest.testPassed();
} else {
	alert("pass by reference test failed");
        LiftiumTest.testFailed();
}


// Make sure it makes a copy
var fourFiveSix = [4,5,6];
var y = Liftium.clone(fourFiveSix);
y[0] = "y"; 
if ( fourFiveSix[0] === 4) {
        LiftiumTest.testPassed();
} else {
	alert("clone array test failed");
        LiftiumTest.testFailed();
}

// And now a complex object
var tag = {"network_name":"Test","tag_id":"242","network_id":"11","tag":"<script src=\"blah\"><\/script>, my text <!-- tag 242 -->","guaranteed_fill":"No","slotnames":["TOP_LEADERBOARD","FOOTER_BOXAD","LEFT_LOWER_SKYSCRAPERS","INCONTENT_LEADERBOARDS"],"size":"728x90","criteria":{"wgDBname":["athenatest"]}};
var tagCopy = Liftium.clone(tag);
tagCopy["tag_id"] = "Changed tag id";
if ( tag["tag_id"] == "242" && tagCopy["tag_id"] == "Changed tag id" &&
	tag["slotnames"].length === tagCopy["slotnames"].length &&
	tag["tag"] === tagCopy["tag"]) {
        LiftiumTest.testPassed();
} else {
	alert("clone object test failed");
        LiftiumTest.testFailed();
}

</script>

<?php require 'footer.php'?>
