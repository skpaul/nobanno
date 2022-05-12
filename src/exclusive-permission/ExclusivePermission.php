<?php

    //This class is used to bypass application submission startdate and enddate.
    //Only for testing purpose.
    class ExclusivePermission { 

        public static $propName = "pro";
        public static $propValue = "1978$2019";

        public static function hasPermission(){
            $proceedAnyWay = false;
            if(isset($_GET[self::$propName]) && !empty($_GET[self::$propName])){
                if($_GET[self::$propName] === self::$propValue){
                    $proceedAnyWay = true;
                }
            }
            return $proceedAnyWay;
        }
    }
?>