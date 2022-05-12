<?php
    class HttpHeader
    {
        /**
         * Remove current header
         * 
         * @return new static
         */
        public static function remove(){
            header_remove();
            return new static;
        }

        public static function setJson(){
            header("Content-type: application/json; charset=utf-8");
            return new static;
        }
        
        public static function redirect($url){
            // After header(...); you must use exit;
            //HTTP Response Codes: 301, 302, 303
            //301- Permanent redirect, 302- Temporary redirect, 303- Other.
            exit(header("location:$url",true, 302));
        }

        public static function set200(){
            self::_set(200);
            return new static;
        }

        public static function set($httpResponseCode){
            self::_set($httpResponseCode);
            return new static;
        }

        //Static methods should only be called with static:: or self::
        private static function _set(int $code){
            http_response_code($code);
        }
    }
    
?>