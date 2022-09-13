
<?php 

    /*
        Last modified - 01-02-2022
    */
    
    require_once('SwiftSessionInterface.php');
    require_once('SessionException.php');

    class DbSession implements SwiftSessionInterface {
      
        private $sessionId = "";
        private $db = null;
        private $table = "";
        private $defaultSessionTimeoutValue = 0;
        private $data = NULL;
        
        /**
         * The PDO connection.
         *
         * @var \PDO
         */
        private $pdo;

        /**
         * constructor()
         * 
         * @param Database $zeroSql Instance of ZeroSQL.
         * 
         * 
         * @param int $defaultDuration Default session timeout value in seconds. Default value is 10800 seconds (180 minutes) (3 Hours). 
         *          
         * 
         */
        public function __construct(\PDO $pdo, string $tableName, int $defaultDuration = 10800) {
            $this->pdo = $pdo;
            $this->table = $tableName;
            $this->defaultSessionTimeoutValue = $defaultDuration;
            $this->data= new stdClass();
        }

        public function __destruct(){ }

        /**
         * start()
         * 
         * Creates a new session.
         * Call this method only once in a site after a successfull user validation i.e. in login page.
         * 
         * @return this
         */
        public function startNew(){
            $now = new DateTime('now', new DateTimeZone("Asia/Dhaka"));
            $twoDaysAgo = $now->sub(new DateInterval('P2D'));  //go back to 2 days ago.
            $sql = "
                    DELETE FROM `$this->table` WHERE `datetime` < '{$twoDaysAgo->format("Y-m-d H:i:s")}'; 
                    INSERT INTO `$this->table`(`datetime`) VALUES('{$now->format("Y-m-d H:i:s")}'); 
                   ";
            
            $this->pdo->exec($sql);
            $this->sessionId =  $this->pdo->lastInsertId();

            return true;
        }

       /**
        *@return string sessionId 
        */
        public function getSessionId(){
            return $this->sessionId;
        }

        /**
         * continue()
         * 
         * Continues a session that was created prevously.
         * Call this method in every subsequent pages.
         * 
         * @param int $sessionId
         * 
         * @return this
         * 
         * @throws SessionException
         */
        public function continue(int $sessionId):bool{
            //call continue() function on every subsequent page except login page
           
            $sql = "SELECT * FROM `$this->table` WHERE id = :id";
            $statement = $this->pdo->prepare($sql);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $statement->execute(array("id"=>$sessionId));
            $session = $statement->fetch();

            if($session === false) throw new SessionException("Session not found.");

            $this->data = json_decode($session["data"]);

            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $now = strtotime($now);
            $lastActivity = strtotime($session['datetime']);
            $diff = $now - $lastActivity;
            if($diff > $this->defaultSessionTimeoutValue){
                //delete from table
                $statement = $this->pdo->prepare("DELETE FROM `$this->table` WHERE id = :id");
                $statement->execute(array("id"=>$sessionId));
                throw new SessionException("Session expired.");
            }
         
            //all are okay. finally sets.
            $this->sessionId = $sessionId;
            $this->_updateLastActivityDatetime();
            return true;
        }

        public function __call($key, $values=NULL){
            if(isset($values) && !empty($values)){
                $this->data->$key = $values[0];
                return $this;
            }
            else{
                return $this->data->$key;
            }
        }
       
        public function setData(string $key, $value){
            $this->data->$key = $value; 
            $this->_update(); return $this;
        }
        
        /**
         * getData()
         * 
         * @return null if $key not found. 
         */
        public function getData(string $key){
            $this->_updateLastActivityDatetime(); 
            if(isset($this->data->$key)){
                return $this->data->$key; 
            }else{
                return null;
            }
        }

        public function removeData(string $key):bool{
            unset($this->data->{$key}); 
            $this->_update(); 
            return true;
        } 
        
        private function _updateLastActivityDatetime(){
            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $sql = "UPDATE `$this->table` SET `datetime`= '$now' WHERE id= :id";
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array("id"=>$this->sessionId));
        }

        private function _update(){
            $data = json_encode($this->data);
            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $sql = "UPDATE `$this->table` SET `data`=:sessionData, `datetime`= '$now' WHERE id= :id";
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('sessionData'=>$data, "id"=>$this->sessionId));
        }

        //delete all data from sessions
        /**
         * close()
         * 
         * Close the active session and delete all session data from database of this sessionId.
         * 
         * Also, unset the sessionId variable.
         */
        public function close():bool{
            //delete from sessions table
            $sql = "DELETE FROM `$this->table` WHERE id=:id";
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array("id"=>$this->sessionId));
            unset($this->sessionId);
            return true;
        }

    } //<--class
?>