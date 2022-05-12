<?php 
    require_once("../Required.php");

    Required::Logger()
            ->Database()
            ->Clock()
            ->EnDecryptor()
            ->JSON()  
            ->ExclusivePermission()->HttpHeader()
            ->Validable();


    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $clock = new Clock();
    $json= new JSON();
    $db->connect(); $db->fetchAsObject();

    //if config id not found in GET variables, redirect to sorry page.
    if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"]))) {
        HttpHeader::redirect(BASE_URL . "/sorry.php");
    }


    $encConfigId = trim($_GET["config-id"]); //decryption has been done in the following try .. catch block for safety reason.
    $postConfigId = $endecryptor->decrypt($encConfigId);

    $hasExclusivePermission = ExclusivePermission::hasPermission();
    $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";

    $configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $postConfigId));
    
    //whether exclusive permission exists or not, the post configuration must exist.
    if (count($configs) != 1) die($json->fail()->message("Invalid request.")->create() );

    $postConfig = $configs[0];
    
    //admitCardStartDatetime = Null means admit card not available
    if(!isset($postConfig->admitCardStartDatetime) || empty($postConfig->admitCardStartDatetime)){
        die($json->fail()->message("Admit card not yet available to download. Please check back later.")->create() );
    }
    else{
        //Check the datetime limitations------>
        if(isset($postConfig->admitCardStartDatetime) && !empty($postConfig->admitCardStartDatetime) && isset($postConfig->admitCardEndDatetime) && !empty($postConfig->admitCardEndDatetime)                            ){
            $admitStart = $clock->toDate($postConfig->admitCardStartDatetime);
            $admitEnd = $clock->toDate($postConfig->admitCardEndDatetime);
            $currentDatetime = $clock->toDate("now");

            if($currentDatetime < $admitStart){
                $message = $admitStart->format("h:i a, d-m-Y.");
                die($json->fail()->message("Admit card will be available from $message")->create() );
            }
            else{
                if($currentDatetime > $admitEnd){
                    $message = $admitEnd->format("h:i a, d-m-Y.");
                    die($json->fail()->message("Last date of downloading admit card ended on $message")->create() );
                }
            }
        }
        else{
            die($json->fail()->message("Admit card not yet available to download. Please check back later.")->create() );
        }
        //<-------Check the datetime limitations
    }

    $form = new Validable();

    try {
        $regNo = $form->label("Registration No.")->post("regNo")->required()->asInteger(false)->maxLen(10)->validate();
        $regYear = $form->label("Registration Year")->post("regYear")->required()->asInteger(false)->maxLen(4)->validate();
        
    } catch (\ValidableException $ve) {
        $error = $json->fail()->message($ve->getMessage())->create();
        die($error);
    }

    $sql = "SELECT cinfoId, userId, fee, `password`, isEligibleForAdmitCard  FROM `lc_enrolment_cinfo` WHERE regNo = :regNo AND regYear = :regYear";
    $admitCardResults = $db->select($sql, array("regNo"=>$regNo, "regYear"=>$regYear));
    
    if(count($admitCardResults) == 0){
        die($json->fail()->message("Applicant not found.")->create());
    }
    if(count($admitCardResults) > 1){
        die($json->fail()->message("Multiple applicants found. Please contact with Bar Council.")->create());
    }
    
    $admitCard = $admitCardResults[0];
    if($admitCard->fee == 0){
        die($json->fail()->message("Application fee not paid.")->create());
    }
    if(!isset($admitCard->password) || empty($admitCard->password)){
        die($json->fail()->message("Not eligible for admit card.")->create());
    }

    if($admitCard->isEligibleForAdmitCard == 0){
        die($json->fail()->message("Not eligible for admit card.")->create());
    }
    
    $encCinfoId = $endecryptor->encrypt($admitCard->cinfoId);
    $url = BASE_URL . "/admit-card/admit-card-preview.php?cinfo-id=$encCinfoId&config-id=$encConfigId";

    exit($json->success()->redirecturl($url)->create() );
    // exit('{"issuccess":true, "redirecturl":"'. $url .'"}');




?>