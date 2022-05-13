<?php
// namespace Nobanno;

$localHost =  "{$_SERVER['HTTP_HOST']}";//{$_SERVER['REQUEST_URI']}";

require_once("CONSTANTS.php");


class Required{

    public static $path = '/vendor/nobanno/nobanno/src';

    #region Library

        public static function Logger(){
            require_once(ROOT_DIRECTORY . self::$path . "/Logger.php");
            return new static;
        }

        public static function Database(){
            require_once(ROOT_DIRECTORY . self::$path . "/Database.php");
            return new static;
        }

        public static function CSV(){
            require_once(ROOT_DIRECTORY . self::$path . "/CSV.php");
            return new static;
        }

        public static function HttpHeader(){
            require_once(ROOT_DIRECTORY . self::$path . "/http-header/HttpHeader.php");
            return new static;
        }
        public static function Cryptographer(){
            require_once(ROOT_DIRECTORY . self::$path . "/Cryptographer.php");
            return new static;
        }

        public static function DbSession(){
            require_once(ROOT_DIRECTORY . self::$path . "/session/DbSession.php"); 
            return new static;
            
        }

        public static function JSON(){
            require_once(ROOT_DIRECTORY . self::$path . "/JSON.php");
            return new static;
        }

        public static function Validable(){
            require_once(ROOT_DIRECTORY . self::$path . "/Validable.php"); 
            return new static;
        }

        public static function Taka(){
            require_once(ROOT_DIRECTORY . self::$path . "/Taka.php"); 
            return new static;
        }

        public static function With($version = null){
            require_once(ROOT_DIRECTORY ."/lib/with/With.php");
            return new static;
        }

        public static function Imaging($version = null){
            require_once(ROOT_DIRECTORY . self::$path . "/image/Imaging.php");
            return new static;
        }

        public static function UniqueCodeGenerator($version = null){
            require_once(ROOT_DIRECTORY . self::$path . "/unique-code-generator/UniqueCodeGenerator.php");
            return new static;
        }

        public static function AgeCalculator(){
            require_once(ROOT_DIRECTORY . self::$path . "/age-calculator/AgeCalculator.php"); //default version is now 2.
               
            return new static;
        }

        public static function Helpers(){
            require_once(ROOT_DIRECTORY . self::$path . "/helpers/Helpers.php");
            return new static;
        }


        public static function Clock(){
            require_once(ROOT_DIRECTORY . self::$path . "/Clock.php");
            return new static;
        }

        public static function ExclusivePermission(){
            require_once(ROOT_DIRECTORY . self::$path . "/exclusive-permission/ExclusivePermission.php");
            return new static;
        }
        public static function SmsSender(){
            // D:\xampp\htdocs\bar-council\lower-court\enrolment\lib\sms-sender\SmsSender.php
            require_once(ROOT_DIRECTORY . self::$path . "/sms-sender/SmsSender.php");
            return new static;
        }

            /**
         * The Heredoc.php file can't be included this way.
         * It must be called directly from the file.
        */
        public static function Heredoc($version = null){
            require_once(ROOT_DIRECTORY ."/lib/heredoc/Heredoc.php");
            return new static;
        }

        public static function DropDown($version = null){
            require_once(ROOT_DIRECTORY ."/lib/dropdown/dropdown.php");
            return new static;
        }

        public static function RadioButton($version = null){
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

        public static function footer($version = null){
            require_once(ROOT_DIRECTORY . '/inc/Footer.php'); //used in applicants panel
            return new static;
               
        }
    #endregion

    public static function html5shiv(){
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" integrity="sha512-UDJtJXfzfsiPPgnI5S1000FPLBHMhvzAMX15I+qG2E2OAzC9P1JzUwJOfnypXiOH7MRPaqzhPbBGDNNj7zBfoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
        return new static;
    }

    public static function omnicss(){
        echo '<link href="https://cdn.jsdelivr.net/gh/skpaul/omnicss/omnicss.min.css" rel="stylesheet">';
        return new static;
    }
    public static function monogrid(){
        echo '<link href="https://cdn.jsdelivr.net/gh/skpaul/monogrid@0.0.1/monogrid.min.css">';
        return new static;
    }
    public static function lightTheme(){
        echo '<link href="https://cdn.jsdelivr.net/gh/winbip/winbip-css@1.0.0/theme-light.css" rel="stylesheet">';
        return new static;
    }

    public static function bootstrapGrid($version = null){
        echo ' <link href="'.BASE_URL.'/assets/css/bootstrap-grid-v5.1.3..css" rel="stylesheet">';
        return new static;
    }

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

    #region JQuery and JavaScript
        public static function jquery($version = null){
            echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
            return new static;
        }

        public static function hamburgerMenu(){
            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/hamburger-menu@v1.0.0/hamburger-menu.js"></script>';
            
            return new static;
        }

        /**
         * @deprecated
         *
         * @return string 
         */
        public static function adminLeftNavJS(){
            trigger_error('Method "' . __METHOD__ . '()" is deprecated, use "leftNavJS()" instead.', E_USER_DEPRECATED); //E_USER_NOTICE

            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/left-nav@1.0.0/left-nav.js"></script>';            
            return new static;
        }
    
        public static function leftNavJS(){
            echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/left-nav@1.0.0/left-nav.js"></script>';            
            return new static;
        }

     #endregion

    public static function sweetModalJS(){
        echo '<script src="'.BASE_URL.'/assets/js/plugins/jquery.sweet-modal-1.3.3/jquery.sweet-modal.min.js"></script>';
        return new static;
    }

    public static function sweetModalCSS(){
        echo '<link rel="stylesheet" href="'.BASE_URL.'/assets/js/plugins/jquery.sweet-modal-1.3.3/jquery.sweet-modal.min.css">';
        return new static;
    }

    //mobileNumberParser is required for swift-submit.js
    // public static function mobileNumberParser(){
    //     echo '<script src="'.BASE_URL.'/assets/js/mobile_number_parser.js"></script>';
    //     return new static;
    // }
    

    //moment is required for swift-submit.js
    public static function moment(){
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>';
        return new static;
    }

    //requried for swiftSubmit and swiftChanger
    public static function mobileValidator(){
        echo '<script src="'.BASE_URL.'/assets/js/plugins/mobile-number-validator/mobile-number-validator.js"></script>';
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

    /**
     * @deprecated
     *
     * @return string 
     */
    public static function swiftChanger(){
        trigger_error('Method "' . __METHOD__ . '()" is deprecated, use "multiStepForm()" instead.', E_USER_DEPRECATED); //E_USER_NOTICE
        echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/multi-step-form@1.0.1/multi-step-form.min.js"></script>';
        return new static;
    }

    public static function multiStepForm(){
        echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/multi-step-form@1.0.1/multi-step-form.min.js"></script>';
        return new static;
    }

    public static function swiftNumeric(){
        echo '<script src="https://cdn.jsdelivr.net/gh/skpaul/swift-numeric@1.0.0/swift-numeric.js"></script>';
        return new static;
    }

    
    public static function html2pdf(){
        echo '<script src="'.BASE_URL.'/assets/js/plugins/html2pdf/html2pdf.bundle.min.js"></script>';
        return new static;
    }

    public static function airDatePickerJS(){
        echo '<script src="'.BASE_URL.'/assets/js/plugins/air-datepicker/js/datepicker.min.js"></script>';
        // <!-- Include English language -->
        echo '<script src="'.BASE_URL.'/assets/js/plugins/air-datepicker/js/i18n/datepicker.en.js"></script>';
        return new static;
    }

    public static function airDatePickerCSS(){
        echo '<link href="'.BASE_URL.'/assets/js/plugins/air-datepicker/css/datepicker.min.css" rel="stylesheet">';
        return new static;
    }
    
  
}

?>