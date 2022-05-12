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

$whereClause = " requestId>0";

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page


if(isset($_POST["applicantName"]) && !empty($_POST["applicantName"])){
    $applicantName = $_POST["applicantName"];
    $whereClause .= " AND name LIKE '%$applicantName%'";
}

if(isset($_POST["regNo"]) && !empty($_POST["regNo"])){
    $regNo = $form->label("Reg No.")->post("regNo")->required()->validate();
    $whereClause .= " AND regNo LIKE '%$regNo%'";
}

if(isset($_POST["regYear"]) && !empty($_POST["regYear"])){
    $regYear = $form->title("Reg Year")->post("regYear")->required()->validate();
    $whereClause .= " AND regYear LIKE '%$regYear%'";
}

$column = array('','registrationId','regNo', 'regYear', 'name','fatherName','seniorAdvocateName','pupilageContractDate','applicantType','hasApproved');

$sql = "SELECT requestId,registrationId,  regNo, regYear, `name`, fatherName, seniorAdvocateName, pupilageContractDate,applicantType,hasApproved FROM lc_enrolment_registrations_update_request WHERE $whereClause";

$count = "SELECT COUNT(requestId) AS rowCount FROM lc_enrolment_registrations_update_request WHERE $whereClause";

$number_filter_row = $db->select($count);
$number_filter_row = ($number_filter_row[0])->rowCount;

$totalRows = $db->select("SELECT COUNT(requestId) AS totalRows FROM lc_enrolment_registrations_update_request");

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
    $sql .= " ORDER BY requestId DESC LIMIT $row,$rowperpage";
}

$registrationUpdateDataLists = $db->select($sql);
$data = array();
$today = $clock->toDate("now");

foreach ($registrationUpdateDataLists as $registrationUpdateDataList) {
    $eon = "";
    switch ($registrationUpdateDataList->hasApproved) {
        case 'pending':
            $eon = "pending";
            break;
        case 'approved':
            $eon = "approved";
            break;
        case 'declined':
            $eon = "declined";
            break;
        default:
            break;
    }

    $registrationUpdateData = array();
    $registrationUpdateData["requestId"] = $endecryptor->encrypt($registrationUpdateDataList->requestId);
    $registrationUpdateData["registrationId"] = $registrationUpdateDataList->registrationId;
    $registrationUpdateData["regNo"] = $registrationUpdateDataList->regNo;
    $registrationUpdateData["regYear"] = $registrationUpdateDataList->regYear;
    $registrationUpdateData["name"] = $registrationUpdateDataList->name;
    $registrationUpdateData["fatherName"] = $registrationUpdateDataList->fatherName;
    $registrationUpdateData["seniorAdvocateName"] = $registrationUpdateDataList->seniorAdvocateName;
    $registrationUpdateData["pupilageContractDate"] = $clock->toString($registrationUpdateDataList->pupilageContractDate, DatetimeFormat::BdDate());
    $registrationUpdateData["hasApproved"] = $registrationUpdateDataList->hasApproved;
    $registrationUpdateData["eon"] = $eon; //period/time/age
    $data[] = $registrationUpdateData;
}


$recordsTotal =1;
$recordsFiltered = 1;
$output = array(
    "draw"            => intval($_POST["draw"]),
    "recordsTotal"    => intval( $totalRows ),
    "recordsFiltered" => intval( $number_filter_row ),
    "data"            => $data
);

echo json_encode($output);
?>
