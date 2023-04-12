<?php
    declare(strict_types=1);

    class DbException extends PDOException
    {
    }


    // class FetchType
    // {
    //     const Assoc = 2;
    //     const Class = 8;
    //     const Obj = 5;
    //     // etc.
    // }

    class FetchType
    {
        const ASSOC = 2;
        const CLASS = 8;
        const OBJ = 5;
    }

    class DB{
        #region Private Variables
            private $pdo;
            private $options = [
                PDO::ATTR_ERRMODE                   => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE        => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES          => false,
                PDO::MYSQL_ATTR_INIT_COMMAND        => "SET NAMES utf8",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  => true,
            ];
        #endregion
       
        #region Base
            public function __construct(string $server, string $database, string $user, string $password) {
                $charset = 'utf8';
                $dsn = "mysql:host=$server;dbname=$database;charset=utf8";
                try {
                    $this->pdo = new PDO($dsn, $user, $password, $this->options);
                } catch (\PDOException $e) {
                    throw new DatabaseException($e->getMessage(), (int) $e->getCode(), $e);
                }
            }
            
            public function __destruct() {}
        
            public function close(){
                $this->pdo = null;
            }

            public function getPDO():\PDO{
                return $this->pdo;
            }
        #endregion
        
        #region TRANSACTION
            /**
             * @return bool Returns true on success or false on failure.
             */
            public function beginTransaction():bool
            {
                /* Begin a transaction, turning off autocommit */
                return $this->pdo->beginTransaction();
            }

            /**
             * @return bool Returns true on success or false on failure.
             */
            public function commit():bool
            {
                return $this->pdo->commit();
                /* Database connection is now back in autocommit mode */
            }

            /**
             * 
             * @return bool
             */
            public function rollBack()
            {
                if($this->pdo->inTransaction())
                    return $this->pdo->rollBack();
                else
                    false;
            }

            /**
             * Checks if inside a transaction.
             * 
             * @return bool
             */
            public function inTransaction():bool
            {
                return $this->pdo->inTransaction();
            }
        #endregion


        #region INSERT

            /**
             * insert()
             * 
             * Insert operation.
             * 
             * @param mixed $data-  array or object
             * @param string $table-  table name
             * 
             * @return integer last Insert Id
             */
            public function insert(mixed $data, string $table):int{

                if(!is_array($data)){
                    $data = get_object_vars($data);
                }

                $columns = implode(",", array_keys($data));
                $values = ":" .implode(", :", array_keys($data));
                $sql = "INSERT INTO $table($columns) VALUES($values)";

                try {
                    $statement =  $this->pdo->prepare($sql) ;
                    $statement->execute($data);
                    $lastId =  $this->pdo->lastInsertId();
                    return (int)$lastId;
                } catch (\PDOException $e) {
                    $backTraces = debug_backtrace();
                    $backTraceLog = "";
                    foreach ($backTraces as $trace) {
                        $path = $trace["file"]; $lineNo = $trace["line"];
                        $fileName =  basename($path);  
                        $backTraceLog .= "File- $fileName, Line- $lineNo <br>";
                    }
                    throw new DatabaseException($e->getMessage()."SQL-$sql, Backtrace-$backTraceLog", (int) $e->getCode(), $e);
                }
            }

        #endregion

        /**
         * update()
         * 
         * 
         * @param mixed $data-  array or object
         * @param mixed $conditions-  array or object
         * @param string $table-  table name
         * 
         * @return integer affected rows count
         */
        public function update(mixed $data, mixed $conditions, string $table): int {
            //turn into array
            if(!is_array($data)){
                $data = get_object_vars($data);
            }

            //turn into array
            if(!is_array($conditions)){
                $conditions = get_object_vars($conditions);
            }

            $set = ""; $where = "";
            foreach ($data as $column => $value){
                $set .= "$column=:$column,";
                $values[$column] = $value;
            }
            $set = rtrim($set, ",");

            foreach ($conditions as $column => $value){
                $where .= "$column=:$column AND ";
                $values[$column] = $value;
            }
            $where = rtrim($where, ",");

            $sql = "UPDATE $table SET $set WHERE $where";

            try {
                $statement =  $this->pdo->prepare($sql) ;
                $statement->execute($values);
                return $statement->rowCount();

            } catch (\PDOException $e) {
                $backTraces = debug_backtrace();
                $backTraceLog = "";
                foreach ($backTraces as $trace) {
                    $path = $trace["file"]; $lineNo = $trace["line"];
                    $fileName =  basename($path);  
                    $backTraceLog .= "File- $fileName, Line- $lineNo <br>";
                }
                throw new DatabaseException($e->getMessage()."SQL-$sql, Backtrace-$backTraceLog", (int) $e->getCode(), $e);
            }
        }

        public function delete(mixed $conditions, string $tableName):int{
            if(!isset($conditions) || empty($conditions)){
                throw new Exception("Delete conditions required.");
            }
        
            //turn into array
            if(!is_array($conditions)){
                $conditions = get_object_vars($conditions);
            }
            $where = "";
            foreach ($conditions as $column => $value){
                $where .= "$column=:$column AND ";
            }

            $where = rtrim($where, ' AND ');

            $sql = "DELETE FROM {$tableName} WHERE {$where}";
        
            $statement =  $this->pdo->prepare($sql) ;
            $statement->execute($conditions);
        
            return $statement->rowCount();
        }

        public function fetchClass(string $columns, string $table, mixed $conditions, $class){
                 
            //turn into array
            if(!is_array($conditions)){
                $conditions = get_object_vars($conditions);
            }

            $where = "";
            foreach ($conditions as $column => $value){
                $where .= "$column=:$column AND ";
            }

            $where = rtrim($where, ' AND ');

            $sql = "SELECT {$columns} FROM {$table} WHERE {$where}";
        
            $statement =  $this->pdo->prepare($sql) ;
            $statement->setFetchMode(PDO::FETCH_CLASS, $class); 
            $statement->execute($conditions);
            return  $statement->fetch();
        }


        #region EXEC
            /*
                //exec() allows to execute multiple insert, update & delete sql
                $sql = "
                DELETE FROM car; 
                INSERT INTO car(name, type) VALUES ('car1', 'coupe'); 
                INSERT INTO car(name, type) VALUES ('car2', 'coupe');
                ";
            */

            public function exec(string $sql){
                $this->pdo->exec($sql);
            }
        #endregion
    }
?>