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

$whereClause = " R.registrationId>0";

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page


if(isset($_POST["applicantName"]) && !empty($_POST["applicantName"])){
    $applicantName = $_POST["applicantName"];
    $whereClause .= " AND R.`name` = '$applicantName'";
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
    R.registrationId,  R.regNo, R.regYear, R.`name`, R.fatherName, R.barName, R.seniorAdvocateName, 
    R.pupilageContractDate, R.applicantType, R.hasBarAtLaw, C.cinfoId 
    FROM lc_enrolment_registrations AS R
    LEFT JOIN lc_enrolment_cinfo C on R.registrationId = C.registrationId
*/
// $column = array('','applicantName', 'meetingDate', 'meetingapplicantType');
$column = array('','regNo', 'regYear', 'name','fatherName','barName','seniorAdvocateName','pupilageContractDate','applicantType','hasBarAtLaw');

// $sql = "SELECT meetingId, applicantName, meetingDate, startTime, endTime, meetingapplicantType FROM meetings WHERE $whereClause";
$sql = "SELECT 
        registrationId,  regNo, regYear, `name`, fatherName, barName, seniorAdvocateName, pupilageContractDate, applicantType, hasBarAtLaw 
        FROM lc_enrolment_registrations 
        WHERE $whereClause";

// $count = "SELECT COUNT(meetingId) AS rowCount FROM meetings WHERE $whereClause";
$count = "SELECT COUNT(registrationId) AS rowCount FROM lc_enrolment_registrations WHERE $whereClause";

$number_filter_row = $db->select($count);
$number_filter_row = ($number_filter_row[0])->rowCount;

// $totalRows = $db->select("SELECT COUNT(meetingId) AS totalRows FROM meetings WHERE organizationId=1");
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
    // $sql .= " ORDER BY meetingDate DESC, startTime ASC LIMIT $row,$rowperpage";
    $sql .= " ORDER BY regNo ASC, regYear ASC LIMIT $row,$rowperpage";
}

$meetings = $db->select($sql);
$data = array();
$today = $clock->toDate("now");

foreach ($meetings as $meeting) {
    $eon = "";
    switch ($meeting->applicantType) {
        case 'Regular':
            $eon = "reg";
            break;
        case 'Re-appeared':
            $eon = "reapp";
            break;
        case 'Re-Registration':
            $eon = "rereg";
            break;
        default:
            break;
    }

    $sub_array = array();
    $sub_array["registrationId"] = $endecryptor->encrypt($meeting->registrationId);
    $sub_array["regNo"] = $meeting->regNo;
    $sub_array["regYear"] = $meeting->regYear;
    $sub_array["name"] = $meeting->name;
    $sub_array["fatherName"] = $meeting->fatherName;
    $sub_array["barName"] = $meeting->barName;
    $sub_array["seniorAdvocateName"] = $meeting->seniorAdvocateName;
    $sub_array["pupilageContractDate"] = $clock->toString($meeting->pupilageContractDate, DatetimeFormat::BdDate());
    $sub_array["applicantType"] = $meeting->applicantType;
    $sub_array["hasBarAtLaw"] = $meeting->hasBarAtLaw == 1 ? "Yes" : "No";
    $sub_array["eon"] = $eon; //period/time/age
    $data[] = $sub_array;
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
