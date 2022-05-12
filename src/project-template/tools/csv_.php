<?php   


    require_once("../CONSTANTS.php");
    require_once("prevent_access_if_not_localhost.php");

    require_once(ROOT_DIRECTORY . "/lib/Logger/SwiftLogger.php"); 
    require_once(ROOT_DIRECTORY . "/lib/Database/ZeroSQL.php");

    $logger = new SwiftLogger(ROOT_DIRECTORY);
    $db = new ZeroSQL();
    $db->Server(DATABASE_SERVER)->User(DATABASE_USER_NAME)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->Connect();

    $select_sql = $_POST["sql"];

   
    
    $connection = $db->getConnection();

    if(isset($_POST["submit"])){
        
        
       $csv = $db->getCSV($select_sql);
        
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=data.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print $csv;
    }
?>
