<?php 
    require_once("../Required.php");

    Required::Logger()
        ->Database()->DbSession()
        ->Clock()
        ->EnDecryptor()
        ->JSON()
        ->Validable()
        ->AgeCalculator(2)
        ->Imaging()
        ->UniqueCodeGenerator()
        ->Helpers()->ExclusivePermission()->HttpHeader();

    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $form = new Validable();
    
    $clock = new Clock();
    $json = new JSON();
   
    $db->connect();
    $db->fetchAsObject();

    if(!isset($_POST["cinfoId"]) || empty(trim($_POST["cinfoId"])))
        die($json->fail()->message("Invalid request.")->create());
    
    $encCinfoId = trim($_POST["cinfoId"]); //decryption has been done in the following try .. catch block for safety reason.
   
    //if decryption throws any exception, redirect to the sorry page.
    try {
        $cinfoId =  $endecryptor->decrypt($encCinfoId);       
    } catch (\Exception $exp) {
        die($json->fail()->message("Invalid request.")->create());
    }

    $sql = "SELECT 	cinfoId, 
                    registrationId, 
                    userId, 
                    regNo, 
                    regYear,
                    applicantType
            FROM lc_enrolment_cinfo 
            WHERE cinfoId=:cinfoId";
    $cinfos = $db->select($sql, array('cinfoId' => $cinfoId));

    if (count($cinfos) != 1) {
        die($json->fail()->message("Application not found.")->create());
    }

    $cinfo = $cinfos[0];

    #region Check whether any requests exist.
        $sql = "SELECT requestId, hasApproved FROM lc_enrolment_registrations_update_request WHERE cinfoId=:cinfoId";
        $existingRequests = $db->select($sql, array("cinfoId"=>$cinfoId));
        
        if(count($existingRequests) > 0){
            die($json->fail()->message("You already submitted correction request.")->create());
        }

        // if(count($existingRequests) > 1){
        //     die($json->fail()->message("You already submitted correction request.")->create());
        // } else if(count($existingRequests) == 1){
        //     $existingRequests = $existingRequests[0];        
        //     if(strtolower($existingRequests->hasApproved) == 'approved'){
        //         die($json->fail()->message("Your request has been approved. New request is prohibited.")->create());
        //     }elseif(strtolower($existingRequests->hasApproved) == 'declined'){
        //         die($json->fail()->message("Your previous request has been declined. New request is prohibited.")->create());
        //     } else {
        //         die($json->fail()->message("Your request has been pending. New request is prohibited.")->create());
        //     }
        // }
    #endregion

    $insert["registrationId"] = trim($cinfo->registrationId);
    $insert["cinfoId"] = $cinfoId;
    $insert["userId"] = trim($cinfo->userId);
    $insert["regNo"] = trim($cinfo->regNo);
    $insert["regYear"] = trim($cinfo->regYear);

    #region Form validation
        try {
            $insert["name"] = strtoupper($form->label("Name")->post("name")->required()->asString(true)->maxLen(100)->validate());
            $insert["fatherName"] = strtoupper($form->label("Father Name")->post("fatherName")->required()->asString(true)->maxLen(100)->validate());
            $insert["universityName"] = strtoupper($form->label("University Name")->post("universityName")->required()->asString(true)->maxLen(150)->validate());
            $insert["seniorAdvocateName"] = strtoupper($form->label("Senior Advocate Name")->post("seniorAdvocateName")->required()->asString(true)->maxLen(100)->validate());
            $insert["pupilageContractDate"] = $form->label("Pupilage Contract Date")->post("pupilageContractDate")->required()->asDate()->validate();
            $insert["pupilageContractDate"] = $clock->toString($insert["pupilageContractDate"], DatetimeFormat::MySqlDate());
            $insert["applicantType"] = $cinfo->applicantType;
            $insert["remarks"] = $form->label("Remarks")->post("remarks")->optional()->asString(true)->maxLen(150)->validate();
        } catch (\ValidableException $ve) {
            die($json->fail()->message($ve->getMessage())->create());
        }
        catch (\Exception $exp) {
            $logger->createLog($exp->getMessage());
            die($json->fail()->message($exp->getMessage())->create());
        }
    #endregion

    #region Save data
        try{
            $insert["submitDatetime"]= $clock->toString("now", DatetimeFormat::MySqlDatetime());
            $insert["requestId"] = $db->insert($insert, "lc_enrolment_registrations_update_request");    
        }
        catch (\Exception $exp) {
            $logger->createLog($exp->getMessage());
            die($json->fail()->message("Problem in saving data. Please try again.")->create());
        }
    #endregion

    exit($json->success()->message("Request submitted successfully.")->create());
?>