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


        $delconfigID= $form->label("Exam Session")->post("deleteId")->required()->validate();
        $deleteid = $endecryptor->decrypt($delconfigID);
       
        $sql = "SELECT configId FROM post_configurations WHERE configId=:configId";
        $examDetail = ($db->select($sql, array("configId"=>$deleteid)));
        if(count($examDetail) == 0){
            die($json->fail()->message("Exam Session Details not found.")->create());
        }

        $sql = "SELECT configId FROM post_configurations WHERE configId=:configId";
        $examDetail = ($db->select($sql, array("configId"=>$deleteid)));
        if(count($examDetail) == 0){
            die($json->fail()->message("Request Details not found.")->create());
        }
    
        $sql = "DELETE FROM post_configurations WHERE configId=:configId";
        $db->delete($sql, array("configId"=>$deleteid));

        exit($json->success()->message("Exam Details deleted successfully.")->redirecturl(BASE_URL."/admins/exam-session/read/list/exam-session-list.php")->create());
?>