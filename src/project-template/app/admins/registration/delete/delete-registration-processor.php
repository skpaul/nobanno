<?php 
    // This file is used in upload csv and single registration create page.
    require_once("../../../Required.php");


    Required::Logger()
        ->Database()
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
        $endecryptor = new EnDecryptor();

        // $encId = trim($_GET["registration-id"]);
        // $id =  $endecryptor->decrypt($encId);

        $db->connect(); 
        $db->fetchAsObject();


        $deleteRegID= $form->label("Registration")->post("deleteId")->required()->validate();
        $id = $endecryptor->decrypt($deleteRegID);
       
        $sql = "SELECT registrationId FROM lc_enrolment_registrations WHERE registrationId=:registrationId";
        $regDetail = ($db->select($sql, array("registrationId"=>$id)));
        if(count($regDetail) == 0){
            die($json->fail()->message("Registration Details not found.")->create());
        }

        $sql = "SELECT registrationId FROM lc_enrolment_registrations_update_request WHERE registrationId=:registrationId";
        $reqDetail = ($db->select($sql, array("registrationId"=>$id)));
        if(count($reqDetail) == 0){
            die($json->fail()->message("Request Details not found.")->create());
        }
    
    
        $sql = "DELETE FROM lc_enrolment_registrations WHERE registrationId=:registrationId";
        $db->delete($sql, array("registrationId"=>$id));

        $sql = "DELETE FROM lc_enrolment_registrations_update_request WHERE registrationId=:registrationId";
        $db->delete($sql, array("registrationId"=>$id));
        exit($json->success()->message("Registration Details deleted successfully.")->redirecturl(BASE_URL."/admins/registration/read/list/registration-list.php")->create());
?>