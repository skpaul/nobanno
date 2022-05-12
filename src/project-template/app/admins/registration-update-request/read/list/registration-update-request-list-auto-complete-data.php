<?php
    
    require_once('../../../../Required.php');

    Required::Logger()
        ->Database()->EnDecryptor()->HttpHeader()->Clock() ->Validable();
    $logger = new Logger(ROOT_DIRECTORY);
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $endecryptor = new EnDecryptor();
    $clock = new Clock();

    $db->connect();
    $queryString = $_SERVER['QUERY_STRING'];

    $term = $_GET["term"]; //comes from autocomplete plugin
    $columnName = $_GET["column"] ; //comes from autocomplete plugin

    $sql = "select distinct  `$columnName` from lc_enrolment_registrations_update_request where `$columnName` like '%$term%' order by `$columnName` limit 10";
    $logger->createLog($sql);
    $lists = $db->select($sql);

    $data = [];
    foreach ($lists as $item) 
    $data[] = $item[$columnName];

    $json = json_encode($data);
    $db->close();
    exit($json);

?>



