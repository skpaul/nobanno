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

$whereClause = " id>0";

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page


if(isset($_POST["venue"]) && !empty($_POST["venue"])){
    $venue = $_POST["venue"];
    $whereClause .= " AND venue LIKE '%$venue%'";
}

if(isset($_POST["building"]) && !empty($_POST["building"])){
    $building = $form->label("Building")->post("building")->required()->validate();
    $whereClause .= " AND  building LIKE '%$building%'";
}

if(isset($_POST["floor"]) && !empty($_POST["floor"])){
    $floor = $form->title("Floor")->post("floor")->required()->validate();
    $whereClause .= " AND  floor LIKE '%$floor%'";
}

if(isset($_POST["room"]) && !empty($_POST["room"])){
    $room = $form->title("Room")->post("room")->required()->validate();
    $whereClause .= " AND room LIKE '%$room%'";
}


// $column = array('','applicantName', 'meetingDate', 'meetingapplicantType');
$column = array('','venue', 'building', 'floor','room','start_roll','end_roll','total');

// $sql = "SELECT meetingId, applicantName, meetingDate, startTime, endTime, meetingapplicantType FROM meetings WHERE $whereClause";
$sql = "SELECT id,  venue, building, `floor`, room, start_roll, end_roll, total FROM lc_enrolment_seat_plans WHERE $whereClause";

// $count = "SELECT COUNT(meetingId) AS rowCount FROM meetings WHERE $whereClause";
$count = "SELECT COUNT(id) AS rowCount FROM lc_enrolment_seat_plans WHERE $whereClause";

$number_filter_row = $db->select($count);
$number_filter_row = ($number_filter_row[0])->rowCount;

// $totalRows = $db->select("SELECT COUNT(meetingId) AS totalRows FROM meetings WHERE organizationId=1");
$totalRows = $db->select("SELECT COUNT(id) AS totalRows FROM lc_enrolment_seat_plans");

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
    $sql .= " ORDER BY id ASC LIMIT $row,$rowperpage";
}

$meetings = $db->select($sql);
$data = array();
$today = $clock->toDate("now");

foreach ($meetings as $meeting) {
    /*$eon = "";
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
    }*/

    $sub_array = array();
    $sub_array["id"] = $endecryptor->encrypt($meeting->id);
    $sub_array["venue"] = $meeting->venue;
    $sub_array["building"] = $meeting->building;
    $sub_array["floor"] = $meeting->floor;
    $sub_array["room"] = $meeting->room;
    $sub_array["start_roll"] = $meeting->start_roll;
    $sub_array["end_roll"] = $meeting->end_roll;
   // $sub_array["pupilageContractDate"] = $clock->toString($meeting->pupilageContractDate, DatetimeFormat::BdDate());
    //$sub_array["applicantType"] = $meeting->applicantType;
    $sub_array["total"] = $meeting->total;
    //$sub_array["eon"] = $eon; //period/time/age
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
