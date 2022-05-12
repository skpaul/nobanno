<?php
declare(strict_types=1);
class Footer{
    /**
     * prepare()
     * 
     * This method has dynamic argument(s).
     * 
     * Arguments- 1) str Base URL 2) bool showHamburger
     */
    public static function prepare():string
    {
        $numberOfArguments = func_num_args();
        $arguments = func_get_args();
        // $baseUrl = $arguments[0]; 
       
        $html = <<<HTML
            <div class="footer-container">
                <div class="container">
                    <div class="divider"></div>
                    <div class="footer-content-wrapper">
                        <div class="copyright">©2022, Bangladesh Bar Council, All Rights Reserved.</div>
                        <img class="footer-logo" alt="teletalk Logo" title="Powered By: Teletalk" src="http://demo.bar.teletalk.com.bd/bar/lower-court/enrolment/assets/images/teletalk-logo.png">
                        <div class="powered-by">Powered By: Teletalk Bangladesh Ltd. </div>
                    </div>
                </div>
            </div>
        HTML;

        return $html;
    }
}
?>




