<?php
/* This script goes through memcache and collects the stats that have been streaming in real time,
 * and stores them permanently in the database. It's designed to run every minute.
 */
ini_set('display_errors', true);
require_once dirname(__FILE__) . '/../includes/Framework.php';

$activeTags = AdTag::searchTags(array('enabled'=>1));
$cache = LiftiumCache::getInstance();
$dbw = Framework::getDB("master");

$maxBack = 2;
$now = time();

foreach ($activeTags as $tag){
        echo "Gathering stats for {$tag->tag_name}\n";

        $minutesBack = 0; $time=$now;

        // When attempts is false, that means there were no entries for that minute
        while ($minutesBack <  $maxBack){
                $minutesBack++;
                $attempts = $loads = $rejects = 0;
                $time = strtotime("-$minutesBack minutes");

                // Attempts
                $key = EventRecorder::serializeKey(array('Attempt', $tag->id), "minute", $time);
                echo "Checking $key\n";
                $attempts = $cache->get($key);

                if (empty($attempts)){
                        // No stats for this tag, don't bother
                        echo "No stats!\n";
                        continue;
                }

                // Loads
                $key = EventRecorder::serializeKey(array('Load', $tag->id), "minute", $time);
                $loads = $cache->get($key);

                // Rejects
                $key = EventRecorder::serializeKey(array('Reject', $tag->id), "minute", $time);
                $rejects = $cache->get($key);

                // Load into database
                $sql = "REPLACE INTO fills_minute VALUES ({$tag->id}, '" . date('Y-m-d H:i:00', $time) . "'," .
                        $dbw->quote($attempts) . "," .
                        $dbw->quote($loads) . "," .
                        $dbw->quote($rejects) . ")";
                echo "$sql\n";
                $dbw->exec($sql);
        }
}
echo "Done";
?>
