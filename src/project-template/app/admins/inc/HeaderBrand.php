<?php
declare(strict_types=1);
class HeaderBrand{
    private static function includeHam(bool $condition){
        $html = "";
        if($condition){
            $html= <<<HTML
                  <div class="ham-menu-container">
                        <div  class="hamb" id="hambItem" style="display: block;">☰</div>
                        <div class="hamb" id="hambClose" style="display: none;">✕</div>
                    </div>
            HTML;
        }

        return $html;
    }

    /**
     * Create()
     * 
     * This method has dynamic argument(s).
     * 
     * Arguments- 1) str $baseUrl 2) bool $showHamburger
     */
    public static function prepare():string
    {
        $numberOfArguments = func_num_args();
        $arguments = func_get_args();
        $baseUrl = $arguments[0]; 
        $hamIcon = self::includeHam($arguments[1]);
        $html = <<<HTML
            <div class="brand-container">
                <div class="container-fluid ">
                    <div class="brand">
                        <img class="logo" src="$baseUrl/assets/images/bar-logo.png" alt="Bangladesh Govt. Logo">
                        <div style="flex:1; margin-left: 0.4rem;">
                            <div class="govt-name" >&nbsp;Government of the People's Republic of Bangladesh</div>
                            <div class="org">Bangladesh Bar Council</div>
                        </div>
                        $hamIcon
                    </div>
                </div>
            </div>
                        
        HTML;

        return $html;
    }
}
?>


