<?php


    

    class Person{
        public $var = "";

        /**
         * @deprecated
         *
         * @return string 
         */
        function oldMethod(string $kk)
        {
            trigger_error('Method "' . __METHOD__ . '()" is deprecated, use "newMethod()" instead.', E_USER_DEPRECATED); //E_USER_NOTICE

            return "hi";
        }
    }

    $person = new Person();
    $person->var = "saumitra";
    $person->oldMethod("hi");

    echo $person->var;
?>