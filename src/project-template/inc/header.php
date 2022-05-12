<?php
    function prepareHeader($organizationFullName = ""){
        // $numberOfArguments = func_num_args();
        // $arguments = func_get_args();
        // $isLoggedIn = $arguments[0];
        // $queryString = "";
        // if($numberOfArguments>1)
        //     $queryString = "?".  $arguments[1];
            
        // $homeUrl = sprintf("%sindex.php%s",BASE_URL, $queryString);
        $homeUrl = BASE_URL . "/index.php";
        $menu = 
        <<<HTML
            <li><a href="$homeUrl">Home</a></li>
        HTML;
        
        $applicantCopyUrl =  BASE_URL . "/applicant-copy/applicant-copy.php";
        $admitCardUrl =  BASE_URL . "/admit-card/select-application.php";
        $paymentStatusUrl = BASE_URL . "/payment-status/payment-status.php";
        $recoverUserIdUrl = BASE_URL . "/recover-userid/recover-userid.php";
        $noticeBoardUrl = BASE_URL . "/notice-board/notice-board.php";
        $customerCareUrl = BASE_URL . "/customer-care/customer-care.php";
        $correctionUrl = BASE_URL . "/registration-correction/registration-userid.php";

        
        $menu .= 
        <<<HTML
            <li><a href="$applicantCopyUrl">Applicant Copy</a></li>
            <li><a href="$correctionUrl">Registration Correction</a></li>
            <li><a href="$admitCardUrl">Admit Card</a></li>
            <li><a href="$paymentStatusUrl">Payment Status</a></li>
            <li><a href="$recoverUserIdUrl">Recover User ID</a></li>
            <li><a href="https://www.photobip.com" target="_blank">Photo Resizer</a></li>
            <li><a href="$noticeBoardUrl">Notices</a></li>
            <li><a href="$customerCareUrl">Customer Care</a></li>
           
        HTML;
      
        $logoSrc = sprintf("%s/assets/images/bar-logo.png", BASE_URL);
        $hamburgerSrc = sprintf("%s/assets/images/hamburger-1.png", BASE_URL);

        $html = 
            <<<HTML
                    <div class="top">
                        <div class="left">
                            <div class="brand">
                                <div class="logo-container">
                                    <img class="logo" src="$logoSrc" alt="Bangladesh Govt. Logo">
                                </div>
                                <div class="govt-org">
                                    <div class="govt">Government of the People's Republic of Bangladesh</div>
                                    <div class="organization" >$organizationFullName</div>
                                </div>
                            </div>         
                        </div>
                        <div class="right mobile-menu">
                            <div class="hamburger-icon-container">
                            <a href="javascript:void(0);" class="icon" onclick="hamburgerMenu()">  
                                <img class="hamburger" src="$hamburgerSrc" alt="Mobile Menu">
                            </a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="nav-container" id="nav-container">
                            <nav class="top-nav">
                                <ul>
                                    $menu
                                </ul>
                            </nav> 
                        </div>
                    </div>
            HTML;

        return $html;
    }
?>

