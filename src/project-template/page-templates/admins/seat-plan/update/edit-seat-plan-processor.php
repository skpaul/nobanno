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

        $db->connect(); 
        $db->fetchAsObject();

        //$seatPlan = new stdClass();

    try {
        $encRequestId = trim($_GET["request-id"]);            

        $requestId =  $endecryptor->decrypt($encRequestId);
        

        $update["venue"]= $form->label("Venue")->post("venue")->required()->asString(true)->maxLen(1000)->validate();
        $update["building"]= $form->label("Building")->post("building")->required()->asString(true)->validate();
        $update["floor"]= $form->label("Floor")->post("floor")->required()->asString(true)->validate();
        $update["room"]= $form->label("Room")->post("room")->required()->asInteger(true)->validate();
        $update["start_roll"]= $form->label("Start Roll")->post("start_roll")->required()->asInteger(true)->validate();
        $update["end_roll"]= $form->label("End Roll")->post("end_roll")->required()->asInteger(true)->validate();
        $update["total"]= $form->label("Total")->post("total")->required()->asInteger(true)->validate();

    } catch (\ValidableException $ve) {
        $error = $json->fail()->message("f". $ve->getMessage())->create(); // SwiftJSON::failure($ve->getMessage()); 
        die($error);
    }

    

    try{
        $db->update("update lc_enrolment_seat_plans set venue=:venue,building=:building,floor=:floor,room=:room,start_roll=:start_roll,end_roll=:end_roll,total=:total where id=:id", 
        array("venue"=>$update["venue"],"building"=>$update["building"],"floor"=>$update["floor"],"room"=>$update["room"],"start_roll"=>$update["start_roll"],"end_roll"=>$update["end_roll"],"total"=>$update["total"],"id"=>$id));
    }
    catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        die($json->fail()->message("Update Failed.")->create()); 

    }

    exit($json->success()->message($update["venue"])->create());
?>