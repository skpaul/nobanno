<?php

declare(strict_types=1);

#region imports
require_once('../../../../Required.php');

Required::Logger()
    ->Database()->Clock()->HttpHeader()    ;
#endregion

#region declarations
$logger = new Logger(ROOT_DIRECTORY);
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$clock = new Clock();
#endregion


$db->connect();
$db->fetchAsObject();

include_once("xlsxwriter.class.php");

// $writer->writeSheetHeader('Sheet1', $rowdata = array(300,234,456,789), $col_options = ['widths'=>[10,20,30,40]] );

$writer = new XLSXWriter();
$stylesHeader = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#538ed5', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,top,bottom', 'border-style'=>'medium', 'widths'=>[10,10,10,30,30,30,30,20,20,30,30,15,35]);

$stylesRow = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#8db4e3', 'halign'=>'center', 'valign'=>'center', 'border'=>'bottom', 'border-style'=>'thin', 'widths'=>[10,10,10,30,30,30,30,20,20,30,30,15,35], 'height'=>20);


$styles2 = array( ['font-size'=>6],['font-size'=>8],['font-size'=>10],['font-size'=>16] );
$styles3 = array( ['font'=>'Arial'],['font'=>'Courier New'],['font'=>'Times New Roman'],['font'=>'Comic Sans MS']);
$styles4 = array( ['font-style'=>'bold'],['font-style'=>'italic'],['font-style'=>'underline'],['font-style'=>'strikethrough']);
$styles5 = array( ['color'=>'#f00'],['color'=>'#0f0'],['color'=>'#00f'],['color'=>'#666']);
$styles6 = array( ['fill'=>'#ffc'],['fill'=>'#fcf'],['fill'=>'#ccf'],['fill'=>'#cff']);
$styles7 = array( 'border'=>'left,right,top,bottom');
$styles8 = array( ['halign'=>'left'],['halign'=>'right'],['halign'=>'center'],['halign'=>'none']);
$styles9 = array( array(),['border'=>'left,top,bottom'],['border'=>'top,bottom'],['border'=>'top,bottom,right']);

$header = array(
    'Request ID'=>'string',
    'Reg No.'=>'string',
    'Year'=>'string',
    'Name (Old)'=>'string',
    'Name (New)'=>'string',
    'Father (Old)'=>'string',
    'Father (New)'=>'string',
    'Contract Date (Old)'=>'string',
    'Contract Date (New)'=>'string',
    'Adv. Name (Old)'=>'string',
    'Adv. Name (New)'=>'string',
    'Status'=>'string',
    'Submitted On'=>'string',
  );


$writer->writeSheetHeader('Sheet1', $header, $stylesHeader );

$sql = "SELECT
            R.requestId,
            C.regNo, 
            C.regYear, 
            C.fullName , 
            R.`name`, 
            C.fatherName AS fatherOld, 
            R.fatherName AS fatherNew, 
            C.pupilageContractDate as oldPupilageDate, 
            R.pupilageContractDate as newPupilageDate, 
            C.seniorAdvocateName as oldSeniorName, 
            R.seniorAdvocateName as newSeniorName, 
            R.hasApproved, 
            R.submitDatetime
        FROM
            lc_enrolment_cinfo AS C
        INNER JOIN
            lc_enrolment_registrations_update_request AS R
        ON 
                C.cinfoId = R.cinfoId
        WHERE R.hasApproved = 'declined'";

$dataList = $db->select($sql);

foreach ($dataList as $dataItem) {
    $dataRow =[];
    $dataRow[] = $dataItem->requestId;
    $dataRow[] = $dataItem->regNo;
    $dataRow[] = $dataItem->regYear;
    $dataRow[] = $dataItem->fullName;
    $dataRow[] = $dataItem->name;
    $dataRow[] = $dataItem->fatherOld;
    $dataRow[] = $dataItem->fatherNew;
    $dataRow[] = $clock->toString($dataItem->oldPupilageDate, DatetimeFormat::BdDate());
    $dataRow[] = $clock->toString($dataItem->newPupilageDate, DatetimeFormat::BdDate());
    
    $dataRow[] = $dataItem->oldSeniorName;
    $dataRow[] = $dataItem->newSeniorName;
    $dataRow[] = $dataItem->hasApproved;
    $dataRow[] = $clock->toString($dataItem->submitDatetime, DatetimeFormat::BdDatetime());

    $writer->writeSheetRow('Sheet1', $rowdata = $dataRow, $stylesRow );
}


// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles2 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles3 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles4 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles5 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles6 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles7 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles8 );
// $writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $styles9 );
$writer->writeToFile('declined.xlsx');

HttpHeader::redirect(BASE_URL. "/admins/registration-update-request/read/list/declined.xlsx")

?>
