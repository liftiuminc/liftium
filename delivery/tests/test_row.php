<?php require 'header.php'?>
This page is for testing ____________
<p>
<script>
Liftium.getCountryFound = "us";
Liftium.isValidCountry(["us"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.isValidCountry(["uk"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.isValidCountry(["us", "uk", "row"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

Liftium.getCountryFound = "unknown";
!Liftium.isValidCountry(["us"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!Liftium.isValidCountry(["uk"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.isValidCountry(["us", "uk", "row"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

Liftium.getCountryFound = "pl";
!Liftium.isValidCountry(["us"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.isValidCountry(["uk", "row"]) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

