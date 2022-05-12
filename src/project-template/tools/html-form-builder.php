<?php   

    include_once("../CONSTANTS.php");


    $connection = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
    if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);

    $sql = "SHOW TABLES FROM " . DB_NAME ;


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

  $all = "";
?>


<!DOCTYPE html>
<html>
    <head>
        <title>HTML Form Builder</title>

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
                height: 76px;
            }
         
        </style>
    </head>
    <body>
       
        <div class="container"> 
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
                            $height += 76;
                            $field_name = $field->name;
                            $length = $field->length;
                            $type = $field->type;
                            $flags = $field->flags;
                            
                            $html = '<!-- ' . $field_name . '  --> &#13;';
                            $html .= '<div class="field">  &#13;';
                            $html .= '  <label class="required">' . $field_name . '</label>&#13;';

                            $allow_null = "";
                            if($flags == 0){
                                $allow_null = "allow null";
                            }


                            //Datatype List- https://www.php.net/manual/en/mysqli-result.fetch-fields.php
                            switch ($type){
                                case 253: //253 string
                                    $html .= '  <input name="'. $field_name .'" id="'. $field_name .'" title="" class="validate" data-title="'. $field_name .'" data-datatype="string" data-required="required" data-minlen="'. $allow_null .'" data-maxlen="'. $length .'" >&#13;';
                                    break;
                                case 3: //integer
                                    $html .= '  <input name="'. $field_name .'" id="'. $field_name .'" title="" class="validate" data-title="'. $field_name .'" data-datatype="integer"  data-required="required"  data-minval="1" data-maxval="-1" >&#13;';
                                    break;
                                case 4: //float
                                    $html .= '  <input name="'. $field_name .'" id="'. $field_name .'" title="" class="validate" data-title="'. $field_name .'" data-datatype="float"  data-required="required"  data-minval="" data-maxval="" >&#13;';
                                    break;
                                case  246: //decimal: 
                                    $html .= '  <input name="'. $field_name .'" id="'. $field_name .'" title="" class="validate" data-title="'. $field_name .'"  data-datatype="decimal"  data-required="required"  data-minval="" data-maxval="" >&#13;';
                                    break;
                                case  10: //date 
                                    $html .= '  <input name="'. $field_name .'" id="'. $field_name .'" title="" class="validate" data-title="'. $field_name .'"  data-datatype="date"  data-required="required"  data-minval="" data-maxval="" >&#13;';
                                    break;
                                case  12: //datetime 
                                    $html .= '  <input name="'. $field_name .'" id="'. $field_name .'" title="" class="validate" data-title="'. $field_name .'"  data-datatype="datetime"  data-required="required"  data-minval="" data-maxval="" >&#13;';
                                    break;
                                case 1: //bool
                                    break;

                            }

                            $html .= '</div>';

                            $all .= '&#13;' . $html ;
                            echo '<div>Field: ' . $field_name . ', Type: ' . $type . '</div>'; 
                            echo '<div><textarea>' .  $html  . '</textarea></div>';
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