<?php 
    // This file is used in upload csv and single registration create page.
    require_once("../../../Required.php");


    Required::Logger()
        ->Database()
        ->Clock()
        ->EnDecryptor()
        ->JSON()
        ->Validable()->HttpHeader();

        $logger = new Logger(ROOT_DIRECTORY);
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $form = new Validable();
        $clock = new Clock();
        $json = new JSON();
        $endecryptor = new EnDecryptor();


        $db->connect(); 
        $db->fetchAsObject();

        #region check session
        if(!isset($_GET["session-id"]) || empty(trim($_GET["session-id"])))
            die($json->fail()->message("Invalid session. Please login again.")->create());
        $encSessionId = trim($_GET["session-id"]);
        #endregion

        if(!isset($_GET["delete-id"]) || empty(trim($_GET["delete-id"]))){
            HttpHeader::redirect(BASE_URL . "/admins/seat-plan/read/list/seat-plan-list.php?session-id=$encSessionId");
        }

        $encId = trim($_GET["delete-id"]);
        $id =  $endecryptor->decrypt($encId);

        try{
            $deleteSeatID= $form->label("Venue")->post("deleteSeatID")->required()->validate();
            $id = $endecryptor->decrypt($deleteSeatID);
        
            $sql = "SELECT id FROM lc_enrolment_seat_plans WHERE id=:id";
            $seatPlan = ($db->select($sql, array("id"=>$id)));
            if(count($seatPlan) == 0){
                die($json->fail()->message("Seat plan not found.")->create());
            }
        
        
            $sql = "DELETE FROM lc_enrolment_seat_plans WHERE id=:id";
            $db->delete($sql, array("id"=>$id));
        }catch(\Exception $ex){
            $logger->createLog($ex->getMessage());
            die($json->fail()->message("Failed to save data.")->create()); 
        }
        exit($json->success()->message("Seat Plan deleted successfully.")->redirecturl(BASE_URL."/admins/seat-plan/read/list/seat-plan-list.php?session-id=$encSessionId")->create());
?>