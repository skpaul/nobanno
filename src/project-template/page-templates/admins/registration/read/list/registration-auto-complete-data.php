<?php
    
#region imports
require_once('../../../../Required.php');

Required::Logger()
    ->Database()->EnDecryptor()->HttpHeader()->Clock() ->Validable();
#endregion

#region declarations
$logger = new Logger(ROOT_DIRECTORY);
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$endecryptor = new EnDecryptor();
$clock = new Clock();
#endregion


$db->connect();
// $db->fetchAsObject();













    
    $queryString = $_SERVER['QUERY_STRING'];

    $term = $_GET["term"]; //comes from autocomplete plugin

    $columnName = "name";
    $sql = "select distinct  `$columnName` from lc_enrolment_registrations where  `$columnName` like '%$term%' order by  `$columnName` limit 10";
    // $lists = $db->distinct($columnName)->from("meetings")->where($columnName)->like("%$term%")->orderBy($columnName)->take(10)->fetchArray()->toList();
    $lists = $db->select($sql);

    $data = [];
    foreach ($lists as $item) 
    $data[] = $item[$columnName];

    $json = json_encode($data);
    $db->close();
    exit($json);

?>



