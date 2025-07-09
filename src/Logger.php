<?php 

    /**
     * Logger class for error and custom log management.
     */
    class Logger
    {
        private $rootDirectory = "";
        private $subDirectory = "";
        private $logFileName = "error.log";
        private $logFilePath = "";

        /**
         * Constructor for Logger.
         * @param string $rootDirectoryPath
         * @param string|null $subDirectoryName
         * @throws Exception
         */
        public function __construct($rootDirectoryPath, $subDirectoryName = null) {
            $this->rootDirectory = $rootDirectoryPath;

            if ($subDirectoryName !== null) {
                $this->subDirectory = $subDirectoryName;
                $logDir = $this->rootDirectory . DIRECTORY_SEPARATOR . $this->subDirectory;
                if (!file_exists($logDir)) {
                    if (!mkdir($logDir, 0777, true) && !is_dir($logDir)) {
                        throw new \RuntimeException("Failed to create log directory: $logDir");
                    }
                }
                $this->logFilePath = $logDir . DIRECTORY_SEPARATOR . $this->logFileName;
            } else {
                $this->logFilePath = $this->rootDirectory . DIRECTORY_SEPARATOR . $this->logFileName;
            }

            $this->_ensureLogFileExists($this->logFilePath);

            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

            ini_set('log_errors', '1');
            if (defined('ENVIRONMENT') && ENVIRONMENT == "PRODUCTION") {
                ini_set('display_errors', 1);
            }

            ini_set('error_log', $this->logFilePath);

            set_error_handler(function($errno, $errstr, $errfile, $errline){
                if (!(error_reporting() & $errno)) {
                    return false;
                }
                $errstr = htmlspecialchars($errstr);
                switch ($errno) {
                    case E_USER_DEPRECATED:
                        $this->_createErrorLog("E_USER_DEPRECATED", $errstr, $errfile, $errline);
                        break;
                    case E_ERROR:
                        $this->_createErrorLog("E_ERROR", $errstr, $errfile, $errline);
                        break;
                    case E_WARNING:
                        $this->_createErrorLog("E_WARNING", $errstr, $errfile, $errline);
                        break;
                    case E_PARSE:
                        $this->_createErrorLog("E_PARSE", $errstr, $errfile, $errline);
                        break;
                    case E_NOTICE:
                        $this->_createErrorLog("E_NOTICE", $errstr, $errfile, $errline);
                        break;
                    case E_CORE_ERROR:
                        $this->_createErrorLog("E_CORE_ERROR", $errstr, $errfile, $errline);
                        break;
                    case E_CORE_WARNING:
                        $this->_createErrorLog("E_CORE_WARNING", $errstr, $errfile, $errline);
                        break;
                    case E_COMPILE_ERROR:
                        $this->_createErrorLog("E_COMPILE_ERROR", $errstr, $errfile, $errline);
                        break;
                    case E_ALL:
                        $this->_createErrorLog("E_ALL", $errstr, $errfile, $errline);
                        break;
                    case ~E_NOTICE:
                        $this->_createErrorLog("~E_NOTICE", $errstr, $errfile, $errline);
                        break;
                    default:
                        $this->_createErrorLog($errno, $errstr, $errfile, $errline);
                        break;
                }
                return true;
            });

            set_exception_handler(function($exp){
                $this->_createErrorLog($exp->getCode(), $exp->getMessage(), $exp->getFile(), $exp->getLine());
                echo("Error occurred. See the error log file for details");
            });
        }

        /**
         * Ensure the log file exists.
         * @param string $filePath
         * @throws Exception
         */
        private function _ensureLogFileExists($filePath) {
            if (!file_exists($filePath)) {
                $handle = fopen($filePath, 'w');
                if ($handle === false) {
                    throw new \RuntimeException("Can't create file: $filePath");
                }
                fclose($handle);
            }
        }

        //This function is used in set_error_handler() and set_exception_handler() to handle uncaught errors.
        private function _createErrorLog($errNo, $errDetails, $fileName, $lineNumber){
            // if($errDetails == 'Undefined property: ZeroSQL::$tableName'){
            //     return;
            // }
            
            $currentdatetime = new DateTime("now", new DateTimeZone('Asia/Dhaka'));
            $FormattedDateTime = $currentdatetime->format('d-m-Y h:i:s A');  //date('Y-m-d H:i:s');
            
            $final_log = strval($errNo) . ":: Description-". $errDetails . "\n";
            $final_log .= "File:$fileName, Line:$lineNumber, Datetime:$FormattedDateTime  " . "\n";
            $final_log .= "------------------------------------------------------------------------------------\n";
            file_put_contents($this->logFilePath, $final_log, FILE_APPEND | LOCK_EX );
        }
        
        private function _createLog($log_text){
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            $path = $caller['file'];
            $file_name = basename($path); // $file is set to "file.php"
            $line_number = $caller['line'];
            
            $currentdatetime = new DateTime("now", new DateTimeZone('Asia/Dhaka'));
            $FormattedDateTime = $currentdatetime->format('d-m-Y h:i:s A');  //date('Y-m-d H:i:s');
            
            $final_log = $log_text . "\n";
            $final_log .= "File:$file_name, Line:$line_number, Datetime:$FormattedDateTime  " . "\n";
            $final_log .= "------------------------------------------------------------------------------------\n";

            //error_log($final_log . "\n",3, $this->rootDirectory . "/site_logs.log");
            //Default path to error log is /var/log/apache2/error.log
            
            file_put_contents($this->logFilePath, $final_log, FILE_APPEND | LOCK_EX );
        }
       
        public function createLog($log_text){
            $this->_createLog($log_text);
        }

        private function _clearLogs($file){
            file_put_contents($file, "");
        }

        public function clearLogs(){
            $this->_clearLogs($this->logFilePath);
        }

        public function deleteLogs(){
            $this->_clearLogs($this->logFilePath);
        }
        

        private function _readLogs($file){
            if (!file_exists($file)) {
                echo "Log file does not exist.";
                return;
            }
            $fp = fopen($file, "r");
            if ($fp === false) {
                echo "Unable to open log file.";
                return;
            }
            if(filesize($file) > 0){
                $content = fread($fp, filesize($file));
                fclose($fp);
                $lines = explode("\n", $content);
                foreach($lines as $newline){
                    echo htmlspecialchars($newline) . '<br>';
                }
            }
            else{
                fclose($fp);
                echo "Hurray!! No log found.";
            }
        }

    

        public function readLogs(){
            $this->_readLogs($this->logFilePath);
        }

        public function hasLogs(){
            return (file_exists($this->logFilePath) && filesize($this->logFilePath) > 0);
        }
    } //<--class

?>