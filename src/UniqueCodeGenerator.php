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
            $charCodes = [65,66,67,68,69,70,71,72,74,75,77,78,80,81,82,83,84,85,86,87,88,89,90];
            $code ='';
            for ($i=1; $i <= $length; $i++){ 
                $random_number = rand(0,22); 
                $code .= chr($charCodes[$random_number]);
            }

            /*
                //A more reliable answer is geven by scott 
                //in StackOverflow question - "PHP: How to generate a random, unique, //alphanumeric string for use in a secret link?"

                $bytes = random_bytes(6); 
                $code3 = bin2hex($bytes); create code with 12 digits/characters
            */

            $pdo = $this->db->getPDO();
            $code = $prefix.$code;
            $sql = "SELECT COUNT($column) AS Qty FROM $table WHERE $column='$code'";

            $statement =  $pdo->prepare($sql) ;
            $statement->setFetchMode(PDO::FETCH_ASSOC); 
            $statement->execute();
            $count = ($statement->fetch())["Qty"];
           
            if($count > 0)
                $this->generate($length, $column,$table);

            return $code;
        }
    }
?>