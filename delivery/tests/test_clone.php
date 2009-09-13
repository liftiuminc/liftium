<?php require 'header.php'?>
This page is for testing Lift.clone
<p>
<script>
// Simple string
var str = "Hello World!";
if ( Lift.clone(str) === "Hello World!"){
        LiftTest.testPassed();
} else {
	alert("str test failed");
        LiftTest.testFailed();
}

// Simple integer
var num = 1
if ( Lift.clone(num) === 1){
        LiftTest.testPassed();
} else {
	alert("num test failed");
        LiftTest.testFailed();
}

// Bool
var z = true;
if ( Lift.clone(z) === true){
        LiftTest.testPassed();
} else {
	alert("bool test failed");
        LiftTest.testFailed();
}

// Empty 
var emptyObj = {}; 
var emptyArray = []; 
if ( typeof Lift.clone(emptyObj) == "object" && Lift.e(Lift.clone(emptyObj)) && Lift.e(Lift.clone(emptyArray))){
        LiftTest.testPassed();
} else {
	alert("empty test failed");
        LiftTest.testFailed();
}

// Test to see if passes by reference by default
var oneTwoThree = [1, 2, 3];
var x = oneTwoThree;
x[0] = "x"; 
if ( oneTwoThree[0] === "x") {
        LiftTest.testPassed();
} else {
	alert("pass by reference test failed");
        LiftTest.testFailed();
}


// Make sure it makes a copy
var fourFiveSix = [4,5,6];
var y = Lift.clone(fourFiveSix);
y[0] = "y"; 
if ( fourFiveSix[0] === 4) {
        LiftTest.testPassed();
} else {
	alert("clone array test failed");
        LiftTest.testFailed();
}

// And now a complex object
var tag = {"network_name":"Test","tag_id":"242","network_id":"11","tag":"<script src=\"blah\"><\/script>, my text <!-- tag 242 -->","guaranteed_fill":"No","slotnames":["TOP_LEADERBOARD","FOOTER_BOXAD","LEFT_LOWER_SKYSCRAPERS","INCONTENT_LEADERBOARDS"],"size":"728x90","criteria":{"wgDBname":["athenatest"]}};
var tagCopy = Lift.clone(tag);
tagCopy["tag_id"] = "Changed tag id";
if ( tag["tag_id"] == "242" && tagCopy["tag_id"] == "Changed tag id" &&
	tag["slotnames"].length === tagCopy["slotnames"].length &&
	tag["tag"] === tagCopy["tag"]) {
        LiftTest.testPassed();
} else {
	alert("clone object test failed");
        LiftTest.testFailed();
}

</script>

<?php require 'footer.php'?>
