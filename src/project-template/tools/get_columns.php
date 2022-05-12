<?php

    include_once("../CONSTANTS.php");
    require_once(ROOT_DIRECTORY . "/lib/error_reporting.php"); 
    require_once(ROOT_DIRECTORY . "/lib/db_functions_7.php");

    $tableName = $_GET['table'];


    // $dbname = "kushtia_bricks";

    // Create connection
    $connection= create_database_connection();  
    Select_Database(DATABASE_NAME, $connection);

   $sql = "SHOW COLUMNS FROM " . $tableName;
   $result = $connection->query($sql);

   //$row = $result->fetch_array();
   //echo json_encode($row);
    $array = array();
    while ($row = $result->fetch_array()) {
        array_push($array, $row[0]);
        //echo "Columns: {$row[0]}<br>";
    }

    echo json_encode($array);
    $connection->close();

?>
