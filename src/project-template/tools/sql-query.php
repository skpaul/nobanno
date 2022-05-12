<?php   

    require_once("../Required.php");

    require_once("prevent_access_if_not_localhost.php");
    
    Required::SwiftLogger()
                ->ZeroSQL()
                ->SwiftDatetime()
                ->SwiftCSRF()
                ->EnDecryptor()
                ->Validable()
                ->SwiftJSON()
                ->Taka()
                ->With();
 
    date_default_timezone_set('Asia/Dhaka');
    $logger = new SwiftLogger(ROOT_DIRECTORY, false);
    $db = new ZeroSQL();
    $db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME)->connect() ;

    $sql = "SHOW TABLES FROM " . DATABASE_NAME ;
    
    //fetchRow() is required to show records by their index.
    $rows = $db->fetchRow()->showTables();

    $columnName = "Tables_in_".DATABASE_NAME;

    $tableNames = '<option>Select..</option>';

    if(isset($_POST["submit"])){
        $table_name = $_POST["table_name"];
        foreach ($rows as $row) {
            $selected = "";
            if($row[0] == $table_name) $selected = "selected";
            $tableNames = $tableNames . '<option value="'. $row[0] .'" '. $selected .' >'. $row[0].'</option>' ; //echo "Table: {$row[0]}\n";
        }
    }
    else{
        foreach ($rows as $row) {
            $tableNames = $tableNames . '<option value="'. $row[0] .'">'. $row[0] .'</option>' ;
        }
   }
  
?>


<!DOCTYPE html>
<html>
    <head>
        <title>SQL</title>
        <link rel="shortcut icon" type="image/png" href="images/sql.png"/>
        <style>

            *{
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }
            .container{
                width: 100%;
                margin: auto;
                display: flex;
                flex-direction: row-reverse;
            }

            .form-container{
                width:40%;
                position: relative;
            }

            form{
                position: fixed;
                width: 506px;
            }
            .data-container{
                width: 60%;
            }
            select{
                width: 100%;
                padding:5px;
                margin-bottom: 20px;
            }


            textarea{
                width: 100%;
                height: 250px;
            }

            button{
                width: 159px;
                height: 50px;

            }
            table{
                border: 1px solid lightblue;
                padding: 5px;
                width: 100%;
                margin: auto;
               
                font-size: 0.7rem;
                margin-bottom: 20px;
            }

            tr{

            }

            td{
                background-color: lightblue;
                padding:2px;
            }

            .column{
                font-weight: bold;
                text-align: right;
                width: 45%;
                padding-right: 10px;
            }
            .value{
                padding-left: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container"> 
            <div class="form-container">
                <form action="<?php $_SERVER["PHP_SELF"]?>" method="post">
                    <select name="table_name" id="table-names" onchange="clearTextarea()"><?php  echo $tableNames; ?></select>  
                    <?php
                        if(isset($_POST["submit"])){
                            $table_name = $_POST["table_name"];

                            if(isset($_POST["truncate"])){
                                $truncate = $_POST["truncate"];
                                if($truncate==1){
                                    // $sql = "TRUNCATE TABLE $table_name";
                                    // $result = mysqli_query($connection, $sql);
                                    $db->truncate($table_name);
                                }
                            }

                            if(empty($_POST["sql"])){
                               // $query = "SELECT * from $table_name";
                               // $query = $db->select()->Columns("*")->From($table_name)->fetchField()->many();
                               // $fields = $query->rows;
                                $columns = $db->fetchObject()->showColumns($table_name);
                                $select_sql = ""; 
                                    $is_first = true;
                                    foreach ($columns as $column) {
                                        
                                       // $field_name = $field->name;
                                        if($is_first) {
                                            $select_sql = "SELECT " . $column->Field ;
                                            $is_first = false;
                                        }
                                        else{
                                            $select_sql .= ', ' . $column->Field ;
                                        }
                                    }
        
                                    $select_sql .= " FROM " . $table_name;
                            }
                            else{
                                $select_sql = $_POST["sql"];
                            }
                            
                        }
                    ?>
                    <textarea id="textarea" name="sql" style=""><?php echo $select_sql; ?></textarea>
                    <div style="text-align:center; display: none;">
                        <label>
                            <input type="checkbox" name="truncate" value="1"> Truncate table
                        </label>
                    </div>
                
                    <div style="text-align:center; padding:15px;">
                        <button id="prepare" name="submit">Prepare</button>
                    </div>

                
                </form>
            </div>

            <div class="data-container">
                <?php
                    if(isset($_POST["submit"])){
                        //extract select/insert/update/delete keyword from sql statement.
                        $queryType =strtoupper(substr(trim($select_sql),0,1));

                        // Select query starts
                        if($queryType== "S") {
                            $rows = $db->select($select_sql)->fromSQL()->fetchAssoc()->toList();
                            foreach($rows as $row){
                                echo "<table>";
                                foreach($row as $key => $value){
                                    echo '<tr>';
                                    echo '<td class="column">'.$key.'</td>';
                                    echo '<td class="value">'.$value.'</td>';
                                    echo '</tr>';
                                }
                                echo "</table>";
                            }
                        }//select query ends
                        else{
                            //insert, update, delete query starts
                            switch ($queryType) {
                                case 'I':
                                    $result = $db->insert($select_sql)->fromSQL()->execute();
                                    echo "$result rows affected.";
                                    break;
                                case 'U':
                                    $result = $db->update($select_sql)->fromSQL()->execute();
                                     echo "$result rows affected.";
                                    break;
                                case 'D':
                                    $result = $db->delete($select_sql)->fromSQL()->execute();
                                     echo "$result rows affected.";
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            //in    sert, update, delete query ends
                        }
                       
                    }
                ?>
            </div>
        </div>

        <script>
            function clearTextarea(){
                var textArea = document.getElementById('textarea');
                textArea.value='';
            }
        </script>
    </body>
</html>