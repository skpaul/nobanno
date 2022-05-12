<?php 
    require_once("../Required.php");

    Required::Logger()
                ->Database()
                ->DbSession()
                ->EnDecryptor()
                ->Validable()
                ->JSON()->Clock();

    #region Declarations
        $logger = new Logger(ROOT_DIRECTORY);
        $form = new Validable();
        $clock = new Clock();
        $json = new JSON();
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    #endregion

    $db->connect();
    $db->fetchAsObject();

    #region Form Validation
        try {
            $regYear= $form->label("Registration Year")->post("regYear")->required()->asNumeric()->maxLen(4)->validate();
            $regNo= $form->label("Registration No.")->post("regNo")->required()->asNumeric()->maxLen(10)->validate();
            $userId = strtoupper($form->label("User ID")->post("userId")->required()->asString(false)->maxLen(15)->validate());
        } catch (\ValidableException $vexp) {
            die($json->fail()->message($vexp->getMessage())->create());
        }
    #endregion

    $sql = "SELECT cinfoId FROM lc_enrolment_cinfo WHERE regNo=:regNo AND regYear=:regYear AND userId=:userId";
    $cinfos = $db->select($sql, array('regNo' => $regNo, "regYear"=>$regYear, "userId"=>$userId)); 

    if(count($cinfos) != 1){
        die($json->fail()->message("Application not found.")->create());
    }

    $cinfo = $cinfos[0];

    #region Check whether any requests exist.
        $sql = "SELECT requestId, hasApproved, adminMessage FROM lc_enrolment_registrations_update_request WHERE cinfoId=:cinfoId";
        $existingRequests = $db->select($sql, array("cinfoId"=>$cinfo->cinfoId));
        
        if(count($existingRequests) > 0){
            // die($json->fail()->message("You already submitted correction request.")->create());
            $existingRequests = $existingRequests[0];        
            if(strtolower($existingRequests->hasApproved) == 'approved'){
                die($json->fail()->message("Your correction request has been approved. Please download the updated applicant copy.")->create());
            }elseif(strtolower($existingRequests->hasApproved) == 'declined'){
                die($json->fail()->message("Your correction request has been declined. $existingRequests->adminMessage")->create());
            } else {
                die($json->fail()->message("Your correction request is pending for approval. Please check again later.")->create());
            }
        }
    #endregion
    


    $encCinfoId = $endecryptor->encrypt($cinfo->cinfoId);
    $redirectUrl = BASE_URL . "/registration-correction/correction-request.php?cinfo-id=$encCinfoId";
    exit($json->success()->redirecturl($redirectUrl)->create());
?>