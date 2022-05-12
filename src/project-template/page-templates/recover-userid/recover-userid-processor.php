<?php 

    #region Import libraries
        require_once("../Required.php");

        Required::Logger()
                ->Database()
                ->Validable()
                ->JSON();
    #endregion

	#region Class instance declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);
        $form = new Validable();
        $json = new JSON();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    #endregion

    #region Database connection
        $db->connect();
        $db->fetchAsObject();
	#endregion

    #region Form Validation
        try {
            $regYear= $form->label("Registration Year")->post("regYear")->required()->asNumeric()->maxLen(4)->validate();
            $regNo= $form->label("Registration No.")->post("regNo")->required()->asNumeric()->maxLen(10)->validate();
            $mobileNo = $form->label("Mobile No.")->post("mobileNo")->required()->asMobile()->validate();
        } catch (\ValidableException $vexp) {
            die($json->fail()->message($vexp->getMessage())->create());
        }
    #endregion

    $sql = "SELECT userId FROM lc_enrolment_cinfo WHERE regNo=:regNo AND regYear=:regYear AND mobileNo=:mobileNo";
    $cinfos = $db->select($sql, array('regNo' => $regNo, "regYear"=>$regYear, "mobileNo"=>$mobileNo)); 

    if(count($cinfos) != 1){
        die($json->fail()->message("Applicant not found.")->create());
    }

    $cinfo = $cinfos[0];
    $userId = $cinfo->userId;

    exit($json->success()->message("Your User ID is $userId")->create());
?>