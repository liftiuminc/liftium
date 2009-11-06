<?php

// show errors loudly
error_reporting(E_STRICT | E_ALL);
ini_set('display_errors', true);

$ToTest = array( 'masterdb', 'slavedb1' ); 
$User   = 'liftiumprod';
$Pass   = 'gorilla';
$Stat   = 'Uptime';

foreach ( $ToTest as $host ) {
    $dsn = "mysql:dbname=liftium;host=" . $host;
    $dbh = new PDO( $dsn, $User, $Pass );
    foreach ( $dbh->query( "show status like '$Stat'" ) as $row ) {
        $res = $row['Value'];

	echo $host ." ". ($res > 0 ? "OK: $res" : "ERROR") ."<br>\n";
    }
}

?>
