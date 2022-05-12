<?php 
    require_once('../Required.php');
    Required::Logger()->Database()->EnDecryptor()   ;
    
    $logger = new Logger(ROOT_DIRECTORY);
    // $endecryptor = new EnDecryptor();
    // $json = new JSON();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    // $clock = new Clock();
    
    $db->connect();
    $db->fetchAsObject();

?>

<?php
    

    $start_date = new DateTime($_GET["start"], new DateTimeZone("Asia/Dhaka")); //->format("Y-m-d"); // FormatDatetime($_GET["start"], "Y-m-d");;
    $start_date = $start_date->format('Y-m-d');

    $end_date = new DateTime($_GET["end"], new DateTimeZone("Asia/Dhaka")); // FormatDatetime($_GET["end"], "Y-m-d"); 
    $end_date = $end_date->format('Y-m-d');

    $sql = "SELECT CAST(appliedDatetime AS DATE) AS appliedDate, count(*) as appliedQuantity FROM `lc_enrolment_cinfo` WHERE fee=1 GROUP by appliedDate HAVING appliedDate BETWEEN '$start_date' and '$end_date' order by appliedDate";
    $applications = $db->select($sql);

    // $sql = "SELECT * FROM `bank_transactions` WHERE transaction_date BETWEEN '$start_date' and '$end_date'  order by transaction_id desc";
    // $rows = sql_select_many($sql,$connection)["rows"];

    $string = "";
    $id = 1;
    foreach($applications as $row){
        // $transaction_id = $row->transaction_id;
        // $bank_account_name = $row->bank_account_name;
        // $deposited_amount = $row->deposited_amount;
        // $withdrawn_amount = $row->withdrawn_amount;

        // $data = "$bank_account_name,";
        // if($deposited_amount>0.00){
        //     $data .= " +" . $deposited_amount .",";
        // }
        // if($withdrawn_amount > 0.00){
        //     $data .= " -" . $withdrawn_amount;
        // }

        $data = "Application: " . $row->appliedQuantity;
        $date = $row->appliedDate;
        if(empty($string)){
            $string = "{\"id\": \"$id\",\"title\": \"$data\", \"start\": \"$date\"}";
        }
        else{
            $string .= ",{\"id\": \"$id\",\"title\": \"$data\", \"start\": \"$date\"}";
        }
    }
    header('Content-type: application/json');
    // echo "[{\"id\": \"2\", \"title\": \"BOom\", \"start\": \"2019-08-29\", \"url\":\"http://www.google.com\" }]";
    echo "[$string]";
    exit;
?>