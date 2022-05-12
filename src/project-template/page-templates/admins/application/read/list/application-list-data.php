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

$whereClause = " cinfoId>0";

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page


if(isset($_POST["userId"]) && !empty($_POST["userId"])){
    $userId = $_POST["userId"];
    $whereClause .= " AND c.userId = '$userId'";
}

if(isset($_POST["regNo"]) && !empty($_POST["regNo"])){
    $regNo = $form->label("Reg No.")->post("regNo")->required()->validate();
    $whereClause .= " AND c.regNo = '$regNo'";
}

if(isset($_POST["regYear"]) && !empty($_POST["regYear"])){
    $regYear = $form->title("Reg Year")->post("regYear")->required()->validate();
    $whereClause .= " AND c.regYear ='$regYear'";
}

if(isset($_POST["applicantType"]) && !empty($_POST["applicantType"])){
    $applicantType = $_POST["applicantType"];
    $whereClause .= " AND r.applicantType LIKE '%$applicantType%'";
}

/*
SELECT
	c.userId, 
	c.regNo, 
	c.regYear, 
	c.fullName, 
	c.fatherName, 
	c.requiredFee, 
	c.fee, 
	c.appliedDatetime, 
	r.applicantType, 
	r.isReRegistered, 
	r.hasBarAtLaw, 
	c.cinfoId, 
	c.registrationId
FROM
	lc_enrolment_registrations AS r
	INNER JOIN
	lc_enrolment_cinfo AS c
	ON 
    r.registrationId = c.registrationId

*/

$column = array('','userId','regNo', 'regYear', 'fullName','fatherName','requiredFee','fee','applicantType','isReRegistered','hasBarAtLaw','appliedDatetime');

// $sql = "SELECT registrationId,  regNo, regYear, `name`, fatherName, seniorAdvocateName, pupilageContractDate, applicantType, isReRegistered, hasBarAtLaw FROM lc_enrolment_registrations WHERE $whereClause";

$sql = "SELECT
            c.userId, 
            c.regNo, 
            c.regYear, 
            c.fullName, 
            c.fatherName, 
            c.requiredFee, 
            c.fee, 
            r.applicantType, 
            r.isReRegistered, 
            r.hasBarAtLaw, 
            c.cinfoId, 
            c.registrationId,
            c.appliedDatetime 
        FROM
            lc_enrolment_registrations AS r
        INNER JOIN
            lc_enrolment_cinfo AS c
        ON 
            r.registrationId = c.registrationId 
        WHERE $whereClause";

// $count = "SELECT COUNT(registrationId) AS rowCount FROM lc_enrolment_registrations WHERE $whereClause";
$count = "SELECT
            COUNT(c.cinfoId)  AS rowCount
            FROM
            lc_enrolment_registrations AS r
            INNER JOIN
            lc_enrolment_cinfo AS c
            ON 
            r.registrationId = c.registrationId 
            WHERE $whereClause";



$number_filter_row = $db->select($count);
$number_filter_row = ($number_filter_row[0])->rowCount;

// $totalRows = $db->select("SELECT COUNT(regDataId) AS totalRows FROM regDatas WHERE organizationId=1");
$totalRows = $db->select("SELECT COUNT(cinfoId) AS totalRows FROM lc_enrolment_cinfo");

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
    $sql .= " ORDER BY c.regNo ASC, c.regYear ASC LIMIT $row,$rowperpage";
}

$dataLists = $db->select($sql);
$returnDataList = array();
$today = $clock->toDate("now");

foreach ($dataLists as $data) {
    $eon = "";
    switch ($data->fee) {
        case '1':
            $eon = "paid";
            break;
        case '0':
            $eon = "unpaid";
            break;
        default:
            break;
    }

    $returnData = array();
    $returnData["userId"] = $data->userId;
    $returnData["regNo"] = $data->regNo;
    $returnData["regYear"] = $data->regYear;
    $returnData["fullName"] = $data->fullName;
    $returnData["fatherName"] = $data->fatherName;
    $returnData["applicantType"] = $data->applicantType;
    $returnData["requiredFee"] = $data->requiredFee;
    $returnData["fee"] = $data->fee == 1 ? "Yes" : "No";
    $returnData["appliedDatetime"] = $clock->toString($data->appliedDatetime, DatetimeFormat::BdDatetime());
    $returnData["applicantType"] = $data->applicantType;
    $returnData["isReRegistered"] = $data->isReRegistered == 1 ? "Yes" : "No";
    $returnData["hasBarAtLaw"] = $data->hasBarAtLaw == 1 ? "Yes" : "No";
    $returnData["cinfoId"] = $endecryptor->encrypt($data->cinfoId);
    $returnData["registrationId"] = $endecryptor->encrypt($data->registrationId);
    $returnData["eon"] = $eon; //period/time/age
    $returnDataList[] = $returnData;
}

$recordsTotal =1;
$recordsFiltered = 1;
$output = array(
    "draw"            => intval($_POST["draw"]),
    "recordsTotal"    => intval( $totalRows ),
    "recordsFiltered" => intval( $number_filter_row ),
    "data"            => $returnDataList
);

// HttpHeader::setJson();
echo json_encode($output);
?>
