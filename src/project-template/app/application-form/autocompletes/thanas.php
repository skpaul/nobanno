<?php 
require_once("../Required.php");

Required::SwiftLogger()->ZeroSQL(2)->SwiftJSON()->Validable()->Helpers();

$db = new ZeroSQL();
$db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME);
$db->connect();
  
$queryString = $_SERVER['QUERY_STRING'];

$term = $_GET["term"];
$districtName = $_GET["district"];
$lists= $db->select("thana_name")->from("thanas")->where("district_name")->equalTo($districtName)->andWhere("thana_name")->like("%$term%")-> orderBy("thana_name")->fetchAssoc()->toList();

$data = [];
foreach ($lists as $item) 
   $data[] = $item["thana_name"];

$json = json_encode($data);
$db->close();
exit($json);
?>
