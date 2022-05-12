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

    if (!isset($_GET['sid']) || empty($_GET['sid'])) {
        HttpHeader::redirect(BASE_URL."/school/login/login.php");
    }

    $encSid = trim($_GET['sid']);

    try {
        $sid = $endecryptor->decrypt($encSid);
        $db->connect();
        $session = new DbSession($db);
        $session->continue(intval($sid));
        $session->close();
        HttpHeader::redirect(BASE_URL."/school/login/login.php");
    } 
    catch (\DatabaseException $dbExp) {
        $logger->createLog($dbExp->getMessage());
        HttpHeader::redirect(BASE_URL."/school/login/login.php");
    } 
    catch (\SessionException $exp) {
        HttpHeader::redirect(BASE_URL."/school/login/login.php");
    } 
    catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        HttpHeader::redirect(BASE_URL."/school/login/login.php");
    }
?>