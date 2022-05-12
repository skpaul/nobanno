<?php   


    include_once("../CONSTANTS.php");
    require_once(ROOT_DIRECTORY . "/lib/error_reporting.php"); 

    require_once(ROOT_DIRECTORY . "/lib/db_functions_7.php");
   
    $connection= create_database_connection();  
    Select_Database(DATABASE_NAME, $connection);
    
    if(isset($_POST["submit"])){
        
    }

    $sql = "SHOW TABLES FROM " . DATABASE_NAME ;
    $result = $connection->query($sql);
    $tableNames = '<option>Select..</option>';
    if(isset($_POST["submit"])){
        $table_name = $_POST["table_name"];
        while ($row = $result->fetch_array()) {
            $selected = "";
            if($row[0] == $table_name) $selected = "selected";
            $tableNames = $tableNames . '<option value="'. $row[0] .'" '. $selected .' >'. $row[0] .'</option>' ; //echo "Table: {$row[0]}\n";
        }
    }
    else{
        while ($row = $result->fetch_array()) {
            $tableNames = $tableNames . '<option value="'. $row[0] .'">'. $row[0] .'</option>' ; //echo "Table: {$row[0]}\n";
        }
   }
  //  $connection->close();
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Form Processor</title>

        <link href="https://fonts.googleapis.com/css?family=Montserrat|Roboto" rel="stylesheet">

        <style>
            body{
                font-family: 'Roboto', sans-serif;
            }
            h2{
                padding:0;
                margin:0;
                font-size:15px;
                font-family: 'Montserrat', sans-serif;
            }
            .container{
                width:90%; margin:auto;
            }
            #table-names{
                width:80%;
                padding:10px;
                box-sizing:border-box;
            }

            #prepare{
                width:19%;
                padding:10px;
                box-sizing:border-box;
            }

            textarea{
                width: 100%;
                height: 65px;
            }
         
        </style>
    </head>
    <body>
       
        <div class="container"> 

        <p>This script generates php code for incoming form data. It uses the database table structure to generate the code.</p>
            <form action="<?php $_SERVER["PHP_SELF"]?>" method="post">
                <select name="table_name" id="table-names"><?php  echo $tableNames; ?></select>  
                <button id="prepare" name="submit">Prepare</button>
            </form>
        </div>
      

        <div class="container"> 
        <?php
                if(isset($_POST["submit"])){
                    $table_name = $_POST["table_name"];

                    $query = "SELECT * from $table_name";

                    if ($result = mysqli_query($connection, $query)) {
                
                        /* Get field information for all columns */
                        $fields = mysqli_fetch_fields($result);

                        $all = ""; $height = 5;
                        foreach ($fields as $field) {

                            //form_validator_validate_string($html_form_field_name, $data_title, $minlength, $maxlength,$db_connection = null, $default_value=null)
                            $height += 20;
                            $field_name = $field->name;
                            $length = $field->length;
                            $type = $field->type;
                            $flags = $field->flags;

                            $method_name = "";
                            $min = "";
                            $max = "";

                             //Datatype List- https://www.php.net/manual/en/mysqli-result.fetch-fields.php
                            switch ($type){
                                case 253:
                                    $method_name = "form_validator_validate_string";
                                    $min = "minlength";
                                    $max = $length;
                                    break;
                                case 3:
                                    $method_name = "form_validator_validate_integer";
                                    $min = "minvalue";
                                    $max = "maxvalue";
                                    break;
                                case 4: //float
                                    $method_name = "form_validator_validate_float";
                                    $min = "minvalue";
                                    $max = "maxvalue";
                                    break;
                                case 1: //bool
                                    $method_name = "form_validator_validate_boolean";
                                    $min = "minvalue";
                                    $max = "maxvalue";
                                    break;
                                case  246: //decimal: 
                                    $method_name = "form_validator_validate_decimal";
                                    $min = "minvalue";
                                    $max = "maxvalue";
                                    break;
                                case  10: //date 
                                    $method_name = "form_validator_validate_date";
                                    $min = "minvalue";
                                    $max = "maxvalue";
                                    break;
                                case  12: //datetime 
                                    $method_name = "form_validator_validate_datetime";
                                    $min = "minvalue";
                                    $max = "maxvalue";
                                    break;
                            }

                            $html_form_field_name =  $data_title = $field_name;

                            if($flags == 0){
                                $allow_null = "allow null";
                            }

                            //form_validator_validate_string($html_form_field_name, $data_title, $minlength, $maxlength,$db_connection = null, $default_value=null)
                            $php = '$'. $field_name .' = ' . $method_name . '("'. $html_form_field_name .'", "'. $data_title .'", true, ' .$min . ', ' . $max . ', $connection = null, $default_value='. $allow_null .');';

                            $all .= '&#13;' . $php ;
                            echo '<h5>Column: ' . $field_name . ', Type: ' . $type . '</h3>'; 
                            echo '<div><textarea>' .  $php  . '</textarea></div>';
                            // printf("Max. Len:  %d\n",   $val->max_length);
                        }
                        mysqli_free_result($result);
                    }
                }
           ?>

            <br>
            <br>
            <h5>All together <button onclick="copyAllTogether();">copy to clipboard</button></h5>
            <textarea id="all-together" style="height:<?php echo $height; ?>px;"><?php echo $all; ?></textarea>
            <br>
        </div>  

        <script>
            function copyAllTogether(){
                var textarea = document.getElementById('all-together');
                textarea.select();
                document.execCommand('copy');
            }
        </script>
    </body>
</html>