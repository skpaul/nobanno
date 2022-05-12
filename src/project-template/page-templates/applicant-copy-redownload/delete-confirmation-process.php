<?php

require_once("../Required.php");

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
            die($json->fail()->message("Invalid session. Please login again. Error code- 774965")->create());
        }

        $encSessionId = trim($_GET["session-id"]);

        try {
            $sessionId = $endecryptor->decrypt($encSessionId);
            $session = new DbSession($db, "lc_enrolment_sessions");
            $session->continue($sessionId);
        } catch (\SessionException $th) {
            // $logger->createLog($th->getMessage());
            die($json->fail()->message("Invalid session. Please login again. Error code- 774965")->create());
        } catch (\Exception $exp) {
            die($json->fail()->message("Invalid session. Please login again. Error code- 774965")->create());
        }
    #endregion
    

    $encCinfoId = $form->label("Applicant info")->post("cinfoId")->required()->validate();
    $cinfoId = $endecryptor->decrypt($encCinfoId);
   
    $sql = "SELECT fee FROM `lc_enrolment_cinfo` WHERE cinfoId=:cinfoId";
    $applicants = ($db->select($sql, array("cinfoId"=>$cinfoId)));
    if(count($applicants) == 0){
        die($json->fail()->message("Applicaton not found.")->create());
    }

    $applicant = $applicants[0];
    if($applicant->fee == 1){
        die($json->fail()->message("Applicaton can not be deleted after fee payment.")->create());
    }

    $sql = "DELETE FROM `lc_enrolment_cinfo` WHERE cinfoId=:cinfoId";
    $db->delete($sql, array("cinfoId"=>$cinfoId));
    $session->close();
    exit($json->success()->message("Applicaton successfully deleted.")->redirecturl(BASE_URL)->create());

?>

