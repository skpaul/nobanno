
<?php 
    namespace Nobanno;

    class EnDecryptor
    {
        private string $secretKey = "";
        //Constructor.
        public function __construct(string $secretKey) {
            $this->secretKey = $secretKey;
        }

        //Destructor
        public function __destruct(){
          
        }

        /**
         * encrypt() 
         * 
         * Encrypt a string.
         * 
         * @param string $value The value to encrypt.
         * 
         * @return string Encrypted string, return false if fails.
         */
        public function encrypt(string $value):string {
            $output = false;
            $encrypt_method = "AES-256-CBC";
            // $secret_key = 'jhsdgfgweyf76wryg3rtjhmnfwer78gf634g45rbc3wry8734y5t';
            $secret_key = $this->secretKey;
            $secret_iv = 'sjdgfhsd83497953dshfger78657834tbjwerk';
            // hash
            $key = hash('sha256', $secret_key);
            
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $output = openssl_encrypt($value, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);

            return $output;
        }

        /**
         * decrypt() 
         * 
         * @return string Decrypted string, return false if fails.
         */
        public function decrypt($encrypted_string) {
            $output = false;
            $encrypt_method = "AES-256-CBC";
            // $secret_key = 'jhsdgfgweyf76wryg3rtjhmnfwer78gf634g45rbc3wry8734y5t';
            $secret_key = $this->secretKey;
            $secret_iv = 'sjdgfhsd83497953dshfger78657834tbjwerk';
            // hash
            $key = hash('sha256', $secret_key);
            
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
        
            $output = openssl_decrypt(base64_decode($encrypted_string), $encrypt_method, $key, 0, $iv);

            return $output;
        }

        //https://designingworld.net/how-to-encrypt-and-execute-your-php-code-with-mcrypt-or-openssl/

        //note- DONT USE THIS ON TELETALK SERVER
        public function encrypt_DEPRECATED($payload) {
            // $key = '1234567891011120'; 
            // $IV_SIZE = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            // $iv = mcrypt_create_iv($IV_SIZE, MCRYPT_DEV_URANDOM);
            // $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $payload, MCRYPT_MODE_CBC, $iv);
            // $combo = $iv . $crypt;
            // $garble = base64_encode($iv . $crypt);
            // return $garble;
            return $payload;
        }

        //note- DONT USE THIS ON TELETALK SERVER
        public function decrypt_DEPRICATED($garble) {
            // $key = '1234567891011120'; 
            // $combo = base64_decode($garble);
            // $IV_SIZE = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            // $iv = substr($combo, 0, $IV_SIZE);
            // $crypt = substr($combo, $IV_SIZE, strlen($combo));
            // $payload = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypt, MCRYPT_MODE_CBC, $iv);
            // return $payload;

            return $garble;
        }

        //The following two functions use base64_encode/base64_decode to encrypt/decrypt query string data
        //Recommended for less sensitve data.
        public function encodeQueryString($value){
            $a= urlencode(base64_encode($value));
            return $a;
        }

        public function decodeQueryString($value){
            $b = base64_decode(urldecode($value));
            return $b;
        }

        //This is HIGHLY recommended for sensitive data in query string.
        public function createHash($value){
            $salt = "cV0QuOlx";
            $hashed = md5($salt.$value);//A hash that you'll pass as well
            return $hashed;

            //When you send the parameter via. GET - add a hash value along with it
            //----------------------------------------------------------------------
            // $parameter = "abc"; //The parameter which you'll pass as a GET parameter
            // $salt = "cV0puOlx";
            // $hashed = md5($salt.$parameter);//A hash that you'll pass as well
            // header("Location: http://www.yourdomain.com?param=$parameter&hash=$hash");

            //Then when you read the parameters, check that the hash is a valid one:
            //----------------------------------------------------------------------
            // $parameter  = $_GET['param'];
            // $hash = $_GET['hash'];
            // $salt = "cV0puOlx";
            // $hashed = md5($salt.$parameter);
            // //now you check:
            // if ($hash === $hashed){
            //    //everything's fine - continue processing
            // }
            // else{
            //   // ERROR - the user tried to tamper with your parameter
            //   // show error-message and bail-out
            // }
        }
    } //<--class

?>