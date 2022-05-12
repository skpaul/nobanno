<?php   

    require_once("../Required.php");

    require_once("prevent_access_if_not_localhost.php");
    

 
    date_default_timezone_set('Asia/Dhaka');
    $connection = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
    if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

    $sql = "SHOW TABLES FROM " . DB_NAME ;


    $result = $connection->query($sql);
    


    
//     //fetchRow() is required to show records by their index.
//     $rows = $db->fetchRow()->showTables();

//     $columnName = "Tables_in_".DB_NAME;

//     $tableNames = '<option>Select..</option>';

//     if(isset($_POST["submit"])){
//         $table_name = $_POST["table_name"];
//         foreach ($rows as $row) {
//             $selected = "";
//             if($row[0] == $table_name) $selected = "selected";
//             $tableNames = $tableNames . '<option value="'. $row[0] .'" '. $selected .' >'. $row[0].'</option>' ; //echo "Table: {$row[0]}\n";
//         }
//     }
//     else{
//         foreach ($rows as $row) {
//             $tableNames = $tableNames . '<option value="'. $row[0] .'">'. $row[0] .'</option>' ;
//         }
//    }


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





















  
   function snakeToCamel($val) {  
        preg_match('#^_*#', $val, $underscores);
        $underscores = current($underscores);
        $camel = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));  
        $camel = strtolower(substr($camel, 0, 1)).substr($camel, 1);

        return $underscores.$camel;  
    } 

    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

?>


<!DOCTYPE html>
<html>
    <head>
        <title>Validables</title>
        <link rel="shortcut icon" type="image/png" href="images/validables.png"/>
        <style>

            *{
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }
            .container{
                width: 100%;
                margin: auto;
                display: flex;
                flex-direction: column;
            }

            .form-container{
                width: 96%;
    margin: 0 auto;
    /* border: 1px solid; */
            }
            form{
                display: flex;
            }

            select{
                width: 80%;
                /* padding:5px; */
            }

            .button-div{
                flex-grow: 1;
            }
            button{
                height: 44px;
                /* margin-left: 20px; */
                width: 100%;
            }
            .data-container{
               
                width: 96%;
                margin: auto;
                margin-top: 36px;
                border: 1px solid #e4dddd;
                padding: 14px;
            }
        
            .validable{
                font-family: monospace;
                padding: 15px 5px;
            }

            .validable:hover{
                cursor: pointer;
                background-color: #f1eded;
                
            }
        </style>
    </head>
    <body>
        <div class="container"> 
            <div class="form-container">
                <form action="<?php $_SERVER["PHP_SELF"]?>" method="post">

                    <select name="table_name" id="table-names"><?php  echo $tableNames; ?></select>  
                    <?php
                        if(isset($_POST["submit"])){
                            $table_name = $_POST["table_name"];                            
                        }
                    ?>
                                  
                    <div class="button-div" style="text-align:center;">
                        <button id="prepare" name="submit">Prepare Validables</button>
                    </div>
                
                </form>
            </div>

            <div class="data-container">
                <?php
                    if(isset($_POST["submit"])){
                        // $query = "SELECT * from $table_name";
                        $query = "SHOW COLUMNS FROM `$table_name`";
                        $result = mysqli_query($connection, $query);

                        $rows = []; //array();
                        while ($row = $result->fetch_object()) {
                            $rows[] = $row;
                        }
                       
                       $allString = "";
                       $id = 1;
                       foreach ($rows as $column) {
                        
                            $databaseColumn = $column->Field;
                            $label = ucfirst(str_replace('_', ' ', $databaseColumn));
                        
                            //HTTP POST/GET field name
                            $field = $databaseColumn;//snakeToCamel($databaseColumn);
                            //$field = ucfirst($field); //This is for temporrary for application BJSC only. Remove it later.

                            $allowNull = $column->Null; //YES NO
                        
                            // $requiredOrOptional = $allowNull == "NO"? "required()->":"";
                            $requiredOrOptional = "required()->"; //By default set it required.

                            $dataType = $column->Type;
                            $length = get_string_between($dataType, "(",")");
                            $dataType = explode("(", $dataType);
                            $dataType = $dataType[0];
                            $asType = "";
                            switch ($dataType) {
                                case 'tinyint':
                                    $asType = 'asBool()->maxLen('.$length.')->';
                                    if($column->Default == null){
                                        $default = 'default(NULL)->';
                                    }
                                    else{
                                        $default = 'default('.$column->Default .')->';
                                    }
                                    break;
                                case 'int':
                                    $asType = 'asInteger(false)->maxLen('.$length.')->';
                                    if($column->Default == null){
                                        $default = 'default(NULL)->';
                                    }
                                    else{
                                        $default = 'default('.$column->Default .')->';
                                    }
                                    break;
                                case 'varchar':
                                    $asType = 'asString(true)->maxLen('.$length.')->';
                                    if($column->Default == null){
                                        $default = 'default(NULL)->';
                                    }
                                    else{
                                        $default = 'default(\''.$column->Default .'\')->';
                                    }
                                    break;
                                case 'float':
                                case 'decimal':
                                    $floatLength = explode(",", $length);
                                    $floatLength = $floatLength[0];
                                    $floatLength = $floatLength + 1; //add 1 extra length for decimal point.
                                    $asType = 'asFloat(true)->maxLen('.$floatLength.')->';
                                    if($column->Default == null){
                                        $default = 'default(NULL)->';
                                    }
                                    else{
                                        $default = 'default('.$column->Default .')->';
                                    }
                                    break;
                                case 'date':
                                case 'datetime':    
                                    $asType = 'asDate()->';
                                    if($column->Default == null){
                                        $default = 'default(NULL)->';
                                    }
                                    else{
                                        $default = 'default(\''.$column->Default .'\')->';
                                    }
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        
                            // //As object
                            // $validable = '$'.$table_name.'->'.$databaseColumn.'= $form->label("'.$label.'")->post("'.$field.'")->'.$requiredOrOptional.''.$asType.$default .'validate();' ;
                            
                            //As array
                            $validable = '$data["'.$databaseColumn.'"] = $form->label("'.$label.'")->post("'.$field.'")->'.$requiredOrOptional.''.$asType.$default .'validate();' ;
                           

                            echo '<div id="'. $id .'" class="validable" title="Double click to copy.">' . $validable.'</div>' ;
                            
                            $id ++;
                            
                    }
                       
                    }
                ?>
            </div>
        </div>

        <script>
            var validable = document.getElementsByClassName("validable");
            var myFunction = function() {
                this.style.color = "#c3bcbc";
                this.style.textDecoration = "line-through";

                copyToClipboard(this.innerText);
            };

            for (var i = 0; i < validable.length; i++) {
                validable[i].addEventListener('dblclick', myFunction, false);
            }

            function copyToClipboard (text) {
                if (navigator.clipboard) { // default: modern asynchronous API
                    return navigator.clipboard.writeText(text);
                } else if (window.clipboardData && window.clipboardData.setData) {     // for IE11
                    window.clipboardData.setData('Text', text);
                    return Promise.resolve();
                } else {
                    // workaround: create dummy input
                    const input = h('input', { type: 'text' });
                    input.value = text;
                    document.body.append(input);
                    input.focus();
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                    return Promise.resolve();
                }
            }

        </script>
    </body>
</html>


