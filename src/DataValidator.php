
<?php 

    /**
     * Last modified on 18-04-2020
     */

     
    class ValidationException extends Exception
    {
    }

    class DataValidator{

        #region private variables
        
            private $label = "";

            private $requiredLang =  "উল্লেখ করুন";    //"উল্লেখ করুন" "required."
            private $invalidLang = "সঠিক নয়";        //"সঠিক নয়" "incorrect."
            private $foundLang = "পাওয়া গেছে";            //"পাওয়া গেছে"  "found."

            /**
             * Here we keep the value we going to be validated.
             *
             * @var mix $valueToValidate
             */
            private $valueToValidate = null;

            /**
             * Here we keep the default value of the data.
             *
             * @var mix $defaultValue
             */
            private $defaultValue;

            private $required = false;
            
            /**
             * @var string $character_or_digit
             * 
             * It's value can be either "digits" or "characters".
             * 
             * This variable is required to compose a meaningful message while throwing ValidationException.
             */
            private $character_or_digit = "";

        #endregion

        #region construct and destruct
            public function __construct() {}

            public function __destruct(){}
        #endregion

        #region Receive value to validate

            /**
             * label()
             * 
             * Sets the description of the value. Example- 'Customer Name' or 'Date of Birth'.
             * Similar to HTML <label></label> tag.
             * 
             * It is required to compose a meaningful message if validation fails.
             *
             * @param string $label
             *
             * @return this
             */
            public function label($label){
                $this->label = trim($label);
                return $this;
            }

            /**
             * title()
             * 
             * Sets the description of the value. Example- 'Customer Name' or 'Date of Birth'.
             * 
             * It is required to compose a meaningful message if validation fails.
             *
             * @param string $title
             *
             * @return this
             */
            public function title($title){
                $this->label = trim($title);
                return $this;
            }


            /**
             * value()
             * 
             * Receive value manually.
             * 
             * Useful if value does not come from HTTP POST/GET.
             *
             * @param string $value
             *
             * @return this
             */
            public function value($value){
                $this->valueToValidate = trim($value);
                return $this;
            }

            /**
             * post()
             * 
             * Receive value from HTTP POST.
             *
             * @param string $httpPostFieldName
             *
             * @return this
             */
            public function post($httpPostFieldName){
                if(isset($_POST[$httpPostFieldName])){
                    $value = trim($_POST[$httpPostFieldName]);
                    $this->valueToValidate = $value;
                }
                else{
                    unset($this->valueToValidate);
                }
                return $this;
            }
        
            
            /**
             * httpGet()
             * 
             * Receive value from HTTP GET.
             *
             * @param string $httpPostFieldName
             *
             * @return this
             */
            public function get($httpGetFieldName){
                if(isset($_GET[$httpGetFieldName])){
                    $this->valueToValidate = trim($_GET[$httpGetFieldName]);
                }
                return $this;
            }

            /**
             * default()
             * 
             * If the user input is optional, this method is required to set data for database table.
             * 
             * If the user input is mandatory, no need to use this method.
             * 
             * @param mix $defaultValue
             * 
             * @return this. 
             */
            public function default($defaultValue){
                $this->defaultValue = $defaultValue;
                return $this;
            }
        #endregion

        #region Sanitize

            /**
             * sanitize()
             * 
             * It removes HTML & PHP tags, backslashes(\) and HTML special characters
             * 
             * @param bool $removeTags - whether remove tags or not
             * @param bool $removeSlash - whether remove backslashes or not
             * @param bool $convert - whether convert HTML special characters
             * 
             * @return this $this
             */
            public function sanitize($removeTags = true, $removeSlash = true, $convertHtmlSpecialChars = true){
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    $valueToValidate = $this->valueToValidate;

                    if($removeTags){
                        $valueToValidate = $this->_strip_tags($valueToValidate, null);
                    }
        
                    if($removeSlash){
                        $valueToValidate = $this->_removeSlash($valueToValidate);
                    }
        
                    if($convertHtmlSpecialChars){
                        $valueToValidate = $this->_convert($valueToValidate);
                    }

                    $this->valueToValidate = $valueToValidate ;
                }
                return $this;
            }

            /**
             * removeTags()
             * 
             * Remove HTML and PHP tags from a string.
             * 
             * You can use the optional parameter to specify tags which should not be removed. 
             * These are either given as string, or as of PHP 7.4.0, as array.
             * 
             * @param mixed $allowableTags
             * 
             * @return this $this
             */
            public function removeTags($allowableTags = null){
                $this->valueToValidate = $this->_strip_tags($this->valueToValidate, $allowableTags); 
                return $this;
            }

            //Called from removeTags() and sanitize()
            private function _strip_tags($valueToValidate, $allowableTags){
                //strip_tags() - Strip HTML and PHP tags from a string

                if(isset($allowableTags) && !empty($allowableTags)){
                    $valueToValidate = strip_tags($valueToValidate, $allowableTags); 
                }
                else{
                    $valueToValidate = strip_tags($valueToValidate); 
                }

                return $valueToValidate;
            }

            /**
             * removeSlash()
             * 
             * Remove the backslash (\) from a string.
             * Example: "how\'s going on?" = "how's going on?"
             * 
             */
            public function removeSlash(){
                //The following cascading variables used for making the debugging easy.
                $valueToValidate = $this->valueToValidate ;
                $valueToValidate = $this->_removeSlash($valueToValidate); 
                $this->valueToValidate = $valueToValidate;
                return $this;
            }

            private function _removeSlash($valueToValidate){
                /* 
                    Example 
                    $text="My dog don\\\\\\\\\\\\\\\\'t like the postman!";
                    echo removeslashes($text);
                    RESULT: My dog don't like the postman!
                */

                $temp = implode("", explode("\\", $valueToValidate));
                $valueToValidate = stripslashes(trim($temp));
                return $valueToValidate;
            }

            /**
             * convert()
             * 
             * Convert special characters to HTML entities
             * 
             * Example: htmlspecialchars("<br> Here") = &lt;br&gt; Here
             * 
             * NOTE: If you use this method, you should use 'htmlspecialchars_decode()' to show back the original data.
             * 
             * @param bool $convertDoubleQuote - whether convert double quote
             * @param bool $convertSingleQuote - whether convert single quote
             */
            public function convert($convertDoubleQuote, $convertSingleQuote){

                $flag = ENT_QUOTES; //ENT_QUOTES	Will convert both double and single quotes.

                if($convertDoubleQuote && !$convertSingleQuote){
                    $flag = ENT_COMPAT;
                }
                elseif(!$convertDoubleQuote && !$convertSingleQuote){
                    $flag = ENT_NOQUOTES;
                }
                else{
                    $flag = ENT_QUOTES;
                }

                /*
                    ENT_COMPAT	Will convert double-quotes and leave single-quotes alone.
                    ENT_QUOTES	Will convert both double and single quotes.
                    ENT_NOQUOTES	Will leave both double and single quotes unconverted.
                */

                $valueToValidate = $this->valueToValidate;
                $valueToValidate = $this->_convert($valueToValidate, $flag);  // Converts both double and single quotes
                $this->valueToValidate = $valueToValidate ;
                
                return $this;
            }
            
            private function _convert($valueToValidate, $flag = ENT_QUOTES){

                /*
                    htmlentities — Convert all applicable characters to HTML entities.
                    htmlspecialchars — Convert special characters to HTML entities.
                    Source- https://stackoverflow.com/questions/46483/htmlentities-vs-htmlspecialchars/3614344
                */

                //However, if you also have additional characters that are Unicode or uncommon symbols in your text then you should use htmlentities() to ensure they show up properly in your HTML page.

                /*
                    ENT_COMPAT	Will convert double-quotes and leave single-quotes alone.
                    ENT_QUOTES	Will convert both double and single quotes.
                    ENT_NOQUOTES	Will leave both double and single quotes unconverted.
                */
                $valueToValidate = htmlspecialchars($valueToValidate, $flag); 

                //There is a bug, therefore use that function twice
                $valueToValidate = htmlspecialchars($valueToValidate, $flag); 

                return $valueToValidate;
            }
        #endregion

        #region Required and Optional
        
            /**
             * optional()
             * 
             * The opposite of required()
             * This method is not required to call.
             * Because the value is optional by default.
             * 
             * @return this @this
             */
            public function optional(){
                $this->required = false;
                return $this;
            }

            /**
             * required()
             * 
             * Checks whether current value is required or optional.
             * 
             * @return $this
             * 
             * @throws ValidationException
             */
            public function required(){
                $this->required = true;
                if(!isset($this->valueToValidate)){
                    throw new ValidationException("{$this->label} {$this->requiredLang}");
                }
                else{
                    if(empty($this->valueToValidate)){
                        throw new ValidationException("{$this->label} {$this->requiredLang}");
                    }
                }
                return $this;
            }

        #endregion

        #region Language check
            /**
             * englishOnly()
             * 
             * It allows only english language.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function englishOnly(){
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if (!preg_match('/[^A-Za-z0-9]/', strval($this->valueToValidate)))  {
                        //string contains only letters from the English alphabet
                        throw new ValidationException("{$this->label} must be in english.");
                    }
                }
                
                return $this;
            }
        #endregion

        #region Check for data type
            /**
             * asAscii()
             * 
             * It allows only english language.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asAscii(){
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if(!is_numeric($this->valueToValidate)){
                        throw new ValidationException("{$this->label} must be numeric.");
                    }
                }
                
                return $this;
            }
            
            /**
             * asAlphabetic()
             * 
             * It allows only A-Z/a-z.
             * 
             * @param bool @allowSpace - sets whether allow space in the value.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asAlphabetic($allowSpace){
                $this->character_or_digit = "characters"; //"অক্ষর";
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if($allowSpace){
                        //if allow space, then remove spaces before applying ctype_alpha.
                        $temp = str_replace(" ", "", strval($this->valueToValidate));
                    }
                    else{
                        if($this->_hasWhitespace($this->valueToValidate)){
                            throw new ValidationException("{$this->label} required");
                        }
                        $temp = $this->valueToValidate;
                    }

                    if(!ctype_alpha($temp)){
                        throw new ValidationException("{$this->label} incorrect. It must be alphabatic (A-Z, a-z).");
                    }
                }
                return $this;
            }
            
            /**
             * asNumeric()
             * 
             * Allows numbers only.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asNumeric(){
                $this->character_or_digit = "digits"; //"সংখ্যা";
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if(!is_numeric($this->valueToValidate)){
                        throw new ValidationException("{$this->label} must be numeric.");
                    }
                }
                
                return $this;
            }

            /**
             * asAlphaNumeric()
             * 
             * Check for characters which are either letters or numbers.
             * It allows only A-Z, a-z and 0-9.
             * 
             * @param boolean $allowSpace
             * @return this $this
             * @throws ValidationException
             */
            public function asAlphaNumeric($allowSpace){
                $this->character_or_digit = "characters"; //"অক্ষর";
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if($allowSpace){
                        //if allow space, then remove spaces before applying ctype_alpha.
                        $temp = str_replace(" ", "", strval($this->valueToValidate));
                    }
                    else{
                        $temp = $this->valueToValidate;
                    }

                    if(!ctype_alnum($temp)){
                        throw new ValidationException("{$this->label} a-z/A-Z এবং/অথবা 0-9 হতে হবে।");
                    }
                }
                
                return $this;
            }

            /**
             * asString()
             * 
             * Allows all alphabets/letters/arithmatic signs/special characters.
             * 
             * @param bool @allowSpace - sets whether allow space in the value.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asString($allowSpace){
                $this->character_or_digit = "characters"; //"অক্ষর";
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if(!$allowSpace){
                        if($this->_hasWhitespace($this->valueToValidate)){
                            throw new ValidationException("{$this->label} invalid. Blank space is not allowed.");
                        }
                    }
                }
                return $this;
            }

            /**
             * Checks string for whitespace characters.
             *
             * @param string $text
             *   The string to test.
             * @return bool
             *   TRUE if any character creates some sort of whitespace; otherwise, FALSE.
             */
            private function _hasWhitespace( $text )
            {
                for ( $idx = 0; $idx < strlen( $text ); $idx += 1 )
                    if ( ctype_space( $text[ $idx ] ) )
                        return TRUE;

                return FALSE;
            }

            /**
             * asInteger()
             * 
             * Value must be of integer type.
             *  
             * Parameter can be "1001" or 1001.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asInteger($allowNegative){
                $this->character_or_digit = "digits"; //"সংখ্যা";
            
                $valueToValidate = $this->valueToValidate;
                if(isset($valueToValidate) && !empty($valueToValidate)){
                    $valueToValidate = str_replace(",","", strval($valueToValidate));
                    //it allows negative value, but not decimal value.
                    if(filter_var($valueToValidate, FILTER_VALIDATE_INT) === 0 || filter_var($valueToValidate, FILTER_VALIDATE_INT)){
                        if (!$allowNegative) {
                            $valueToValidate = intval($valueToValidate);
                            if ($valueToValidate < 0) {
                                throw new ValidationException("{$this->label} {$this->invalidLang}");
                            }
                        }
                    }
                    else{
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }

                    $this->valueToValidate = $valueToValidate;
                }
                else{
                    if($this->required ){
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                }
                
                return $this;
            }

        
            /**
             * asFloat()
             * 
             * Value must be of float type.
             * 
             * Parameter can be "1.001" or 1.001
             * 
             * @return $this
             * 
             * @throws ValidationException
             */
            public function asFloat($allowNegative){
                //check whether has a decimal point.
                //if has a decimal point, then check it with is_float().
                //if no decimal point, then check it with is_int().
                //finally return with floatval.

                $this->character_or_digit = "digits"; //"সংখ্যা";
                $valueToValidate = $this->valueToValidate;
                if($this->_has_decimal($valueToValidate)){
                    $valueToValidate = str_replace(",","",strval($valueToValidate));
                    if(filter_var($valueToValidate, FILTER_VALIDATE_FLOAT) === 0 || filter_var($valueToValidate, FILTER_VALIDATE_FLOAT)){
                        if (!$allowNegative) {
                            $valueToValidate = floatval($valueToValidate);
                            if ($valueToValidate < 0) {
                                throw new ValidationException("{$this->label} {$this->invalidLang}.");
                            }
                        }
                    }
                    else{
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                }
                else{
                    $valueToValidate = str_replace(",","",strval($valueToValidate));
                    if(filter_var($valueToValidate, FILTER_VALIDATE_INT) === 0 || filter_var($valueToValidate, FILTER_VALIDATE_INT)){
                        if (!$allowNegative) {
                            $valueToValidate = intval($valueToValidate);
                            if ($valueToValidate < 0) {
                                throw new ValidationException("{$this->label} {$this->invalidLang}");
                            }
                        }
                    }
                    else{
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                }


                $this->valueToValidate = $valueToValidate;
            
                return $this;
            }
        
            //It counts the digits after a decimal point.
            //i.e. 
            private function _count_decimal_value($required_digits){
                $arr = explode('.', strval($this->valueToValidate));
                if(strlen($arr[1]) == $required_digits){
                    return true;
                }
                else{
                    return false;
                }
            }
    
            private function _has_decimal($number){
                $count = substr_count(strval($number), '.');
                if($count == 1){
                    return true;
                }
                else{
                    return false;
                }
            }

            /**
             * asEmail()
             * 
             * Value must be a valid email address.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asEmail(){
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    // $label = $this->label;
                    if (!filter_var($this->valueToValidate, FILTER_VALIDATE_EMAIL)) {
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                }
                return $this;
            }

            /**
             * asMobile()
             * 
             * Checks whether a mobile number is valid.
             * 
             * It produces a valid mobile mobile with "880" prefix.
             * 
             * @return this $this
             * 
             * @throws ValidationException.
             */
            public function asMobile(){
                $MobileNumber = $this->valueToValidate;
            
                if(empty($MobileNumber)){
                    throw new ValidationException("{$this->label} {$this->invalidLang}");
                }
            
                if(!is_numeric($MobileNumber)){
                    throw new ValidationException("{$this->label} {$this->invalidLang}");
                }
            
                if(strlen($MobileNumber)<10){
                    throw new ValidationException("{$this->label} {$this->invalidLang}");
                }
            
                $OperatorCodes = array( "013", "014", "015", "016", "017", "018", "019" );
                
                if($this->_starts_with($MobileNumber,"1")){
                    //if the number is 1711781878, it's length must be 10 digits        
                    if(strlen($MobileNumber) != 10){
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
            
                    $firstTwoDigits = substr($MobileNumber, 0, 2); //returns 17, 18 etc,
                    $operatorCode = "0" . $firstTwoDigits; //Making first two digits a valid operator code with adding 0.
            
                    if (!in_array($operatorCode, $OperatorCodes)) {
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
            
                    $finalNumberString = "880" . $MobileNumber;
                
                    $this->valueToValidate = $finalNumberString;
                    return $this;
                }
                
                if($this->_starts_with($MobileNumber,"01")){
                    //if the number is 01711781878, it's length must be 11 digits        
                    if(strlen($MobileNumber) != 11){
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
            
                    $operatorCode = substr($MobileNumber, 0, 3); //returns 017, 018 etc,
                    
                    if (!in_array($operatorCode, $OperatorCodes)) {
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
            
                    $finalNumberString = "88" . $MobileNumber;
                    $this->valueToValidate = $finalNumberString;
                    return $this;
                }
            
                if($this->_starts_with($MobileNumber,"8801")){
                    //if the number is 8801711781878, it's length must be 13 digits    
                    if(strlen($MobileNumber) != 13){
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
            
                    $operatorCode = substr($MobileNumber, 2, 3); //returns 017, 018 etc,
                    
                    if (!in_array($operatorCode, $OperatorCodes)) {
                        $this->is_valid = false;
                        return false;
                    }        
            
                
                    $this->valueToValidate = $MobileNumber;
                    return $this;
                }
            
                throw new ValidationException("{$this->label} {$this->invalidLang}");
            }

            /**
             * asDate()
             * 
             * Checks whether the value is a valid date/datetime
             * Convert the value as datetime object.
             * 
             * @param string $datetimeZone Default is "Asia/Dhaka".
             * @throws ValidationException if the value is invalid.
             * 
             * @return this $this
             */
            public function asDate($datetimeZone = "Asia/Dhaka"){
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    // $pattern =   '~^(((0[1-9]|[12]\\d|3[01])\\-(0[13578]|1[02])\\-((19|[2-9]\\d)\\d{2}))|((0[1-9]|[12]\\d|30)\\-(0[13456789]|1[012])\\-((19|[2-9]\\d)\\d{2}))|((0[1-9]|1\\d|2[0-8])\\-02\\-((19|[2-9]\\d)\\d{2}))|(29\\/02\\-((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$~';

                    // $isValidFormat =  preg_match($pattern, strval($this->valueToValidate)); // Outputs 1 if date is in valid format i.e. "30-03-2022";
                    // if(!$isValidFormat) throw new ValidationException("{$this->label} {$this->invalidLang}");

                    try {
                        $format = 'd-m-Y';
                        $date = strval($this->valueToValidate);
                        $dt = DateTime::createFromFormat($format, $date, new DatetimeZone($datetimeZone));
                        $isValid =  $dt && $dt->format($format) == $date;
                    
                        if($isValid){
                            // $this->valueToValidate = $dt;  <--- Don't return $dt. Because, DateTime::createFromFormat() method added extra hours with datetime value.
                            $this->valueToValidate = new Datetime(strval($this->valueToValidate), new DatetimeZone($datetimeZone));

                        }
                        else{
                            // echo "not ok";
                            throw new ValidationException("{$this->label} {$this->invalidLang}");
                        }

                       
                    } catch (Exception $exp) {
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                }
                return $this;
            }

            /**
             * asBool()
             * 
             * Checks whether the value is a valid boolean
             * Convert the value as boolean.
             * 
             * @param string $datetimeZone Default is "Asia/Dhaka".
             * @throws ValidationException Exception if the value is invalid.
             * 
             * @return this $this
             */
            public function asBool(){
                $valueToValidate = $this->valueToValidate; //make it debug-friendly with xdebug.
                if(strlen(strval($valueToValidate)) > 0){
                    $valueToValidate = filter_var($valueToValidate, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($valueToValidate === NULL) {
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                    $this->valueToValidate = $valueToValidate;
                }
                return $this;
            }
      
            private function _is_date_valid($date_string){
                //$date_string = '23-11-2010';

                $matches = array();
                $pattern = '/^([0-9]{1,2})\\-([0-9]{1,2})\\-([0-9]{4})$/';
                if (!preg_match($pattern, $date_string, $matches)) return false;
                if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
                return true;

                // $test_arr  = explode('-', $date_string);
                // if (count($test_arr) == 3) {
                //     //checkdate ( int $month , int $day , int $year ) : bool
                //     if (checkdate($test_arr[1], $test_arr[0], $test_arr[2])) {
                //         return true;
                //     } else {
                //         false;
                //     }
                // }
                // else{
                //     return false;
                // }
            }

            private function _convert_string_to_date($DateString){
                $date =  date("Y-m-d", strtotime($DateString));
                return $date;
            }


        #endregion
        
        #region Length checking

            /**
             * exactLen()
             * 
             * Checks whether the value has the specified length.
             * 
             * @param int $length
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function exactLen($length){
                if(!empty($this->valueToValidate)){
                    $_length = strlen(strval($this->valueToValidate));
                    $label = $this->label;
                    if($_length != $length){
                        $msg = "$label {$this->invalidLang} $length $this->character_or_digit {$this->requiredLang} $_length $this->character_or_digit {$this->foundLang}";
                        throw new ValidationException($msg);
                    }
                }
            
                return $this;
            }

            /**
             * minLength()
             * 
             * Checks whether the value has the minimum specified length.
             * 
             * @param int $length
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function minLen($length){
                if(!empty($this->valueToValidate)){
                    $_length = strlen(strval($this->valueToValidate));
                    $label = $this->label;
                    if($_length < $length){
                        $msg = "{$label} {$this->invalidLang}. Minimum {$length} {$this->character_or_digit} required. Found $_length $this->character_or_digit.";
                        throw new ValidationException($msg);
                    }
                }
                return $this;
            }

            /**
             * maxLen()
             * 
             * Checks whether the value has the maximum specified length.
             * 
             * @param int $length
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function maxLen($length){
                if(!empty($this->valueToValidate)){
                    $_length = strlen(strval($this->valueToValidate));
                    $label = $this->label;
                    if($_length > $length){
                        $msg = "{$label} invalid. Maximum {$length} $this->character_or_digit allowed. Found $_length $this->character_or_digit.";
                        throw new ValidationException($msg);
                    }
                }
                return $this;
            }
       #endregion
       
        #region Range checking
            /**
             * minValue()
             * 
             * Checks whether the value has the minimum specified value.
             * 
             * If datatype is date, then convert into date before passing as arguement.
             * 
             * @param int $minimumValue
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function minVal($minimumValue){   
                $valueToValidate = $this->valueToValidate;

                //NOTE: Don't use empty() for numeric value. It treats 0 as empty.
                // if(!empty($valueToValidate)){
                
                // }

                if(strlen(strval($valueToValidate))>0){
                    $label = $this->label;
                    if($valueToValidate < $minimumValue){
                        $msg = "$label অবশ্যই $minimumValue এর সমান অথবা বেশি হতে হবে।";
                        throw new ValidationException($msg);
                    }
                }
            
            
                return $this;
            }
        
            /**
             * maxVal()
             * 
             * Checks whether the value has the minimum specified value.
             * If datatype is date, then convert into date before passing as arguement.
             * 
             * @param int $minimumValue
             * @return this $this
             * @throws ValidationException
             */
            public function maxVal($maximumValue){ 
                $valueToValidate = $this->valueToValidate;

                //NOTE: Don't use empty() for numeric value. It treats 0 as empty.
                // if(!empty($valueToValidate)){
                // }

                if(strlen(strval($valueToValidate)) > 0){
                    $label = $this->label;
                    if($valueToValidate > $maximumValue){
                        // $msg = "$label অবশ্যই $maximumValue এর সমান অথবা কম হতে হবে।";
                        $msg = "$label maximum value allowed $maximumValue.";
                        throw new ValidationException($msg);
                    }
                }
                return $this;
            }
        #endregion

        public function startsWith($startString){ 
            $string = $this->valueToValidate;
            $label = $this->label;
            if(!$this->_starts_with($string,$startString)){
                $msg = "$label শুরু হবে $startString দিয়ে।";
                throw new ValidationException($msg);
            }
            return $this;
        } 

        private function _starts_with($string, $startString){ 
            $len = strlen($startString); 
            if(strlen($string) === 0){
                return false;
            }
            return (substr($string, 0, $len) === $startString); 
        } 
        
        function endsWith($endString){ 
            $string = $this->valueToValidate;
            if(!$this->_ends_with($string, $endString)){
                $msg = "$this->label শেষ হবে $endString দিয়ে।";
                throw new ValidationException($msg);
            }
            return $this;
        } 

        private function _ends_with($string, $endString){ 
            $len = strlen($endString); 
            if(strlen($string) === 0){
                return false;
            }
            return (substr($string, -$len) === $endString); 
        } 

        /**
         * validate()
         * 
         * This must be the final call.
         * 
         * @return mix $valueToValidate Value or default value.
         */
        public function validate(){
            //  $valueToValidate = $this->valueToValidate;
             $valueToValidate = "";
          
            if(!isset($this->valueToValidate)){
                $valueToValidate = $this->defaultValue;
            }
            else{
                if(empty($this->valueToValidate)){
                    if(isset($this->defaultValue)){
                        $valueToValidate = $this->defaultValue;
                    }
                    else{
                        $valueToValidate = NULL;
                    }
                }
                else{
                    $valueToValidate = $this->valueToValidate;
                }
            }

            $this->_reset_private_variables();
            return $valueToValidate;
        }

                
        private function _reset_private_variables(){
            $this->label = "";
            unset($this->defaultValue);
            $this->required = false;
            // unset($this->valueToValidate);
            $this->valueToValidate = null;
            $this->character_or_digit = "";
        }

    } //<--class

?>