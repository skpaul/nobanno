<?php
    // if (session_status() === PHP_SESSION_NONE) session_start();

    #region imports
        require_once('../Required.php');
        Required::Logger()->Database()->JSON()->HttpHeader()->Validable()->EnDecryptor()->DbSession()->Clock();
    #endregion

    #region declarations
        $logger = new Logger(ROOT_DIRECTORY);
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $json = new JSON();
        $form = new Validable();
        $endecryptor = new EnDecryptor();
        $clock = new Clock();
    #endregion

    $db->connect(); $db->fetchAsObject();

    try {
        $loginName = $form->label("Login name")->post('loginName')->required()->asString(false)->validate();
        $password = $form->label("Password")->post("loginPassword")->required()->maxLen(10)->asString(false)->validate();
    } 
    catch (\ValidableException $vexp) {
        $response = $json->fail()->message($vexp->getMessage())->create();
        die($response);
    } 

    $sql = "SELECT * FROM admins where loginName=:loginName AND loginPassword=:loginPassword AND isActive=1";
    $admins = $db->select($sql, array('loginName'=>$loginName, 'loginPassword'=>$password));

    if(count($admins) == 0){
        $response = $json->success()->authentication(false)->message("Login Name/Password invalid.")->create();
        exit($response);
    }
        
    $admin = $admins[0];
    $session = new DbSession($db, "admin_sessions");
    $session->start($admin->adminId);
    $session->setData("fullName", $admin->fullName);
    $session->setData("loginName", $admin->loginName);
    $session->setData("roleCode", $admin->roleCode);

    $encSessionId = $endecryptor->encrypt($session->getSessionId());
    $session->setData("EncSessionId", $encSessionId);

    $fullName = $admin->fullName;
    $loginName = $admin->loginName;
    // $adminType = $endecryptor->encrypt($admin->adminType);

    $lastLogin = $clock->toString("now", DatetimeFormat::MySqlDatetime());
    $updateSql = "UPDATE admins SET lastLogin=:lastLogin WHERE adminId=:adminId";
    $db->update($updateSql, array("lastLogin"=>$lastLogin, "adminId"=>$admin->adminId));

    $redirectUrl = BASE_URL."/admins/dashboard.php?session-id=$encSessionId";
    $response = $json->success()->lastLogin($lastLogin)->redirecturl($redirectUrl)->create();
    exit($response);

    
?>