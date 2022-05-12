<?php 

    #region Session
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["admin"])) {
            die("Please login");
        }

        if($_SESSION["admin"] == false){
            die("Please login");
        }
    #endregion


    #region imports
        require_once('../../../Required.php');
        Required::Logger()->Database()->EnDecryptor()->CSV();
        #endregion

        #region declarations
        $logger = new Logger(ROOT_DIRECTORY);
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $endecryptor = new EnDecryptor();
    #endregion


    $db->connect();
    $db->fetchAsObject();

    $sql = "SELECT * FROM lc_enrolment_registrations";
    $csv = CSV::getCSV($sql, DB_SERVER,DB_USER,DB_PASSWORD, DB_NAME); 
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=registrations.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print $csv;

?>

