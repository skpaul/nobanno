<?php 
require_once("../Required.php");

Required::Logger()->Database()->JSON();

$logger = new Logger(ROOT_DIRECTORY);
$json = new JSON();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$db->connect();
$db->fetchAsAssoc();
  
$queryString = $_SERVER['QUERY_STRING'];

$term = $_GET["term"];
// $lists = $db->select("name")->from("board_and_university_list")->where("name")->like("%$term%")->orderBy("name")->take(10)->fetchAssoc()->toList();

// $sql = "SELECT `name` FROM board_and_university_list WHERE `name` LIKE '%:term%' ORDER BY `name` LIMIT 10";
$sql = "SELECT `name` FROM board_and_university_list WHERE `name` LIKE concat('%', :term, '%') ORDER BY `name` LIMIT 10";
$lists = $db->select($sql, array("term"=>$term));
$db->backToPrevFetchStyle();

$data = [];
foreach ($lists as $item) {
   $data[] = $item["name"];
}

$universities = json_encode($data);
$db->close();
echo $universities;
exit;
?>

