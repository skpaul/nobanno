<?php 
   
    #region Import libraries
        require_once("../Required.php");
        Required::Logger()
                ->Database()
                ->DbSession()
                ->EnDecryptor()
                ->Validable()
                ->JSON()->Clock();
    #endregion

	#region Class instance declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);
        $form = new Validable();
        $clock = new Clock();
        $json = new JSON();
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    #endregion
   
	#region Database connection
        $db->connect();
        $db->fetchAsObject();
	#endregion

    #region Form Validation
        try {
            $regYear= $form->label("Registration Year")->post("regYear")->required()->asNumeric()->maxLen(4)->validate();
            $regNo= $form->label("Registration No.")->post("regNo")->required()->asNumeric()->maxLen(10)->validate();
            $userId = $form->label("User ID")->post("userId")->required()->asString(false)->maxLen(15)->validate();
        } catch (\ValidableException $vexp) {
            die($json->fail()->message($vexp->getMessage())->create());
        }
    #endregion

    $sql = "SELECT `cinfoId`, `fee` FROM lc_enrolment_cinfo WHERE regNo=:regNo AND regYear=:regYear AND userId=:userId";
    $cinfos = $db->select($sql, array('regNo' => $regNo, "regYear"=>$regYear, "userId"=>$userId)); 

    if(count($cinfos) != 1){
        die($json->fail()->message("Applicant not found.")->create());
    }
    else{
        $encryptedCinfoId = $endecryptor->encrypt($cinfos[0]->cinfoId);
        $redirectUrl = BASE_URL . "/payment-status/payment-preview.php?cinfo-id=$encryptedCinfoId";
        exit($json->success()->redirecturl($redirectUrl)->create());
    }
    
?>