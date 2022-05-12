<?php 

    require_once('../../../../Required.php');

    Required::Logger()
        ->Database()->DbSession()
        ->Clock()
        ->EnDecryptor()
        ->JSON()
        ->Validable()->SmsSender();

    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $form = new Validable();
    $clock = new Clock();
    $json = new JSON();
    $db->connect(); $db->fetchAsObject();

    #region check session
        if(!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))){
            die($json->fail()->message("Session expired or not found. Please login.")->create());
        }

        $encSessionId = trim($_GET["session-id"]);

        try {
            $sessionId = (int)$endecryptor->decrypt($encSessionId);
            $session = new DbSession($db, "admin_sessions");
            $session->continue($sessionId);
            $roleCode = $session->getData("roleCode");
        } catch (\SessionException $th) {
            // $logger->createLog($th->getMessage());
            die($json->fail()->message("Session expired or not found. Please login.")->create());
        } catch (\Exception $exp) {
            die($json->fail()->message("Session expired or not found. Please login.")->create());
        }
    #endregion
        
        try {
            if(!isset($_POST["requestId"]) || empty($_POST["requestId"])){
                die($json->fail()->message("Invalid request.")->create());
            }

            $encRequestId =  trim($_POST["requestId"]);            
            $requestId =  $endecryptor->decrypt($encRequestId);
             
         } 
         catch(\Exception $exp){
             die($json->fail()->message($exp->getMessage())->create());
         }
    
        $sql = "SELECT * FROM `lc_enrolment_registrations_update_request` WHERE requestId=:requestId";
        $requests = $db->select($sql, array("requestId"=>$requestId));
    
        if(count($requests) != 1){
            die($json->fail()->message("Invalid request Id.")->create());
        }
        $request = $requests[0];

        if(strtolower($request->hasApproved) != 'pending'){
            die($json->fail()->message("Already $request->hasApproved.")->create());
        }

        // $sql = "SELECT mobileNo FROM lc_enrolment_cinfo WHERE cinfoId= $request->cinfoId";
        // $cinfo = ($db->select($sql))[0];

        try{
            $currentUser = $session->getData("loginName");
            $now = $clock->toString("now", DatetimeFormat::MySqlDatetime());
            $db->beginTransaction();
            $cinfoUpdate = array("fullName"=>$request->name, 
                                "fatherName"=>$request->fatherName, 
                                "seniorAdvocateName"=>$request->seniorAdvocateName, 
                                "pupilageContractDate"=>$request->pupilageContractDate, 
                                "modifiedBy"=> $currentUser,
                                "modifiedDatetime"=> $now
                            );
            $eduUpdate = array("llbUni"=>$request->universityName);

            $db->updateAuto("lc_enrolment_cinfo", "cinfoId=:cinfoId", $cinfoUpdate, array("cinfoId"=>$request->cinfoId));
            $db->updateAuto("lc_enrolment_higher_educations", "cinfoId=:cinfoId", $eduUpdate, array("cinfoId"=>$request->cinfoId));

            $db->update("UPDATE `lc_enrolment_registrations_update_request` SET hasApproved='approved', approvedBy='$currentUser', approvedDatetime='$now' where requestId=:requestId", array("requestId"=>$requestId));
            $db->commit();
            if(ENVIRONMENT == 'PRODUCTION'){
                // SmsSender::sendSms("Registration update request accepted. Reg-$request->regNo, Year-$request->regYear. Thank you- Bar Council",$request->mobileNo, SMS_API_PASSWORD);
            }
            
        }catch (\Exception $exp) {
            $logger->createLog($exp->getMessage());
            die($json->fail()->message("Failed to save data.")->create()); 
    
        }

    
    exit($json->success()->message("Success.")->create());
?>