<?php 
require_once("../Required.php");

Required::Logger()->Database()->JSON();

$logger = new Logger(ROOT_DIRECTORY);
$json = new JSON();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$db->connect();
$db->fetchAsAssoc();

$term = $_GET["term"];
// $lists = $db->select("name")->from("countries")->where("name")->like("%$term%")->andWhere("isActive")->equalTo("yes")->orderBy("name")->take(10)->fetchAssoc()->toList();
$sql = "SELECT `name` FROM countries WHERE `name` LIKE '%$term%' AND isActive='yes' ORDER BY `name` LIMIT 10";
$lists = $db->select($sql);
$db->backToPrevFetchStyle();
$data = [];
foreach ($lists as $item) {
   $data[] = $item["name"];
}

$thanaas = json_encode($data);
$db->close();
echo $thanaas;
exit;
?>

