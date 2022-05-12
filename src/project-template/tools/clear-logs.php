<?php 
    require_once("../Required.php");
    Required::Logger();

    require_once("prevent_access_if_not_localhost.php");
    
    $logger = new Logger(ROOT_DIRECTORY);
    $logger->clearLogs();

    $queryString = $_SERVER['QUERY_STRING'];
    header('Location:read-logs.php?'.$queryString, true, 303);
?>
