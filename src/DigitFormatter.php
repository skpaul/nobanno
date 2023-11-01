<?php
    class DigitFormatter{
        public static function format($value){
            $number = strval($value);
            $len = strlen($number);
            $output = "";
            $position = 1;
            for ($i = $len-1; $i >= 0; $i--)
            {
                if($position == 4){
                    $output = "," . $output;
                }
                if($position == 6 || $position==8 || $position==10){
                    $output = "," . $output;
                }
                $output = substr($number, $i, 1) . $output; 
                $position++;
            }
    
            return $output;    
        }
    }
?>