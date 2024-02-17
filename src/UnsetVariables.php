<?php
    /*Ther caller script must decalre $retainedVariables variable and add value to it -
        for example-
        $retainedVariables[] = "db";
        $retainedVariables[] = "validator";
    */
    
    $decalaredVars =  array_keys(get_defined_vars());
    $variablesToUnset = array_diff($decalaredVars, $retainedVariables);
    foreach($variablesToUnset as $var) unset(${"$var"});
?>