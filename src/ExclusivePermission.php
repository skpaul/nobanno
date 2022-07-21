<?php

    //This class is used to bypass application submission startdate and enddate.
    //Only for testing purpose.
    class ExclusivePermission { 

        // public static $propName = "pro";
        // public static $propValue = "1978$2019";

        public static function hasPermission(string $key, string $value):bool{
            $proceedAnyWay = false;
            if(isset($_GET[$key]) && !empty($_GET[$key])){
                if($_GET[$key] === $value){
                    $proceedAnyWay = true;
                }
            }
            return $proceedAnyWay;
        }
    }
?>