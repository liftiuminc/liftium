<?php require 'header.php'?>
This page is for testing Liftium.empty.
<p>
<script>
// Truthies
Liftium.empty(false) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.empty(parseInt("blah")) ? LiftiumTest.testPassed() : LiftiumTest.testFailed(); 
Liftium.empty(parseFloat("blah")) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.empty("") ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.empty(null) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.empty({}) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.empty([]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.empty(0) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

// Falsies
!Liftium.empty(true) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty(parseInt("23")) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty(parseFloat("3.14159")) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty("string") ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty(Array(1,2,3)) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty({"foo":"bar"}) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty("0") ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.empty(Liftium) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

