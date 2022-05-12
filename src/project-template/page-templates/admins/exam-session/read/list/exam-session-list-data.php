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
$db->fetchAsObject();

// $sid = $_GET["sid"];


$form = new Validable();

$whereClause = " configId>0";

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page


if(isset($_POST["applicationType"]) && !empty($_POST["applicationType"])){
    $applicationType = $_POST["applicationType"];
    $whereClause .= " AND applicationType LIKE '%$applicationType%'";
}


$column = array('','court', 'applicationType', 'isActive','applicationStartDatetime','applicationEndDatetime','admitCardStartDatetime','admitCardEndDatetime');


$sql = "SELECT configId,  court, applicationType, `isActive`, applicationStartDatetime, applicationEndDatetime, admitCardStartDatetime, admitCardEndDatetime FROM post_configurations WHERE $whereClause";


$count = "SELECT COUNT(configId) AS rowCount FROM post_configurations WHERE $whereClause";

$number_filter_row = $db->select($count);
$number_filter_row = ($number_filter_row[0])->rowCount;

$totalRows = $db->select("SELECT COUNT(configId) AS totalRows FROM post_configurations");

$totalRows = $totalRows[0]->totalRows;



$postOrder = $_POST['order'];
$orderByColumn = $_POST['order']['0']['column']; // Column index
$orderByColumn = intval($orderByColumn);
$orderByColumn = $column[$orderByColumn];
$orderByDirection = $_POST['order']['0']['dir'];

if(!empty($orderByColumn))
{
    $sql .= " ORDER BY $orderByColumn $orderByDirection LIMIT $row,$rowperpage";
}
else
{
    $sql .= " ORDER BY configId ASC LIMIT $row,$rowperpage";
}

$meetings = $db->select($sql);
$data = array();


foreach ($meetings as $meeting) {
    $configData = array();
    $empty_date_format= array("",null,"0000-00-00 00:00:00");
    $configData["configId"] = $endecryptor->encrypt($meeting->configId);
    $configData["court"] = $meeting->court;
    $configData["applicationType"] = $meeting->applicationType;
    $configData["isActive"] = $meeting->isActive==1? "Yes":"No";
    $application_start=trim(strval($meeting->applicationStartDatetime));
    $configData["applicationStartDatetime"] = in_array($application_start, $empty_date_format) ? "Not Started Yet" : date("d-m-Y h:i A",strtotime($application_start));
    $application_end=trim(strval($meeting->applicationEndDatetime));
    $configData["applicationEndDatetime"] = in_array($application_end, $empty_date_format) ? "Not Started Yet" : date("d-m-Y h:i A",strtotime($application_end));
    $application_card_start=trim(strval($meeting->admitCardStartDatetime));
    $configData["admitCardStartDatetime"] = in_array($application_card_start, $empty_date_format) ? "Not Started Yet":date("d-m-Y h:i A",strtotime($application_card_start));
    $application_card_end=trim(strval($meeting->admitCardEndDatetime));
    $configData["admitCardEndDatetime"] = in_array($application_card_end, $empty_date_format) ? "Not Started Yet":date("d-m-Y h:i A",strtotime($application_card_end));
    $data[] = $configData;
}


$recordsTotal =1;
$recordsFiltered = 1;
$output = array(
    "draw"            => intval($_POST["draw"]),
    "recordsTotal"    => intval( $totalRows ),
    "recordsFiltered" => intval( $number_filter_row ),
    "data"            => $data
);

// HttpHeader::setJson();
echo json_encode($output);
?>
