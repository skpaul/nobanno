<?php
   
   namespace SugarPHP;
   
    class RadioButton { 

        public static function isChecked($value1, $value2){
            if ($value1 == $value2) 
                return 'checked'; 
            else
                return ''; 
        }
    } 
?>