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

$whereClause = " registrationId>0";

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page


if(isset($_POST["applicantName"]) && !empty($_POST["applicantName"])){
    $applicantName = $_POST["applicantName"];
    $whereClause .= " AND `name` = '$applicantName'";
}

if(isset($_POST["regNo"]) && !empty($_POST["regNo"])){
    $regNo = $form->label("Reg No.")->post("regNo")->required()->validate();
    $whereClause .= " AND regNo = '$regNo'";
}

if(isset($_POST["regYear"]) && !empty($_POST["regYear"])){
    $regYear = $form->title("Reg Year")->post("regYear")->required()->validate();
    $whereClause .= " AND regYear ='$regYear'";
}

if(isset($_POST["applicantType"]) && !empty($_POST["applicantType"])){
    $applicantType = $_POST["applicantType"];
    $whereClause .= " AND applicantType LIKE '%$applicantType%'";
}

/*
SELECT
	registrationId, 
	regNo, 
	regYear, 
	`name`, 
	fatherName, 
	barName, 
	seniorAdvocateName, 
	pupilageContractDate, 
	applicantType, 
	hasBarAtLaw
FROM
	lc_enrolment_registrations



*/
// $column = array('','applicantName', 'regDataDate', 'regDataapplicantType');
$column = array('', '','regNo', 'regYear', 'name','fatherName','seniorAdvocateName','pupilageContractDate','applicantType','isReRegistered','hasBarAtLaw');

// $sql = "SELECT regDataId, applicantName, regDataDate, startTime, endTime, regDataapplicantType FROM regDatas WHERE $whereClause";
$sql = "SELECT registrationId,  regNo, regYear, `name`, fatherName, seniorAdvocateName, pupilageContractDate, applicantType, isReRegistered, hasBarAtLaw FROM lc_enrolment_registrations WHERE $whereClause";

// $count = "SELECT COUNT(regDataId) AS rowCount FROM regDatas WHERE $whereClause";
$count = "SELECT COUNT(registrationId) AS rowCount FROM lc_enrolment_registrations WHERE $whereClause";

$number_filter_row = $db->select($count);
$number_filter_row = ($number_filter_row[0])->rowCount;

// $totalRows = $db->select("SELECT COUNT(regDataId) AS totalRows FROM regDatas WHERE organizationId=1");
$totalRows = $db->select("SELECT COUNT(registrationId) AS totalRows FROM lc_enrolment_registrations");

$totalRows = $totalRows[0]->totalRows;



$postOrder = $_POST['order'];
$orderByColumn = $_POST['order']['0']['column']; // Column index
$orderByColumn = intval($orderByColumn);
$orderByColumn = $column[$orderByColumn];
$orderByDirection = $_POST['order']['0']['dir'];

if(!empty($orderByColumn))
{
    // $sql .= " ORDER BY $orderByColumn $orderByDirection, startTime ASC LIMIT $row,$rowperpage";
    $sql .= " ORDER BY $orderByColumn $orderByDirection LIMIT $row,$rowperpage";
}
else
{
    // $sql .= " ORDER BY regDataDate DESC, startTime ASC LIMIT $row,$rowperpage";
    $sql .= " ORDER BY regNo ASC, regYear ASC LIMIT $row,$rowperpage";
}

$registrationDataLists = $db->select($sql);
$data = array();
$today = $clock->toDate("now");

foreach ($registrationDataLists as $regData) {
    $eon = "";
    switch ($regData->applicantType) {
        case 'Regular':
            $eon = "reg";
            break;
        case 'Re-appeared':
            $eon = "reapp";
            break;
        default:
            break;
    }

    $registrationData = array();
    $registrationData["registrationId"] = $endecryptor->encrypt($regData->registrationId);
    $registrationData["regNo"] = $regData->regNo;
    $registrationData["regYear"] = $regData->regYear;
    $registrationData["name"] = $regData->name;
    $registrationData["fatherName"] = $regData->fatherName;
    $registrationData["seniorAdvocateName"] = $regData->seniorAdvocateName;
    $registrationData["pupilageContractDate"] = $clock->toString($regData->pupilageContractDate, DatetimeFormat::BdDate());
    $registrationData["applicantType"] = $regData->applicantType;
    $registrationData["isReRegistered"] = $regData->isReRegistered == 1 ? "Yes" : "No";
    $registrationData["hasBarAtLaw"] = $regData->hasBarAtLaw == 1 ? "Yes" : "No";
    $registrationData["eon"] = $eon; //period/time/age
    $data[] = $registrationData;
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
