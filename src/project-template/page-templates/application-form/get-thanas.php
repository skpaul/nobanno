<?php 
require_once("../Required.php");



Required::Logger()
->Database()
->JSON();

$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$db->connect();
  
$district = $_GET["district"];
$sql = "SELECT thana_name FROM thanas WHERE district_name=:district_name ORDER BY thana_name";
$thanas= $db->select($sql, array("district_name"=>$district));
$thanaas = json_encode($thanas);
$db->close();
echo '{"issuccess":true,"data":'. $thanaas .'}';
exit;
?>
