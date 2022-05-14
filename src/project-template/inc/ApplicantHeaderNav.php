<?php
declare(strict_types=1);
class ApplicantHeaderNav{
    /**
     * prepare()
     * 
     * This method has dynamic argument(s).
     * 
     * Arguments- 1) str Base URL
     */
    public static function prepare(array $params):string
    {
        $numberOfArguments = func_num_args();
        $arguments = func_get_args();
        $baseUrl =  $params["baseUrl"]; 
        $html = <<<HTML
                <div class="main-top-nav-container">
                    <div class="container">
                        <nav id="mainTopNav" class="main-top-nav">
                            <a href="$baseUrl/index.php"><span class="m-icons">home</span>Home</a>    
                            <a href="$baseUrl/app/applicant-copy/applicant-copy-redownload.php"><span class="m-icons">article</span>Applicant Copy</a>
                            <a href="$baseUrl/app/court-higher/written/admit-card/hc-written-admit-card.php"><span class="m-icons">account_box</span>Admit Card</a>
                            <a href="$baseUrl/app/payment-status/payment-status.php"><span class="m-icons">done_outline</span>Payment Status</a>
                            <a href="$baseUrl/app/recover-userid/recover-userid.php"><span class="m-icons">perm_identity</span>Recover User ID</a>
                            <a href="https://www.photobip.com" target="_blank"><span class="m-icons">crop</span>Photo Resizer</a>
                            <a href="$baseUrl/app/help/customer-care.php"><span class="m-icons">phone_in_talk</span>Customer Care</a>
                        </nav> 
                    </div>
                </div>
        HTML;

        return $html;
    }
}
?>


<!-- <div class="brand-container">
    <div class="container-fluid ">
        <div class="brand">
            <img class="logo" src="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/assets/images/bar-logo.png" alt="Bangladesh Govt. Logo">
            <div style="flex:1; margin-left: 0.4rem;">
                <div class="govt-name" >&nbsp;Government of the People's Republic of Bangladesh</div>
                <div class="org">Bangladesh Judicial Service Commissin</div>
            </div>
            <div class="ham-menu-container">
                <div  class="hamb" id="hambItem" style="display: block;">☰</div>
                <div class="hamb" id="hambClose" style="display: none;">✕</div>
            </div>
        </div>
    </div>
</div>
           
<div class="main-top-nav-container">
    <div class="container-fluid">
        <nav id="mainTopNav" class="main-top-nav">
            <a href="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/index.php"><span class="m-icons">home</span>Home</a>    
            <a href="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/applicant-copy/applicant-copy.php"><span class="m-icons">article</span>Applicant Copy</a>
            <a href="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/court-higher/written/admit-card/hc-written-admit-card.php"><span class="m-icons">account_box</span>Admit Card</a>
            <a href="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/payment-status/payment-status.php"><span class="m-icons">done_outline</span>Payment Status</a>
            <a href="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/recover-userid/recover-userid.php"><span class="m-icons">perm_identity</span>Recover User ID</a>
            <a href="https://www.photobip.com" target="_blank"><span class="m-icons">crop</span>Photo Resizer</a>
            <a href="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/help/customer-care.php"><span class="m-icons">phone_in_talk</span>Help</a>
        </nav> 
    </div>
</div> -->

