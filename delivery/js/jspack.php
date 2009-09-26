<?php
// you can pass this script to PHP CLI to minify/obfuscate your javascript

if (empty($argv[1])) {
        echo 'you must specify  a source file and a result filename',"\n";
        echo 'example :', "\n", 'php jspack.php myScript-src.js myPackedScript.js',"\n";
        return;
} else if (empty($argv[2])){
	// One file specified. pack in place
        $out = $argv[1];
	$tempfile = tempnam("/home/tempfiles/3hours/", basename($out));
        $src = $tempfile;
	copy($out, $src);
} else {
	$src = $argv[1];
	$out = $argv[2];
}

require '../../includes/JavaScriptPacker.php';

$script = file_get_contents($src);

$t1 = microtime(true);

$packer = new JavaScriptPacker($script, 'Normal', true, false);
$packed = $packer->pack();

$t2 = microtime(true);
$time = sprintf('%.4f', ($t2 - $t1) );
echo "$out script minified/obfuscated in $time seconds\n";

file_put_contents($out, $packed);
?>
