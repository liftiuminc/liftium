<?php

class LiftiumCache extends Memcache {
        static function getInstance(){
        
                static $Cache;
                if (!empty($Cache)){
                        return $Cache;
                }

                $Cache = new LiftiumCache();
                
		foreach($GLOBALS['CONFIG']['memcached'] as $c){
                        $Cache->pconnect($c['host'], $c['port']) || trigger_error("Error connecting to memcached:" . print_r($c, true), E_USER_WARNING);
                }

                return $Cache;
        }


        function set($key, $value, $flag = null, $expire = 0){
                parent::set($key, $value, $flag, $expire);
        }
}
