<?php $LiftiumOptions = array("disabled" => true) ?>
<?php require 'header.php'?>

This page is for testing Liftium.print_r.
<p>
<script>

Liftium.print_r(true) === "<<true>>" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r(false) === "<<false>>" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r(42) === "42" ? LiftiumTest.testPassed() : LiftiumTest.testFailed(); 
Liftium.print_r(3.14159) === "3.14159" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r("") === "<<empty string>>" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r(null) === "<<null>>" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r({}) === "<<empty object>>" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r(0) === "0" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r("string") === "string" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r([1,2,3]) != "" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r({"foo":"bar"}) != "" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r(Liftium.config) != "" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.print_r(Liftium.empty) ===  "<<function>>" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>
Array:
<pre>
<script>document.write(Liftium.print_r([1,2,3,4,5])) && LiftiumTest.testPassed()</script>
</pre>
<hr />

Assoc Array:
<pre>
<script>document.write(Liftium.print_r({"var1":"val1", "var2": "val2", "var3": [1, 2, 3]})) && LiftiumTest.testPassed()</script>
</pre>

<?php require 'footer.php'?>

