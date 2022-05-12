<?php
   
   /*
   NOTE:: Example of using a function in heredoc
   =============================================
   <<<HTML
        <div>
            {$sampleFunction(date('r'))} 
        </div>
    HTML;
   */

   //Function name must be decalred as a variable.
  

    function sampleFunction($param){
        return $param;
    }

    $sampleFunction = "sampleFunction";


    function heredoc($param) {
        // just return whatever has been passed to us
        return $param;
    }
    
    $heredoc = 'heredoc';

    function createSerial( &$infoSerialNo ) { 
        $infoSerialNo++; 
        return $infoSerialNo . ". "; 
    } 
    $createSerial = 'createSerial';

   

?>