<?php

    #region imports
        require_once('../../Required.php');
        Required::SwiftLogger()->Database()->JSON()->HttpHeader()->Validable()->EnDecryptor()->DbSession();
    #endregion

    #region declarations
        $logger = new SwiftLogger(ROOT_DIRECTORY);
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $json = new JSON();
        $form = new Validable();
        $endecryptor = new EnDecryptor();
    #endregion

    HttpHeader::setJson();
    try {
        $db->connect();
    } catch (\Throwable $th) {
        $logger->createLog($dbExp->getMessage());
        $response = $json->fail()->message('Something is wrong.')->create();
        die($response);
    }


    try {
        $loginName = $form->label("Login name")->post('loginName')->required()->asString(false)->validate();
        $password = $form->label("পাসওয়ার্ড")->post("loginPassword")->required()->maxLen(10)->asString(false)->validate();

        $sql = "SELECT * FROM admins where loginName=:loginName AND loginPassword=:loginPassword AND isActive=1";
        $admins = $db->select($sql, array('loginName'=>$loginName, 'loginPassword'=>$password));

        if(count($admins) == 0){
            $response = $json->fail()->message("Login Name/পাসওয়ার্ড সঠিক নয়।")->create();
            exit($response);
        }

        $admin = $admins[0];


        $str = "login Success";
        
        $encSessionId = $endecryptor->encrypt($str);

        //sid = session id 
        $redirectUrl = BASE_URL."/school/welcome.php?sid=$encSessionId";
        $response = $json->success()->redirecturl($redirectUrl)->create();
        exit($response);
    } 
    catch (\ValidableException $vexp) {
        $logger->createLog($vexp->getMessage());
        $response = $json->fail()->message($vexp->getMessage())->create();
        die($response);
    } 
    catch(\DatabaseException $dbExp){
        $logger->createLog($dbExp->getMessage());
        $response = $json->fail()->message('Problem in saving data. Please try again.')->create();
        die($response);
    }
?>