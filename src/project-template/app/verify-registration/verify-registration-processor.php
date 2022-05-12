<?php 
    #region Import libraries
        require_once("../../Required.php");

        Required::Logger()
                    ->Database()
                    ->DbSession()
                    ->EnDecryptor()
                    ->Validable()
                    ->JSON()
                    ->ExclusivePermission()->Clock();
    #endregion

    #region Initialize variables
        $logger = new Logger(ROOT_DIRECTORY);
        $form = new Validable();
        $clock = new Clock();
        $json = new JSON();
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $hasExclusivePermission = ExclusivePermission::hasPermission();
    #endregion
   
    #region Database connection
        $db->connect(); $db->fetchAsObject();
    #endregion

    #region Check Post Config
        //if config id not found in POST variables, send json response with redirect url.
        if(!isset($_POST["configId"]) || empty(trim($_POST["configId"])))
            die($json->fail()->message("Invalid request. Error code- 91438.")->create());

        $encConfigId = trim($_POST["configId"]);

        $configId = $endecryptor->decrypt($encConfigId);
        if(!$configId)  die($json->fail()->message("Invalid request. Error code- 45789.")->create());

        if($hasExclusivePermission){
            //If has exclusive permission, don't check isActive status
            $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";
        }
        else{
            //If does not have exclusive permission, check isActive status.
            $sql = "SELECT * FROM `post_configurations` WHERE isActive=1 AND court=:court AND applicationType = :applicationType AND configId=:configId";
        }

        $configs = $db->select($sql, array('court' => COURT, "applicationType"=>APPLICATION_TYPE, "configId"=>$configId ));  
        if(count($configs) != 1) die($json->fail()->message("Application is not available.")->create());

        $postConfig = $configs[0];
    #endregion

    #region Check Start & End datetime
        //If does not have exclusive permission, check application start and end datetime.
        if(!$hasExclusivePermission){
            $now = $clock->toDate("now");
            $applicationStartDatetime = $clock->toDate($postConfig->applicationStartDatetime); 
            $applicationEndDatetime = $clock->toDate($postConfig->applicationEndDatetime);  
            if($now < $applicationStartDatetime) 
                die($json->fail()->message("Application will start from " . $clock->toString ($postConfig->applicationStartDatetime, DatetimeFormat::BdDatetime()))->create());

            if($now > $applicationEndDatetime) 
                die($json->fail()->message("Application is not available")->create());
        }
    #endregion

    #region Form validation
        try {
            $regNo= $form->label("Registration No.")->post("regNo")->required()->asNumeric()->maxLen(10)->validate();
            $regYear= $form->label("Registration Year")->post("regYear")->required()->asNumeric()->maxLen(4)->validate();
            $applicantType = $form->label("Applicant Type")->post("applicantType")->required()->asString(false)->maxLen(15)->validate();
        } catch (\ValidableException $vexp) {
            die($json->fail()->message($vexp->getMessage())->create());
        }
    #endregion

    #region Session
        //Start a brand new session
        $session = new DbSession($db, "lc_enrolment_sessions");
        $session->start($registration->registrationId);
        $session->setData("regNo", $registration->regNo);
        $session->setData("regYear", $registration->regYear);
        $session->setData("registrationId", $registration->registrationId);
        $encSessionId = $endecryptor->encrypt($session->getSessionId());
        $session->setData("EncSessionId", $encSessionId);
    #endregion

    //Prepare an special query string parameter if the user has special permission
    $exclusivePermissionQueryString = "";
    if(isset($_GET[ExclusivePermission::$propName]) && !empty($_GET[ExclusivePermission::$propName])){
        $exclusivePermissionQueryString = "&" . ExclusivePermission::$propName."=".ExclusivePermission::$propValue;
    }


    $queryString = "registration-id=$encRegistrationId&config-id=$encConfigId&session-id=$encSessionId" . $exclusivePermissionQueryString;
    $redirectUrl = BASE_URL . "/app/registration-confirmation/confirm-registration.php?$queryString";

    die($json->success()->redirecturl($redirectUrl)->create());
?>