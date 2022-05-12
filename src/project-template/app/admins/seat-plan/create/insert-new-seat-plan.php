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

    try {
        $insert["venue"]= $form->label("Venue")->post("venue")->required()->validate();
        $insert["building"]= $form->label("Building")->post("building")->required()->validate();
        $insert["floor"]= $form->label("Floor")->post("floor")->required()->validate();
        $insert["room"]= $form->label("Room")->post("room")->required()->validate();
        $insert["start_roll"]= $form->label("Start Roll")->post("start_roll")->required()->validate();
        $insert["end_roll"]= $form->label("End Roll")->post("end_roll")->required()->validate();
        $insert["total"] = $form->label("Total")->post("total")->required()->validate();

        $start_roll= $insert["start_roll"];
        $end_roll= $insert["end_roll"];
        

    } catch (\ValidableException $ve) {
        $error = $json->fail()->message("f". $ve->getMessage())->create(); // SwiftJSON::failure($ve->getMessage()); 
        die($error);
    }

    $db->connect(); $db->fetchAsObject();
    /*$existingSeatPlan = $db->select("select * from lc_enrolment_seat_plans where start_roll between  $start_roll  AND $end_roll AND end_roll between  $start_roll  AND $end_roll");

    if(count($existingSeatPlan) > 0){
        $msg = "Start Roll- {$insert["start_roll"]} and End Roll-{$insert["end_roll"]} already exists.";
        die($json->fail()->message($msg)->create()); 
    }*/

    try{
        $db->insert($insert, "lc_enrolment_seat_plans");
    }
    catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        die($json->fail()->message("Failed to save data.")->create()); 

    }

    exit($json->success()->message("Success")->create()); 
?>