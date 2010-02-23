<?php
require_once dirname(__FILE__) . '/gapi.class.php';

define('ga_email','nick+ga@liftium.com');
define('ga_password','liftium123');
define('ga_profile_id','24566821');
define('ga_launch_date','2009-12-15');


class liftium_gapi extends gapi {

        function __construct( ){
                parent::__construct(ga_email,ga_password);
        }

        function getPageViewsByDate($start_date = ga_launch_date, $end_date = null, $filter = null){

                $out = array(); $start = 1; $numApiRows = 10000; $recordsLeft = true; $loops = 0;
                while ($recordsLeft && $loops < 50) {
                        $loops++; // safety net to prevent never ending loop

                        $this->requestReportData(ga_profile_id,array("pagePath", "date"),array('pageviews'), array('-pageviews'), $filter, $start_date, $end_date, $start, $numApiRows);

                        $numResults = 0;
                        foreach($this->getResults() as $result) {
                                $numResults++;
				$d = $result->getDimensions();
				$m = $result->getMetrics();
                                $out[$d['pagePath']][$d['date']] = intval($m['pageviews']);
                        }

                        if ($numResults < $numApiRows) {
                                $recordsLeft = false;
                                break;
                        } else {
                                $start += $numApiRows;
                        }
                }

                return $out;
        }

}
