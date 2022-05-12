<?php 

    #region Import libraries
        require_once("../../Required.php");

        Required::Logger()
                    ->Database()
                    ->DbSession()
                    ->EnDecryptor()
                    ->Validable()
                    ->JSON()->Clock();
    #endregion

    #region Variable declarations & initialization
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

    #region Form validation
        try {
            $regYear= $form->label("Registration Year")->post("regYear")->required()->asNumeric()->maxLen(4)->validate();
            $regNo= $form->label("Registration No.")->post("regNo")->required()->asNumeric()->maxLen(10)->validate();
            $userId = strtoupper($form->label("User ID")->post("userId")->required()->asString(false)->maxLen(15)->validate());
        } catch (\ValidableException $vexp) {
            die($json->fail()->message($vexp->getMessage())->create());
        }
    #endregion

    $sql = "SELECT * FROM lc_enrolment_cinfo WHERE regNo=:regNo AND regYear=:regYear AND userId=:userId";
    $cinfos = $db->select($sql, array('regNo' => $regNo, "regYear"=>$regYear, "userId"=>$userId)); 

    if(count($cinfos) != 1){
        die($json->fail()->message("Applicant not found.")->create());
    }

    $cinfo = $cinfos[0];

    #region Session
        $session = new DbSession($db, "lc_enrolment_sessions");
        $session->start($cinfo->registrationId);
        $session->setData("regNo", $cinfo->regNo);
        $session->setData("regYear", $cinfo->regYear);
        $session->setData("registrationId", $cinfo->registrationId);
        $session->setData("cinfoId", $cinfo->cinfoId);
        $encSessionId = $endecryptor->encrypt($session->getSessionId());
        $session->setData("EncSessionId", $encSessionId);
        $encRegistrationId = $endecryptor->encrypt($cinfo->registrationId);
        $session->setData("EncRegistrationId", $encRegistrationId);
    #endregion

    $encPostConfigId = $endecryptor->encrypt($cinfo->postConfigId);
    $redirectUrl = BASE_URL . "/app/applicant-copy/preview.php?session-id=$$encSessionId&config-id=$encPostConfigId";
    exit($json->success()->redirecturl($redirectUrl)->create());
?>