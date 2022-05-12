<?php 


//echo Taka::format(12345670000);

class Taka{
    public static function format($amount, $default='0.00'){
       
        if(empty($amount)){
            return $default;
        }

        $negative ="";
        if(strstr($amount,"-")){ 
            $amount = str_replace("-","",$amount); 
            $negative = "-"; 
        } 
        
        $amount = number_format((float)$amount, 2, '.', '');

        $split_number = explode(".",$amount); 
        
        $taka = $split_number[0]; 
        $poisa = $split_number[1]; 
        
        if(strlen($taka)>3){ 
            $thousands ="";
            $hundreds = substr($taka,strlen($taka)-3); 
            $strlen = strlen($taka)-3;
            $substr = substr($taka,0, $strlen);
           
            $thousands = "";
            $counter = 0;
            for ($i=$strlen-1; $i >=0 ; $i = $i - 1) { 
                $digit = $substr[$i];
                $temp = $digit.$thousands;
                $thousands = $temp;
                $counter = $counter + 1;
                if($counter == 2){
                    if($i >0){
                        $thousands = ",". $thousands;
                    }
                    $counter = 0;
                }
            }

            $formatted_taka = $thousands.",".$hundreds; 
        } 
        else{ 
            $formatted_taka = $taka; 
        } 
        
        $formatted_poisa ="00";

        if((int)$poisa>0){ 
            $formatted_poisa = substr($poisa,0,2); 
        } 
        
        return $negative.$formatted_taka.'.'.$formatted_poisa; 
    } 
}
?>