<?php
    //Must add SLASH(/) after this constant i.e.  require_once(ROOT_DIRECTORY . '/db_connect.php');
    defined("ROOT_DIRECTORY")
    or define("ROOT_DIRECTORY", realpath(dirname(__FILE__)));
    //$example = ROOT_DIRECTORY . "/applicant_photo/" . "$gender" . "/" . $post_code . "/" . $userid. ".jpg";


    //Must add SLASH(/) after this constant i.e. require_once(LIBRARY_PATH .'/form.php');
    //defined("LIBRARY_PATH") or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));

    defined("BASE_URL") or define("BASE_URL", "http://localhost/bar-council/lower-court/enrolment"); 
    defined("DB_SERVER") or define("DB_SERVER", "localhost");
    defined("DB_USER") or define("DB_USER", "root");
    defined("DB_PASSWORD") or define("DB_PASSWORD", "");
    defined("DB_NAME") or define("DB_NAME", "bar_council");
    defined("ENVIRONMENT") or define("ENVIRONMENT", "DEVELOPMENT"); //DEVELOPMENT  //PRODUCTION
    defined("COURT") or define("COURT", "Lower Court"); //Lower Court  //Higher Court
    defined("APPLICATION_TYPE") or define("APPLICATION_TYPE", "Enrolment"); //Enrolment  //Written   //Viva   //Etc.

    defined("ORGANIZATION_SHORT_NAME") or define("ORGANIZATION_SHORT_NAME", "Bangladesh Bar Council");
    defined("ORGANIZATION_FULL_NAME") or define("ORGANIZATION_FULL_NAME", "Bangladesh Bar Council");

?>
