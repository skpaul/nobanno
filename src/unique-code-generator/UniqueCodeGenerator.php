<?php

    class UniqueCodeGenerator{
        /** @var ZeroSQL */
        protected $db;
        
        /**
         * @param string $alphabet
         */
        public function __construct($zeroSql) {
            $this->db = $zeroSql;
        }

        public function generate($length, $column, $table, $prefix = ""){
             //from opu
            $code ='';
            for ($i=1; $i <= $length; $i++){ 
                $random_number = rand(65,90); 
                $code .= chr($random_number);
            }

            /*
                //A more reliable answer is geven by scott 
                //in StackOverflow question - "PHP: How to generate a random, unique, //alphanumeric string for use in a secret link?"

                $bytes = random_bytes(6); 
                $code3 = bin2hex($bytes); create code with 12 digits/characters
            */

            $code = $prefix.$code;
            $sql = "SELECT $column FROM $table WHERE $column=:code";
            $data = $this->db->select($sql, array("code"=>$code));
            if(count($data) > 0)
                $this->generate($length, $column,$table);
            // if($isFound != null){
            //     $this->generate($length, $column,$table);
            // }

            return $code;
        }
    }
?>