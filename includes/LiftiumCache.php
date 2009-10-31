<?php

class LiftiumCache extends Memcache {
        static function getInstance(){
        
                static $Cache;
                if (!empty($Cache)){
                        return $Cache;
                }

                $Cache = new LiftiumCache();
                
                global $DEV_HOSTS;
                if (in_array(Framework::getHostname(), $DEV_HOSTS)){
                        $Cache->pconnect('localhost', 11211) || error_log('Error connecting to memcached');
                } else {
                        $Cache->pconnect('memcached1', 11211) || error_log('Error connecting to memcached');
                        ### disabled memcached2 for now, because the ruby & php libraries are using different
                        ### hashing strategies. See FB 118 for details. -Jos
                        #$Cache->pconnect('memcached2', 11211) || error_log('Error connecting to memcached');
                }

                return $Cache;
        }


        function set($key, $value, $flag = null, $expire = 0){
                parent::set($key, $value, $flag, $expire);
        }
}
