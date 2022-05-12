<?php 

    require_once("../Required.php");
    Required::SwiftLogger()
                
                ->ZeroSQL()
                ->Validable()
                ->SwiftDatetime()
                ->EnDecryptor();


    $logger = new SwiftLogger(ROOT_DIRECTORY,true);
    $endecryptor = new EnDecryptor();
    $db = new ZeroSQL();
    $db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME);
    $db->connect();
    
  
    $districtCode = $_GET["district_code"];

    $thanaList = $db->select('thana_code, thana_name')
                    ->from('div_dist_thana_list')
                    ->where('district_code')->equalTo($districtCode)
                    ->orderBy('thana_name')
                    ->execute();

    $thanaas = json_encode($thanaList);
    echo '{"issuccess":true,"data":'. $thanaas .'}';
    exit;

?>
