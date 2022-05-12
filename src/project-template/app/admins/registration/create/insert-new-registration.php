<?php 
    // This file is used in upload csv and single registration create page.
    require_once("../../../Required.php");

    Required::Logger()
        ->Database()
        ->DbSession()
        ->Clock()
        ->EnDecryptor()
        ->HttpHeader()
        ->JSON()
        ->Validable();

    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();  //$endecryptor  $endecryptor
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
            $currentUser = $session->getData("loginName");
        } catch (\SessionException $th) {
            // $logger->createLog($th->getMessage());
            HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again. Error Code-456815.");
        } catch (\Exception $exp) {
            HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again. Error Code-774965.");
        }
    #endregion

    try {
        $insert["regNo"]= $form->label("Registration no.")->post("regNo")->required()->asInteger(false)->maxLen(10)->validate();
        $insert["regYear"]= $form->label("Registration year")->post("regYear")->required()->asInteger(false)->exactLen(4)->validate();
        $insert["name"]= strtoupper($form->label("Name")->post("name")->required()->asString(true)->maxLen(150)->validate());
        $insert["fatherName"]= strtoupper($form->label("Father name")->post("fatherName")->required()->asString(true)->maxLen(150)->validate());
        $insert["universityName"]= strtoupper($form->label("University Name")->post("universityName")->required()->asString(true)->validate());
        $insert["seniorAdvocateName"]= strtoupper($form->label("Senior advocate name")->post("seniorAdvocateName")->required()->asString(true)->maxLen(150)->validate());
        $insert["pupilageContractDate"] = $form->label("Pupilage contract date")->post("pupilageContractDate")->required()->asDate()->validate();
        $insert["pupilageContractDate"] = $clock->toString($insert["pupilageContractDate"], DatetimeFormat::MySqlDate());

        //$applicantType must be Regular or Re-appeared
        $insert["applicantType"] = $form->label("Applicant type")->post("applicantType")->required()->asString(false)->maxLen(20)->validate();

        $isReRegistered = $form->label("Re-Registration Information")->post("isReRegistered")->required()->asString(false)->validate();
        $insert["isReRegistered"] = strtolower($isReRegistered) == "yes" ? 1 : 0;

        $hasBarAtLaw = $form->label("Bar-at-law Information")->post("hasBarAtLaw")->required()->asString(false)->validate();
        $insert["hasBarAtLaw"] = strtolower($hasBarAtLaw) == "yes" ? 1 : 0;

    } catch (\ValidableException $ve) {
        $error = $json->fail()->message($ve->getMessage())->create();
        die($error);
    }


    $existingDatas = $db->select("select * from lc_enrolment_registrations where regNo=:regNo AND regYear=:regYear", 
                                    array("regNo"=>$insert["regNo"], "regYear"=>$insert["regYear"]));

    if(count($existingDatas) > 0){
        $msg = "Registration No.- {$insert["regNo"]} and Registration Year-{$insert["regYear"]} already exists.";
        die($json->fail()->message($msg)->create()); 
    }

    try{
        
        $insert["modifiedBy"] = $currentUser;
        $insert["createDatetime"] = $clock->toString("now", DatetimeFormat::MySqlDatetime());
        $db->insert($insert, "lc_enrolment_registrations");
    }
    catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        die($json->fail()->message("Failed to save data.")->create()); 

    }

    // $redirectUrl = BASE_URL . "/admins/registration/read/list/registration-list.php?session-id=$encSessionId";
    // exit($json->success()->message("Success")->redirecturl($redirectUrl)->create());
    exit($json->success()->message("Success")->create());
?>