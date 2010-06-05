<?php
require_once dirname(__FILE__) . '/../includes/Framework.php';

$dbw = Framework::getDB("master");
$flagfile = '/tmp/liftium.rotator.lastrun';
if (file_exists($flagfile)){
        // Don't reload anything more than we have to
        $min_date = date(MYSQL_DATE_FORMAT, filemtime($flagfile) - 3600);
} else {
        $min_date = date(MYSQL_DATE_FORMAT, strtotime('-10 years'));
}

$dbw->exec("REPLACE INTO fills_hour SELECT tag_id,
        DATE_FORMAT(minute, '%Y-%m-%d %H:00:00') AS hour,
        SUM(attempts) AS attempts, SUM(loads) AS loads,
        SUM(rejects) AS rejects FROM fills_minute WHERE
        minute >= DATE_FORMAT(" . $dbw->quote($min_date) . 
        ", '%Y-%m-%d %H:00:00') GROUP BY hour, tag_id;");

$dbw->exec("REPLACE INTO fills_day SELECT tag_id,
        DATE_FORMAT(hour, '%Y-%m-%d') AS day,
        SUM(attempts) AS attempts, SUM(loads) AS loads,
        SUM(rejects) AS rejects FROM fills_hour GROUP BY day, tag_id;");

$dbw->exec("DELETE FROM fills_minute WHERE minute < " . $dbw->quote(date(MYSQL_DATE_FORMAT, strtotime('-7 days'))));

$dbw->exec("DELETE FROM fills_hour WHERE hour < " . $dbw->quote(date(MYSQL_DATE_FORMAT, strtotime('-1 month'))));

$dbw->exec("DELETE FROM fills_day WHERE day < " . $dbw->quote(date(MYSQL_DATE_FORMAT, strtotime('-1 year'))));

touch($flagfile);
