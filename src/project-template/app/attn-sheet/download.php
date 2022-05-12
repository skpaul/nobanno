<?php
require_once("../../../Required.php");
require_once('../../../vendor/autoload.php');

Required::SwiftLogger()
->SessionBase()
->ZeroSQL(2)
->SwiftDatetime()
->EnDecryptor()
->JSON()
->Validable();

// 1px = 0.2645833333mm

$db = new ZeroSQL();
$db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME);
$db->connect();

$postConfig = $db->select("reference")
    ->from("post_configurations")
    ->where("courtType")->equalTo("Higher")
    ->andWhere("examType")->equalTo("Written")
    ->singleOrNull();

$start_roll = trim($_POST["start_roll"]);
$end_roll = trim($_POST["end_roll"]);

//Create table `attn_sheet_for_mandatory_subjects` SELECT * FROM `attn_sheet_data`

$query = "SELECT
            rollNo, 
            userId,
            fullName, 
            fatherName, 
            dob
        FROM
            final_data_to_handover_2021_09_17_for_correction
        WHERE
            rollNo BETWEEN $start_roll and $end_roll
        ORDER BY
        rollNo ASC";

$applicants = $db->select($query)->fromSQL()->toList();

$query = "SELECT `venue`, building,`floor`, `room`, total FROM `exam_venue` WHERE `start_roll`=$start_roll AND `end_roll`= $end_roll";

$exam_venue = $db->select($query)->fromSQL()->fetchAssoc()->singleOrNull();

try {
$mpdfConfig = array(
                'mode' => 'utf-8', 
                'format' => 'A4',
                // 'margin_header' => 0,     // 30mm not pixel
                // 'margin_top' => 35,     // 30mm not pixel
                'margin_footer' => 10,     // 10mm
                'orientation' => 'P'    
            );
$mpdf = new \Mpdf\Mpdf($mpdfConfig);
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

$stylesheet1 = file_get_contents('css/pdf-style.css');

$mpdf->WriteHTML($stylesheet1,1);

$mpdf->setFooter("Page {PAGENO} of {nb}");

// $mpdf->SetHeader('Document Title');

// $mpdf->SetHTMLHeader('
// <div class="page-header-container">
//     <div class="page-header-logo">
//     	<img style="" src="http://localhost/bjsc/applicant-images/photos/1.jpg" alt="">
//     </div>
//     <div style="width: 880px;">
//     	<p>Government of the Peoples Republic of Bangladesh</p>
//     </div>
// </div>');

?>
    	

<?php
    $vanue = $exam_venue["venue"];
    $building = $exam_venue["building"];
    $floor = $exam_venue["floor"];
    $room = $exam_venue["room"];
    $totalSeats = $exam_venue["total"];
    $logoSource = BASE_URL."/assets/images/bar-logo.png";
    $htmlHeader = <<<HTML
    <table style="width:100%;">
        <tr>
            <td style="width:10%; text-align:right;">
                <img style="height:50px; padding: 2px;" src="$logoSource" alt="">
            </td>
            <td style="text-align:center;">
                <span style="font-size:13px; text-align:center;">Government of the People's Republic of Bangladesh</span><br>
                <span style="font-size:18px; text-align:center;">Bangladesh Bar Council</span><br>
            </td>
        </tr>

        <tr>
            <td>
            </td>
            <td style="text-align:center;">
                <span style="padding-left:50px; text-align:center;">Attendance Information</span>
            </td>
        </tr>
    </table>
    <br>
    <table class="center-details">
        <tr>
            <td>Circular No.</td>
            <td>: $postConfigNo</td>
        </tr>
        <tr>
            <td>Centre</td>
            <td>: $vanue</td>
        </tr>
        <tr>
            <td>Details</td>
            <td>: Building- $building,  &nbsp;&nbsp;Fl- $floor, &nbsp;&nbsp; Room- $room, &nbsp;&nbsp; Total Seats: $totalSeats</td>
        </tr>
    </table>
    HTML;

    // $mpdf->WriteHTML($htmlHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);

    $htmlTableStart = <<<HTML
        <div class="body-container">
        <table style="width:100%; border-collapse: collapse;">
    HTML;
    $htmlTableEnd = <<<HTML
        </table>
        </div>
    HTML;
    $htmlTableHeader = <<<HTML
        <tr style="background-color: #EBE8E8;">
            <td class="table-header-td" style="width: 20px;">SL.</td>
            <td class="table-header-td" style="width: 360px;">Applicant Details</td>
            <td class="table-header-td text-center" style="width: 60px;">Photo</td>
            <td class="table-header-td text-center" style="width: 60px;">Specimen Signature</td>
            <td class="table-header-td text-center" style="width: 100px;">Signature</td>
        </tr>
    HTML;

$countItemRows = 1;
$countPageItem = 0;
$htmlTableRow="";
$countDataRows = count($applicants);
$countPages = 1;
$pageRowLimit = 11;
$rowsLeft = $countDataRows;
foreach ($applicants as $result) {
	$photoSource = BASE_URL."/applicant-images/higher-court/written-exam/photos/".$result->userId.".jpg";
	$signatureSource = BASE_URL."/applicant-images/higher-court/written-exam/signatures/".$result->userId.".jpg";
    $dateOfBirth = (new DateTime($result->dob, new DateTimeZone('Asia/Dhaka')))->format("d-m-Y");
    $htmlTableRow .= <<<HTML
            <tr class="table-row">
                <td class="table-row-td">$countItemRows</td>
                <td class="table-row-td">
                    <table style="border-collapse: collapse;">
                        <tr><td>Roll No.</td><td>:</td><td>$result->rollNo</td></tr>
                        <tr><td>Name</td><td>:</td><td>$result->fullName</td></tr>
                        <tr><td>Father's Name</td><td>:</td><td>$result->fatherName</td></tr>
                        <tr><td>Date of Birth</td><td>:</td><td>$dateOfBirth</td></tr>
                   </table>
                </td>
                <td class="table-row-td text-center"><img style="height:65px; padding: 2px;" src="$photoSource" alt=""></td>
                <td class="table-row-td text-center"><img style="width:140px;" src="$signatureSource"></td>
                <td class="table-row-td">&nbsp;</td>
            </tr>
    HTML;
	$countItemRows++;
	$countPageItem++;
	// $countSubjectRows++;
	
	//$countPageItem = 1,2,3,4,5,6,7,8,9,10,11,12
	//$pageRowLimit = 10
	if ($countPageItem >= $pageRowLimit) {
	    $mpdf->WriteHTML($htmlHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	    $mpdf->WriteHTML($htmlTableStart ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	    $mpdf->WriteHTML($htmlTableHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	    $mpdf->WriteHTML($htmlTableRow ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	    $mpdf->WriteHTML($htmlTableEnd ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	    
		if ($countDataRows > $countPages * $pageRowLimit) {
			$countPages++;
	    	$mpdf->AddPage();
		}

		$htmlTableRow = '';
		$countPageItem = 0;
		$rowsLeft = $rowsLeft - $pageRowLimit;
	}
}

if (strlen($htmlTableRow) > 0) {
	if ($rowsLeft == 0) {
		$mpdf->AddPage();
    	$mpdf->WriteHTML($htmlHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	}
    $mpdf->WriteHTML($htmlHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
    $mpdf->WriteHTML($htmlTableStart ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
    $mpdf->WriteHTML($htmlTableHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
    $mpdf->WriteHTML($htmlTableRow ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
    $mpdf->WriteHTML($htmlTableEnd ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
}

    $htmlSummary = <<<HTML
    	<table class="footer_table" style="margin-top:20px;">
    		<tr>
    			<td style="width:60%;">
    				Name & Signature of the Invigilator
    			</td>
    			<td>
    				No. of Examinees</td></tr>
    		<tr>
    		<td>
    		<table class="name_signature_table"><tr><td style="width:20px;">1.</td><td>
    			
    		</td>
    		</tr><tr><td>2.</td><td></td></tr>
    		<tr><td>3.</td><td></td></tr><tr><td style="border-bottom:0;">4.</td><td style="border-bottom:0;"></td></tr>
    		</table>
    		</td>
    		<td>
    		<table class="summary_table">
    		<tr>
    		<td>
    		Total :
    		</td>
    		</tr>
    		<tr>
    		<td>
    		Present :
    		</td>
    		</tr>
    		<tr>
    		<td>
    		Absent:
    		</td>
    		<td>
    		</td>
    		</tr>
    		<tr>
    		<td>
    		Remarks:
    		</td>
    		<td></td></tr></table></td>
    		</tr>
    		</table>
    HTML;

	$mpdf->AddPage();
	$mpdf->WriteHTML($htmlHeader ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
	$mpdf->WriteHTML($htmlSummary ,\Mpdf\HTMLParserMode::HTML_BODY, true, true);
    $fileName = "Attendance Sheet ($start_roll - $end_roll).pdf";
	$mpdf->Output($fileName, 'I');
    exit;
?>