<?php 
require_once("../Required.php");

Required::SwiftLogger()->ZeroSQL(2)->SwiftJSON()->Validable()->Helpers();

$db = new ZeroSQL();
$db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME);
$db->connect();
  
$queryString = $_SERVER['QUERY_STRING'];

$term = $_GET["term"];
$lists = $db->select("name")->from("subjects")->where("name")->like("%$term%")->orderBy("name")->take(10)->fetchAssoc()->toList();

$data = [];
foreach ($lists as $item) 
   $data[] = $item["name"];


$json = json_encode($data);
$db->close();
exit($json);
?>

