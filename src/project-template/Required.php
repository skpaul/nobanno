<?php
// namespace Nobanno;

$localHost =  "{$_SERVER['HTTP_HOST']}";//{$_SERVER['REQUEST_URI']}";

require_once("CONSTANTS.php");


class Required{

    #region Include Remote Libs
        public static $nobanno = '/vendor/nobanno/nobanno/src';

        public static function Logger(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Logger.php");
            return new static;
        }

        public static function Database(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Database.php");
            return new static;
        }

        public static function CSV(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/CSV.php");
            return new static;
        }

        public static function HttpHeader(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/HttpHeader.php");
            return new static;
        }
        public static function Cryptographer(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Cryptographer.php");
            return new static;
        }

        public static function DbSession(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/DbSession.php"); 
            return new static;   
        }


        public static function JSON(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/JSON.php");
            return new static;
        }

        public static function DataValidator(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/DataValidator.php"); 
            return new static;
        }

        public static function Taka(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Taka.php"); 
            return new static;
        }

        public static function With(){
            require_once(ROOT_DIRECTORY ."/With.php");
            return new static;
        }

        public static function Imaging(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Imaging.php");
            return new static;
        }

        public static function UniqueCodeGenerator(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/unique-code-generator/UniqueCodeGenerator.php");
            return new static;
        }

        public static function AgeCalculator(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/age-calculator/AgeCalculator.php"); //default version is now 2.
               
            return new static;
        }

        public static function Helpers(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/helpers/Helpers.php");
            return new static;
        }


        public static function Clock(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Clock.php");
            return new static;
        }

        public static function ExclusivePermission(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/exclusive-permission/ExclusivePermission.php");
            return new static;
        }
        public static function SmsSender(){
            // D:\xampp\htdocs\bar-council\lower-court\enrolment\lib\sms-sender\SmsSender.php
            require_once(ROOT_DIRECTORY . self::$nobanno . "/sms-sender/SmsSender.php");
            return new static;
        }

        public static function Greetings(){
            require_once(ROOT_DIRECTORY . self::$nobanno . "/Greetings.php");
            return new static;
        }

        /**
         * The Heredoc.php file can't be included this way.
         * It must be called directly from the file.
        */
        public static function Heredoc(){
            require_once(ROOT_DIRECTORY ."/lib/heredoc/Heredoc.php");
            return new static;
        }

        public static function DropDown(){
            require_once(ROOT_DIRECTORY ."/lib/dropdown/dropdown.php");
            return new static;
        }

        public static function RadioButton(){
            require_once(ROOT_DIRECTORY ."/lib/radiobutton/radiobutton.php");
            return new static;
        }

    #endregion


    #region Partial pages
        public static function gtag($version = null){
            require_once(ROOT_DIRECTORY . '/inc/gtag.html');
            return new static;
        }
        public static function metaTags($version = null){
            require_once(ROOT_DIRECTORY . '/inc/meta-tags.html');
            return new static;
        }

        public static function favicon($version = null){
            require_once(ROOT_DIRECTORY . '/inc/favicon.php');
            return new static;
        }

        public static function headerBrand($version = null){
            require_once(ROOT_DIRECTORY .  '/inc/HeaderBrand.php');
            return new static;
        }

        public static function applicantHeaderNav($version = null){
            require_once(ROOT_DIRECTORY . '/inc/ApplicantHeaderNav.php');
            return new static;
        }

        public static function leftNav($version = null){
            require_once(ROOT_DIRECTORY . '/inc/LeftNav.php');
            return new static;
        }

        public static function Footer($version = null){
            require_once(ROOT_DIRECTORY . '/inc/Footer.php'); //used in applicants panel
            return new static;
               
        }

    
    #endregion

    #region JavaScript
        public static function jquery($version = null){
            echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
            return new static;
        }

        public static function hamburgerMenu(){
            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/hamburger-menu@v1.0.0/hamburger-menu.js"></script>';
            
            return new static;
        }

        public static function html5shiv(){
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" integrity="sha512-UDJtJXfzfsiPPgnI5S1000FPLBHMhvzAMX15I+qG2E2OAzC9P1JzUwJOfnypXiOH7MRPaqzhPbBGDNNj7zBfoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
            return new static;
        }

        //moment is required for swift-submit.js
        public static function moment(){
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>';
            return new static;
        }

        public static function html2pdf(){
            echo '<script src="'.BASE_URL.'/assets/js/plugins/html2pdf/html2pdf.bundle.min.js"></script>';
            return new static;
        }

        public static function leftNavJS(){
            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/left-nav@1.0.0/left-nav.js"></script>';            
            return new static;
        }

        /**
         * swiftForm()
         * 
         * Prerequisites - mobileNumberParser() and moment();
         */
        public static function swiftSubmit(){
            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/swift-submit@2.0.0/swift-submit.min.js"></script>';
            return new static;
        }


        public static function multiStepForm(){
            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/multi-step-form@1.0.1/multi-step-form.min.js"></script>';
            return new static;
        }

    #endregion

    #region CSS
        public static function omnicss(){
            //Documentation: https://skpaul.github.io/omnicss/
            echo '<link href="https://cdn.jsdelivr.net/gh/skpaul/omnicss@0.1.3/omnicss.min.css" rel="stylesheet">';
            return new static;
        }
        public static function griddle(){
            echo ' <link href="https://cdn.jsdelivr.net/gh/skpaul/griddle@0.0.3/griddle.min.css"  rel="stylesheet">';
            return new static;
        }

        public static function bootstrapGrid($version = null){
            echo ' <link href="'.BASE_URL.'/assets/css/bootstrap-grid-v5.1.3..css" rel="stylesheet">';
            return new static;
        }
    #endregion




    #region OverlayScrollbar Plugin
        public static function overlayScrollbarCSS($version = null){
            echo '<link rel="stylesheet" href="'.BASE_URL.'/assets/js/plugins/OverlayScrollbars/css/OverlayScrollbars.min.css">';
            echo '<link rel="stylesheet" href="'.BASE_URL.'/assets/js/plugins/OverlayScrollbars/css/os-theme-round-light.css">';

            return new static;
        }

        public static function overlayScrollbarJS($version = null){
            echo '<script src="'.BASE_URL.'/assets/js/plugins/OverlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>';
            /*
                //How to use----------------------
                $(document).ready(function(){   
                    $('.overlayScroll').overlayScrollbars({
                        className: 'os-theme-round-light',
                        scrollbars : {
                            visibility: "auto", 
                            autoHide: 'leave',
                            autoHideDelay: 100
                        }                    
                    });
                });
            */

            return new static;
        }

    #endregion


    public static function sweetModalJS(){
        echo '<script src="'.BASE_URL.'/assets/plugins/jquery.sweet-modal-1.3.3/jquery.sweet-modal.min.js"></script>';
        return new static;
    }

    public static function sweetModalCSS(){
        echo '<link rel="stylesheet" href="'.BASE_URL.'/assets/plugins/jquery.sweet-modal-1.3.3/jquery.sweet-modal.min.css">';
        return new static;
    }

   
    //requried for swiftSubmit and swiftChanger
    public static function mobileValidator(){
        echo '<script src="'.BASE_URL.'/assets/js/plugins/mobile-number-validator/mobile-number-validator.js"></script>';
        return new static;
    }

    public static function swiftNumeric(){
        echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/swift-numeric@1.0.0/swift-numeric.js"></script>';
        return new static;
    }

    /**
     * airDatePicker()
     * 
     * Includes necessary css and js.
     */
    public static function airDatePicker(){
        echo '<link href="'.BASE_URL.'/assets/plugins/air-datepicker/css/datepicker.min.css" rel="stylesheet">';

        echo '<script src="'.BASE_URL.'/assets/plugins/air-datepicker/js/datepicker.min.js"></script>';
        // <!-- Include English language -->
        echo '<script src="'.BASE_URL.'/assets/plugins/air-datepicker/js/i18n/datepicker.en.js"></script>';
        return new static;
    }


    public static function airDatePickerJS(){
        echo '<script src="'.BASE_URL.'/assets/plugins/air-datepicker/js/datepicker.min.js"></script>';
        // <!-- Include English language -->
        echo '<script src="'.BASE_URL.'/assets/plugins/air-datepicker/js/i18n/datepicker.en.js"></script>';
        return new static;
    }

    public static function airDatePickerCSS(){
        echo '<link href="'.BASE_URL.'/assets/plugins/air-datepicker/css/datepicker.min.css" rel="stylesheet">';
        return new static;
    }
}

?>