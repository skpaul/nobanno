<?php 
    // This file is used in upload csv and single registration create page.
    require_once("../../../Required.php");

    Required::Logger()
        ->Database()->DbSession()
        ->Clock()
        ->EnDecryptor()
        ->JSON()
        ->Validable();

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
        $regId =$form->label("Registration Id")->post("registrationId")->required()->validate();
        $regId =  $endecryptor->decrypt($regId);

        // $update["regNo"] = strtoupper($form->label("Reg. No.")->post("regNo")->required()->asInteger(false)->maxLen(10)->validate());
        // $update["regYear"] = strtoupper($form->label("Reg. Year")->post("regYear")->required()->asInteger(true)->exactLen(4)->validate());
        $update["name"] = strtoupper($form->label("Name")->post("name")->required()->asString(true)->maxLen(100)->validate());
        $update["fatherName"] = strtoupper($form->label("Father Name")->post("fatherName")->required()->asString(true)->maxLen(100)->validate());
        $update["seniorAdvocateName"] = strtoupper($form->label("Senior Advocate Name")->post("seniorAdvocateName")->required()->asString(true)->maxLen(100)->validate());
        $update["pupilageContractDate"] = $form->label("Pupilage Contract Date")->post("pupilageContractDate")->required()->asDate()->validate();
        $update["pupilageContractDate"] = $clock->toString( $update["pupilageContractDate"], DatetimeFormat::MySqlDate());

        $update["applicantType"] = $form->label("Applicant Type")->post("applicantType")->required()->asString(true)->validate();
        $update["universityName"] = strtoupper($form->label("University Name")->post("universityName")->required()->asString(true)->validate());

        //isReRegistered
        $isReRegistered = $form->label("Re-registration status")->post("isReRegistered")->required()->asString(false)->validate();
        $update["isReRegistered"] = strtolower($isReRegistered) == "yes" ? 1 : 0;

        $hasBarAtLaw = $form->label("Bar-at-law")->post("hasBarAtLaw")->required()->asString(false)->validate();
        $update["hasBarAtLaw"] = strtolower($hasBarAtLaw) == "yes" ? 1 : 0;

        $isActive = $form->label("Active status")->post("isActive")->required()->asString(false)->validate();
        $update["isActive"] = strtolower($isActive) == "yes" ? 1 : 0;

        $isBlocked = $form->label("Block status")->post("isBlocked")->required()->asString(false)->validate();
        $update["isBlocked"] = strtolower($isBlocked) == "yes" ? 1 : 0;

    } catch (\ValidableException $ve) {
        die($json->fail()->message($ve->getMessage())->create());
    }
    catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        die($json->fail()->message($exp->getMessage())->create());
    }

    $checkSql = "SELECT registrationId FROM lc_enrolment_cinfo WHERE registrationId=:registrationId";
    $existingRecords = $db->select($checkSql, array("registrationId"=>$regId));
    if(count($existingRecords)>0){
        die($json->fail()->message("You can not edit registration data after submitting the application.")->create());
    }

    #region Save data
        try{
            $whereSQL = "registrationId=:registrationId";
            
            $update["modifiedDatetime"]= $clock->toString("now", DatetimeFormat::MySqlDatetime());
            $update["modifiedBy"]= $session->getData("loginName");

            $updateResult = $db->updateAuto("lc_enrolment_registrations", $whereSQL, $update, array("registrationId"=>$regId));
        }
        catch (\Exception $exp) {
            $logger->createLog($exp->getMessage());
            die($json->fail()->message("Problem in saving data. Please try again.")->create());
        }
    #endregion
    
    exit($json->success()->message("Updated successfully.")->create());
?>