<?php

    class UniqueCodeGenerator{
       
        protected $db;
        
        public function __construct($ExPDO) {
            $this->db = $ExPDO;
        }

        /*
        // 48	0	
        // 49	1	
        50	2
        51	3	
        52	4	
        53	5
        54	6
        55	7	
        56	8
        57	9
        */

        public function generate($length, $column, $table, $prefix = ""){            
            $charCodes = [50,51,52,53,54,55,56,57,65,66,67,68,69,70,71,72,74,75,77,78,80,81,82,83,84,85,86,88,89,90];
            $code ='';
            for ($i=1; $i <= $length; $i++){ 
                $random_number = rand(0,29); 
                $code .= chr($charCodes[$random_number]);
            }

            /*
                //A more reliable answer is geven by scott 
                //in StackOverflow question - "PHP: How to generate a random, unique, //alphanumeric string for use in a secret link?"

                $bytes = random_bytes(6); 
                $code3 = bin2hex($bytes); create code with 12 digits/characters
            */
            
            $code = $prefix.$code;
            $sql = "SELECT COUNT($column) AS Qty FROM $table WHERE $column='$code'";
            $result =  $this->db->fetchAssoc($sql);
            $count = $result["Qty"];
           
            if($count > 0)
                $this->generate($length, $column,$table);

            return $code;
        }
    }
?>