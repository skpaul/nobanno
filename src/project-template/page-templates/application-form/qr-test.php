<?php



$userid = "1010";


//QR Code starts --->
//set it to writable location, a place for temp generated PNG files
$PNG_TEMP_DIR = dirname(__FILE__).'/qr_code_images/';
    
//html PNG location prefix
$PNG_WEB_DIR = 'qr_code_images/';

include "phpqrcode/qrlib.php";  

//ofcourse we need rights to create temp dir
if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);
        
//processing form input
//remember to sanitize user input in real-life solution !!!
$errorCorrectionLevel = 'L';
$errorCorrectionLevel ='H'; // array('L','M','Q','H')    

$matrixPointSize = 4;
$filename = $PNG_TEMP_DIR. $userid .'.png';
QRcode::png("forkan", $filename, $errorCorrectionLevel,  10, 1);  

//QRtools::timeBenchmark();      // benchmark
//QR Code ends <--


?>
