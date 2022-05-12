<?php
    require_once('../Required.php');
    Required::Logger()->Database()->EnDecryptor()   ;
    
   

    defined('MAX_IDLE_TIME') or define('MAX_IDLE_TIME', 1); 


class online {
	public static function who() {

        // $now = new DateTime("now", new DateTimeZone("Asia/Dhaka")); 
        // // $start_date = $start_date->format('Y-m-d');
		// //$interval = DateInterval::createFromDateString('-15 minutes');

		// $twoMinsAgo = $datetime->now()->subMinutes(5)->asYmdHis();
		
        $twoMinsAgo = new DateTime("2 minutes ago", new DateTimeZone("Asia/Dhaka"));
        $twoMinsAgo = $twoMinsAgo->format("Y-m-d H:i:s");

	
		// Get data from database
		try{

            $logger = new Logger(ROOT_DIRECTORY);
            $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
            $db->connect();
            $db->fetchAsObject();

			$sql = "SELECT count(*) AS total FROM `lc_enrolment_sessions` WHERE `datetime` >= '$twoMinsAgo'";
			$onlineUser = ($db->select($sql))[0];
			$onlineUser = $onlineUser->total;
			return $onlineUser;
		} catch (\Exception $exp) {
			$logger->createLog($exp->getMessage());
		}
		
	}
	
}
