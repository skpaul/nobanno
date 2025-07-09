
<?php

/**
 * DataValidator Class
 *
 * This class provides a fluent interface for validating and sanitizing various types of data.
 * It supports input from POST/GET, manual values, and offers a wide range of validation rules
 * including type checking (numeric, alphabetic, email, mobile, date, boolean, array),
 * length constraints, and value range checks. It also includes sanitization methods
 * for removing tags, slashes, and converting HTML special characters.
 * All validation failures result in a ValidationException.
 *
 * Last modified: July 8, 2025
 */

     
    class ValidationException extends Exception
    {
    }

    class DataValidator{

        #region private variables
        
            private $label = "";

            private $requiredLang =  "required.";    //"উল্লেখ করুন" "required."
            private $invalidLang = "incorrect.";        //"সঠিক নয়" "incorrect."
            private $foundLang = "found.";            //"পাওয়া গেছে"  "found."

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
             * @deprecated Use title() instead.
             * 
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
            public function label(string $label): self{
                $this->label = trim($label);
                return $this;
            }

            /**
             * Sets the human-readable name for the value being validated.
             *
             * This name is used in validation error messages to make them more descriptive.
             *
             * @param string $title The name for the value (e.g., 'Customer Name', 'Date of Birth').
             * @return $this
             */
            public function title(string $title): self
            {
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
            public function value(string $value): self{
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
            public function post(string $httpPostFieldName): self{
                if(isset($_POST[$httpPostFieldName])){
                    if(is_array($_POST[$httpPostFieldName])){
                        $value = $_POST[$httpPostFieldName]; //dont use trim() on an array.
                    }
                    else{
                        $value = trim($_POST[$httpPostFieldName]);
                    }
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
            public function get(string $httpGetFieldName): self{
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
            public function default(mixed $defaultValue): self{
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
            public function sanitize(bool $removeTags = true, bool $removeSlash = true, bool $convertHtmlSpecialChars = true): self{
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
            public function removeTags(string|array|null $allowableTags = null): self{
                $this->valueToValidate = $this->_strip_tags($this->valueToValidate, $allowableTags); 
                return $this;
            }

            //Called from removeTags() and sanitize()
            private function _strip_tags(mixed $valueToValidate, string|array|null $allowableTags): mixed{
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
            public function removeSlash(): self{
                //The following cascading variables used for making the debugging easy.
                $valueToValidate = $this->valueToValidate ;
                $valueToValidate = $this->_removeSlash($valueToValidate); 
                $this->valueToValidate = $valueToValidate;
                return $this;
            }

            private function _removeSlash(string $valueToValidate): string{
                /* 
                    Example 
                    $text="My dog don\\\\\\\\'t like the postman!";
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
            public function convert(bool $convertDoubleQuote, bool $convertSingleQuote): self{

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
            
            private function _convert(string $valueToValidate, int $flag = ENT_QUOTES): string
            {
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
                return htmlspecialchars($valueToValidate, $flag);
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
            public function optional(): self{
                $this->required = false;
                return $this;
            }

            /**
             * Marks the current value as required.
             *
             * If the value is not provided (null, empty string, or empty array),
             * a ValidationException is thrown.
             *
             * @return $this
             * @throws ValidationException If the value is missing or empty.
             */
            public function required(): self
            {
                $this->required = true;

                $value = $this->valueToValidate;
                // An input is considered empty if it is null, an empty string, or an empty array.
                // Crucially, '0', 0, and false are considered valid, non-empty inputs.
                $isEffectivelyEmpty = ($value === null || $value === '' || (is_array($value) && count($value) === 0));

                if ($isEffectivelyEmpty) {
                    throw new ValidationException("{$this->label} {$this->requiredLang}");
                }
                return $this;
            }

        #endregion

        #region Language check
            /**
             * Checks if the value contains only English letters, numbers, and spaces in between.
             *
             * @return $this
             * @throws ValidationException If the value contains any other characters.
             */
            public function englishOnly(): self
            {
                if (isset($this->valueToValidate) && $this->valueToValidate !== '') {
                    // The regex /^[A-Za-z0-9]+(?: [A-Za-z0-9]+)*$/ ensures the entire string consists of only
                    // English letters and numbers, allowing single spaces between them.
                    if (!preg_match('/^[A-Za-z0-9]+(?: [A-Za-z0-9]+)*$/', strval($this->valueToValidate))) {
                        throw new ValidationException("{$this->label} must contain only English letters, numbers, and spaces.");
                    }
                }
                return $this;
            }

            /**
             * Checks if the value contains only Bengali characters and spaces.
             *
             * @return $this
             * @throws ValidationException If the value contains any other characters.
             */
            public function banglaOnly(): self
            {
                if (isset($this->valueToValidate) && $this->valueToValidate !== '') {
                    // This regex matches one or more characters in the Bengali Unicode block,
                    // as well as spaces. The 'u' modifier is essential for Unicode matching.
                    if (!preg_match('/^[\x{0980}-\x{09FF}\s]+$/u', strval($this->valueToValidate))) {
                        throw new ValidationException("{$this->label} শুধুমাত্র বাংলা অক্ষর ব্যবহার করুন।");
                    }
                }
                return $this;
            }
        #endregion

        #region Check for data type
            
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
            public function asAlphabetic(bool $allowSpace): self{
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
            public function asNumeric(): self{
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
            public function asAlphaNumeric(bool $allowSpace): self{
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
            public function asString(bool $allowSpace): self{
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
            private function _hasWhitespace(string $text ): bool
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
            public function asInteger(bool $allowNegative): self{
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
            public function asFloat(bool $allowNegative): self{
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
        
            private function _has_decimal(mixed $number): bool{
                $count = substr_count(strval($number), '.');
                if($count == 1){
                    return true;
                }
                else{
                    return false;
                }
            }

            /**
             * asArray()
             * 
             * Value must be an array.
             * 
             * @return this $this
             * 
             * @throws ValidationException
             */
            public function asArray(): self{
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    if(!is_array($this->valueToValidate)){
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                    else{
                        if($this->required){
                            if(count($this->valueToValidate) == 0){
                                throw new ValidationException("{$this->label} {$this->invalidLang}");
                            }
                        }
                    }
                }
                return $this;
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
            public function asEmail(): self{
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    // $label = $this->label;
                    if (!filter_var($this->valueToValidate, FILTER_VALIDATE_EMAIL)) {
                        throw new ValidationException("{$this->label} {$this->invalidLang}");
                    }
                }
                return $this;
            }

            /**
             * Checks and formats a Bangladeshi mobile number.
             *
             * It validates mobile numbers with or without the '88' or '880' prefix and,
             * upon success, formats the number to the '8801...' standard.
             *
             * @return $this
             * @throws ValidationException If the number is invalid.
             */
            public function asMobile()
            {
                $mobileNumber = $this->valueToValidate;

                // If the field is optional and no value is provided, skip validation.
                // If it's required, the required() method should have already handled it.
                if (!isset($mobileNumber) || $mobileNumber === '') {
                    return $this;
                }

                if (!is_numeric($mobileNumber)) {
                    throw new ValidationException("{$this->label} {$this->invalidLang}");
                }

                $operatorCodes = ["013", "014", "015", "016", "017", "018", "019"];
                $normalizedNumber = null;

                // Normalize the number to the 11-digit (01...) format
                if ($this->_starts_with($mobileNumber, "8801")) { // 13 digits: 8801...
                    $normalizedNumber = substr($mobileNumber, 2);
                } elseif ($this->_starts_with($mobileNumber, "01")) { // 11 digits: 01...
                    $normalizedNumber = $mobileNumber;
                } elseif ($this->_starts_with($mobileNumber, "1")) { // 10 digits: 1...
                    $normalizedNumber = "0" . $mobileNumber;
                }

                // Perform validation on the normalized number
                if ($normalizedNumber === null || strlen($normalizedNumber) !== 11) {
                    throw new ValidationException("{$this->label} {$this->invalidLang}");
                }

                $operatorCode = substr($normalizedNumber, 0, 3);
                if (!in_array($operatorCode, $operatorCodes)) {
                    throw new ValidationException("{$this->label} {$this->invalidLang}");
                }

                // Success: store the fully formatted number (e.g., 88017...)
                $this->valueToValidate = "88" . $normalizedNumber;
                return $this;
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
            public function asDate(string $datetimeZone = "Asia/Dhaka"): self{
                if(isset($this->valueToValidate) && !empty($this->valueToValidate)){
                    // $pattern =   '~^(((0[1-9]|[12]\d|3[01])\-(0[13578]|1[02])\-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\-(0[13456789]|1[012])\-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\-02\-((19|[2-9]\d)\d{2}))|(29\/02\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$~';

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
             * @throws ValidationException Exception if the value is invalid.
             * 
             * @return this $this
             */
            public function asBool(): self{
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
            public function exactLen(int $length): self{
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
            public function minLen(int $length): self{
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
            public function maxLen(int $length): self{
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
            public function minVal(int|float $minimumValue): self{   
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
            public function maxVal(int|float $maximumValue): self{ 
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

        #region Misc.
            public function startsWith(string $startString): self{ 
                $string = $this->valueToValidate;
                $label = $this->label;
                if(!$this->_starts_with($string,$startString)){
                    $msg = "$label শুরু হবে $startString দিয়ে।";
                    throw new ValidationException($msg);
                }
                return $this;
            } 

            private function _starts_with(string $string, string $startString): bool{ 
                $len = strlen($startString); 
                if(strlen($string) === 0){
                    return false;
                }
                return (substr($string, 0, $len) === $startString); 
            } 
            
            public function endsWith(string $endString): self{ 
                $string = $this->valueToValidate;
                if(!$this->_ends_with($string, $endString)){
                    $msg = "$this->label শেষ হবে $endString দিয়ে।";
                    throw new ValidationException($msg);
                }
                return $this;
            } 

            private function _ends_with(string $string, string $endString): bool{ 
                $len = strlen($endString); 
                if(strlen($string) === 0){
                    return false;
                }
                return (substr($string, -$len) === $endString); 
            } 
        #endregion

        /**
         * Validates the value and returns the final result.
         *
         * This must be the final method called in the chain. It returns the validated and
         * sanitized value. If the input was missing or empty (null, '', or []),
         * it returns the default value (which is null if not explicitly set).
         *
         * @return mixed The validated value or the default value.
         */
        public function validate()
        {
            $value = $this->valueToValidate;

            // An input is considered empty if it is null, an empty string, or an empty array.
            // Crucially, '0', 0, and false are considered valid, non-empty inputs.
            $isEffectivelyEmpty = ($value === null || $value === '' || (is_array($value) && count($value) === 0));

            if ($isEffectivelyEmpty) {
                // If the provided value is empty, fall back to the default.
                $finalValue = $this->defaultValue;
            } else {
                // Otherwise, use the provided value.
                $finalValue = $this->valueToValidate;
            }

            $this->_reset_private_variables();
            return $finalValue;
        }

                
        private function _reset_private_variables(): void{
            $this->label = "";
            // unset($this->defaultValue);
            $this->defaultValue = null;
            $this->required = false;
            // unset($this->valueToValidate);
            $this->valueToValidate = null;
            $this->character_or_digit = "";
        }

    } //<--class

?>