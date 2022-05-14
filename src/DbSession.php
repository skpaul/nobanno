
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
         * constructor()
         * 
         * @param Database $zeroSql Instance of ZeroSQL.
         * 
         * 
         * @param int $defaultDuration Default session timeout value in seconds. Default value is 10800 seconds (180 minutes) (3 Hours). 
         *          
         * 
         */
        public function __construct(Database $db, string $tableName, int $defaultDuration = 10800) {
            $this->db = $db;
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
         * @param string $sessionName user's ID or email or loginName.
         * 
         * @return this
         */
        public function start(string $owner){
            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $sql = "INSERT INTO `$this->table`(`owner`, `datetime`) 
            VALUES(:sessionOwner, :currentDatetime)";
            $this->sessionId = $this->db->insert($sql, array("sessionOwner"=>$owner, "currentDatetime"=>$now));

            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->sub(new DateInterval('P2D'));  //go back to 2 days ago.
            $now = $now->format('Y-m-d H:i:s');
            $sql = "DELETE FROM `$this->table` WHERE `datetime` < :prevData "; 
            $this->db->delete($sql, array(":prevData"=>$now)); //delete 2 days older data from session table.

            return $this;
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
        public function continue(int $sessionId){
            //call this function on every subsequent page except login page
            $this->db->fetchAsAssoc();
            $sql = "SELECT *  
                    FROM `$this->table` 
                    WHERE id = :id";

            $sessions = $this->db->select($sql, array("id"=>$sessionId));
            if(count($sessions) == 0){
                throw new SessionException("Session not found.");
            }

            $session = $sessions[0];
            $this->data = json_decode($session["data"]);

            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $now = strtotime($now);

            // $lastActivity = $this->swiftDatetime->input($sessionBase->sessionDatetime)->asYmdHis();
            $lastActivity = $session['datetime'];
            $lastActivity = strtotime($lastActivity);
           
            $diff = $now - $lastActivity;
            if($diff > $this->defaultSessionTimeoutValue){
                //delete from session_base table
                $this->_deleteAllSessionData($sessionId);
                throw new SessionException("Session expired.");
            }
         
            //all are okay. finally sets.
            $this->sessionId = $sessionId;
            $this->_updateLastActivityDatetime();
            $this->db->backToPrevFetchStyle();
            return $this;
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
        public function getData(string $key){return $this->data->$key; $this->_updateLastActivityDatetime(); return $this;}
        public function removeData(string $key){unset($this->data->{$key}); $this->_update(); return $this;} 
        
        private function _updateLastActivityDatetime(){
            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $sql = "UPDATE `$this->table` SET `datetime`= :currentDatetime WHERE id= :id";
            $this->db->update($sql, array('currentDatetime'=>$now, "id"=>$this->sessionId));
        }

        private function _update(){
            $data = json_encode($this->data);
            $now = (new DateTime('now', new DateTimeZone("Asia/Dhaka")))->format('Y-m-d H:i:s'); 
            $sql = "UPDATE `$this->table` SET `data`=:sessionData, `datetime`= :currentDatetime WHERE id= :id";
            $this->db->update($sql, array('sessionData'=>$data, 'currentDatetime'=>$now, "id"=>$this->sessionId));
        }

        //delete all data from sessions
        /**
         * close()
         * 
         * Close the active session and delete all session data from database of this sessionId.
         * 
         * Also, unset the sessionId variable.
         */
        public function close(){
            //delete from sessions table
            $sql = "DELETE FROM `$this->table` WHERE id=:id";
            $affectedRows =  $this->db->delete($sql, array("id"=>$this->sessionId));
            unset($this->sessionId);
            return $affectedRows;
        }

    } //<--class
?>