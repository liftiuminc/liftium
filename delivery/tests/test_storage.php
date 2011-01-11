<?php require 'header.php'?>
This page is for testing our client side storage mechanism
test_preserve had a value of '<script>document.write(Liftium.Storage.getEngine().get("test_preserve"));</script>' from a previous test. Clearing...  <script>Liftium.Storage.getEngine().del("test_preserve");</script>
<p>
<script>
function testStorage(storage) {
	storage.clear();
	storage.get("string") === null ? LiftiumTest.testPassed() : LiftiumTest.testFailed("Initial value should be null" + storage.get("string"));
	storage.set("string", "test string");
	storage.get("string") === "test string" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test string: " + storage.get("string"));
	storage.del("string", "test string");
	storage.get("string") === null ? LiftiumTest.testPassed() : LiftiumTest.testFailed("str delete test");

	// Make sure data is encoded properly
	storage.set("empty string", "");
	storage.get("empty string") === "" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("empty string: " + storage.get("empty string"));

	storage.set("funky key !@#$%^&*()\"'", "funky value !@#$%^&*()\"'");
	storage.get("funky key !@#$%^&*()\"'") === "funky value !@#$%^&*()\"'" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("funky key: " + storage.get("funky key !@#$%^&*()\"'"));


	// NOTE that it was an intentional design decision to convert everything to a string,
	// since that's what both cookies and HTML 5 storage do under the hood, and the overhead
	// maintaining strict types (by accompanying every variable with meta data) was undesirable
	//
	// So these tests for different types assume that the return will be the *string* form
	storage.set("int", 42);
	storage.get("int") === "42" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test int: " + storage.get("int"));

	storage.set("undefined", undefined);
	storage.get("undefined") === "undefined" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test undefined: " + storage.get("undefined"));

	storage.set("true", true);
	storage.get("true") === "true" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test true: " + storage.get("true"));

	storage.set("false", false);
	storage.get("false") === "false" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test false: " + storage.get("false"));

	storage.set("null", null);
	storage.get("null") === "null" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test null: " + storage.get("null"));

	storage.set("NaN", parseInt("foo", 10));
	storage.get("NaN") === "NaN" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test NaN: " + storage.get("NaN"));

	storage.set("array", [1, 2, 3]);
	if (window.JSON) {
		// This browser supports JSON, array should be json
		storage.get("array") === "[1,2,3]" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test array: " + storage.get("array"));
	} else {
		// This browser does not support JSON, array should be 1,2,3 (without the block parens)
		storage.get("array") === "1,2,3" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test array: " + storage.get("array"));
	}

	storage.set("object", {"foo": "bar"});
	if (window.JSON) {
		// This browser supports JSON, object should work
		storage.get("object") === '{"foo":"bar"}' ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test object: " + storage.get("object"));
	} else {
		// This browser does not support JSON, object should fail gracefully
		storage.get("object") === '[object Object]' ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test object: " + storage.get("object"));
	}

	storage.inc("test_increment"); 
	storage.inc("test_increment"); 
	storage.inc("test_increment"); 

	storage.get("test_increment") === "3" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("test increment: " + storage.get("test_increment"));

	// Test expiration date
	storage.set("future expire", "hi", 60);
	storage.get("future expire") == "hi" ? LiftiumTest.testPassed() : LiftiumTest.testFailed("future expires");
	storage.set("past expire", "hi", -5);
	storage.get("past expire") === null ? LiftiumTest.testPassed() : LiftiumTest.testFailed("past expires" + storage.get("past expire") );

	storage.count() === 13 ? LiftiumTest.testPassed() : LiftiumTest.testFailed("count before expunge: " + storage.count());
	Liftium.Storage.expunge(storage);

	storage.count() === 12 ? LiftiumTest.testPassed() : LiftiumTest.testFailed("count after expunge, before clear: " + storage.count());

	storage.clear(); // Should remove all values

	storage.count() === 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed("count after clear: " + storage.count());
}

for (var i = 0; i < Liftium.Storage.availableEngines.length; i++) {
	var storage = Liftium.Storage.availableEngines[i];
	if (storage.isSupported()){
		document.write("Testing engine " + storage.name + "...<br />");
		testStorage(storage);
	} else {
		document.write("Skipping engine " + storage.name + " because it's not supported on this browser<br />");
	}
}


// Set a value that is preserved, so that if the tester reloads the page, they can see that it survives
Liftium.Storage.getEngine().set("test_preserve", "Liftium is the best!");

</script>

<?php require 'footer.php'?>

