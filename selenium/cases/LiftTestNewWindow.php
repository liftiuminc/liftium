<?php
/* This generic Symfony test case simply opens the supplied $file (an athena test case)
 * and checks for "0 tests failed" in the title upon page load
 */
if (empty($_GET['file'])){
	echo "file is required";
	exit;
}
$window = preg_replace("/[^a-z_0-9]/", "", $_GET['file']);
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://selenium-ide.openqa.org/profiles/test-case">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="selenium.base" href="" />
<title>New Test</title>
</head>
<body>
<table cellpadding="1" cellspacing="1" border="1">
<thead>
<tr><td rowspan="1" colspan="3"><?php echo htmlspecialchars($_GET['file'])?></td></tr>
</thead><tbody>
<tr>
	<td>openWindow</td>
	<td>/tests/<?php echo htmlspecialchars($_GET['file'])?></td>
	<td><?php echo $window?></td>
</tr>
<tr>
	<td>waitForPopUp</td>
	<td><?php echo $window?></td>
	<td>10000</td>
</tr>
<tr>
	<td>selectWindow</td>
	<td><?php echo $window?></td>
	<td></td>
</tr>
<tr>
        <td>waitForText</td>
        <td>id=page_loaded</td>
        <td>glob:*Page Loaded*</td>
</tr>
<tr>
	<td>verifyText</td>
	<td>id=test_results</td>
	<td>glob:*0 tests failed*</td>
</tr>
<tr>
	<td>close</td>
	<td></td>
	<td></td>
</tr>
</tbody></table>
</body>
</html>
