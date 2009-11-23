<?php $LiftiumOptions = array("offline" => true) ?>
<?php require 'header.php'?>
This page is for testing Liftium.parseQueryString and Liftium.buildQueryString.
<p>
<script>
var nvpairs;

// Plain
nvpairs = Liftium.parseQueryString("var1=val1&var2=%22val%20encoded%22");

if (nvpairs.var1 == "val1" && nvpairs.var2 == '"val encoded"') {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

if (Liftium.buildQueryString(nvpairs) == "var1=val1&var2=%22val%20encoded%22"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

// With prepended ?
nvpairs = Liftium.parseQueryString("?var1=val1&var2=%22val%20encoded%22");
if (nvpairs.var1 == "val1" && nvpairs.var2 == '"val encoded"') {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

// With ; instead of &
nvpairs = Liftium.parseQueryString("var1=val1;var2=%22val%20encoded%22");
if (nvpairs.var1 == "val1" && nvpairs.var2 == '"val encoded"') {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
if (Liftium.buildQueryString(nvpairs, ";") == "var1=val1;var2=%22val%20encoded%22"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}


// With empty value
nvpairs = Liftium.parseQueryString("var1;var2=%22val%20encoded%22");
if (nvpairs.var1 == true && nvpairs.var2 == '"val encoded"') {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

// Extra delimiters
nvpairs = Liftium.parseQueryString("var1=val1;;var2=%22val%20encoded%22;");
// Check to make sure only 2
var len = 0;
for (var name in nvpairs) { len++; }
if (nvpairs.var1 == "val1" && nvpairs.var2 == '"val encoded"' && len == 2) {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

// encoded var names 
nvpairs = Liftium.parseQueryString("%24var1=val1;;var2=%22val%20encoded%22;");
if (nvpairs.$var1 == "val1" && nvpairs.var2 == '"val encoded"') {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
if (Liftium.buildQueryString(nvpairs, ";") == "%24var1=val1;var2=%22val%20encoded%22"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

// Stuff other than a string 
nvpairs = Liftium.parseQueryString(null);
if (typeof nvpairs == "object" && nvpairs.length == 0){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}


// + 
nvpairs = Liftium.parseQueryString("?var1=val1&var2=%22val+encoded%22");
if (nvpairs.var1 == "val1" && nvpairs.var2 == '"val encoded"') {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

</script>

<?php require 'footer.php'?>

