<?php
    class ExPDO extends PDO
    {
        // public function __construct($dsn, $username = NULL, $password = NULL, $options = [])
        public function __construct(string $server, string $databaseName, string $databaseUser, string $databasePassword) 
        {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ];
            // $options = array_replace($default_options, $options);
            $charset = 'utf8';
            $dsn = "mysql:host=$server;dbname=$databaseName;$charset";
            parent::__construct($dsn, $databaseUser, $databasePassword, $options);          
        }

        //Parameters: are the names listed in the function's definition. 
        //Arguments: are the real values passed to the function.

        private function debugBacktrace(){
                $backTraces = debug_backtrace();
                $backTraceLog = "";
                foreach ($backTraces as $trace) {
                    $path = $trace["file"]; $lineNo = $trace["line"];
                    $fileName =  basename($path);  
                    $backTraceLog .= "File: $fileName, Line: $lineNo <br>";
                }
                return $backTraceLog;
        }

        #region Insert

            /**
             * insert()
             * 
             * Execute insert sql and returns lastInsertId.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $data  null/object/array. 
             * 
             */
            public function insert(string $sql, mixed $data = null): int{
                try {
                    $statement = new PDOStatement;
                    if ($data)
                    {
                        if(is_object($data)) $data = get_object_vars($data);
                        $statement = $this->prepare($sql);
                        $statement->execute($data);
                        
                        return $this->lastInsertId();               
                    }
                    else{
                        $statement = $this->query($sql);
                        return $this->lastInsertId();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }

            /**
             * prepareInsertSql()
             * 
             * Creates an INSERT statement i.e. "INSERT INTO mytable(ColA, ColB) VALUES(ValA, ValB)".
             * 
             * @param mixed $dataToInsert  An array/object.
             * @param string  $tableName  Destination table name.
             * @param bool  $isParameterized  Whether prepares parameterized query or not.
             * 
             * @return string  "INSERT INTO mytable(ColA, ColB) VALUES(ValA, ValB)". 
             */
            public static function prepareInsertSql(mixed $dataToInsert, string $tableName, bool $isParameterized = true): string {
                if(is_object($dataToInsert)) $dataToInsert = get_object_vars($dataToInsert);
                $columns = "`" . implode("`, `", array_keys($dataToInsert)) . "`";
                $values= "";
                if($isParameterized){
                    $values = ":" .implode(", :", array_keys($dataToInsert));
                }
                else {
                    foreach ($dataToInsert as $key => $value){
                        $values .= "'$value', ";
                    }
                    $values = rtrim($values, ", ");
                }
               
                $sql = "INSERT INTO $tableName($columns) VALUES($values)";
                return $sql;
            }
        #endregion

        #region Select

            /**
             * fetchAssoc()
             * 
             * Select single row from table using FETCH_ASSOC.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $param  null/object/array. 
             * 
             * 
             * @throws PDOException.
             */
            public function fetchAssoc(string $sql, mixed $args = null){
                try {
                    if ($args)
                    {
                        if(is_object($args)) $args = get_object_vars($args);

                        $statement = $this->prepare($sql) ;
                        $statement->setFetchMode(PDO::FETCH_ASSOC); 
                        $statement->execute($args);
                        return $statement->fetch();
                    }
                    else{
                        $statement = $this->query($sql);
                        $statement->setFetchMode(PDO::FETCH_ASSOC);
                        return $statement->fetch();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }

            /**
             * fetchAssocs()
             * 
             * Select multiple rows from table using FETCH_ASSOC.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $param  null/object/array. 
             * 
             * @return array  Array of selected multiple rows.
             * 
             * @throws PDOException.
             */
            public function fetchAssocs(string $sql, mixed $args = null):array{
                try {
                    if ($args)
                    {
                        if(is_object($args)) $args = get_object_vars($args);
                        $statement = $this->prepare($sql) ;
                        $statement->setFetchMode(PDO::FETCH_ASSOC); 
                        $statement->execute($args);
                        return $statement->fetchAll();
                    }
                    else{
                        $statement = $this->query($sql);
                        $statement->setFetchMode(PDO::FETCH_ASSOC);
                        return $statement->fetchAll();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }

            /**
             * fetchObject()
             * 
             * Select single row from table using FETCH_OBJ.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $param  null/object/array. 
             * 
             * 
             * @throws PDOException.
             */
            public function fetchObject(string $sql, mixed $args = null){
                try {
                    if ($args)
                    {
                        if(is_object($args)) $args = get_object_vars($args);

                        $statement = $this->prepare($sql) ;
                        $statement->setFetchMode(PDO::FETCH_OBJ); 
                        $statement->execute($args);
                        return $statement->fetch();
                    }
                    else{
                        $statement = $this->query($sql);
                        $statement->setFetchMode(PDO::FETCH_OBJ);
                        return $statement->fetch();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }

            /**
             * fetchObjects()
             * 
             * Select multiple rows from table using FETCH_OBJ.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $param  null/object/array. 
             * 
             * @return array  Array of selected multiple rows.
             * 
             * @throws PDOException.
             */
            public function fetchObjects(string $sql, mixed $args = null):array{
                try {
                    if ($args)
                    {
                        if(is_object($args)) $args = get_object_vars($args);
                        $statement = $this->prepare($sql) ;
                        $statement->setFetchMode(PDO::FETCH_OBJ); 
                        $statement->execute($args);
                        return $statement->fetchAll();
                    }
                    else{
                        $statement = $this->query($sql);
                        $statement->setFetchMode(PDO::FETCH_OBJ);
                        return $statement->fetchAll();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }

            /**
             * fetchClass()
             * 
             * Select single row from table using FETCH_CLASS.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param string $className  Name of the returned class.
             * @param mixed $param  null/object/array. 
             * 
             * @return mixed  if found, returns single object of the specified class. Otherwise, return false.
             * 
             * @throws PDOException.
             */
            public function fetchClass(string $sql, string $className, mixed $args = null): mixed{
                try {
                    if ($args)
                    {
                        if(is_object($args)) $args = get_object_vars($args);
                        $statement =  $this->prepare($sql) ;
                        $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className); 
                        $statement->execute($args);
                        return $statement->fetch();
                    }
                    else{
                        $statement = $this->query($sql);
                        $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className); 
                        return $statement->fetch();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }

            /**
             * fetchClasses()
             * 
             * Select multiple rows from table using FETCH_CLASS.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param string $className  Name of the returned class.
             * @param mixed $param  null/object/array. 
             * 
             * @return array  Array of objects of the specified class.
             * 
             * @throws PDOException.            
             */
            public function fetchClasses(string $sql, string $className, mixed $args = null): mixed{
                try {
                    if ($args)
                    {
                        if(is_object($args)) $args = get_object_vars($args);
                        $statement =  $this->prepare($sql) ;
                        $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className); 
                        $statement->execute($args);
                        return $statement->fetchAll();
                    }
                    else{
                        $statement = $this->query($sql);
                        $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className); 
                        return $statement->fetchAll();
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }


            /**
             * prepareSelectSql()
             * 
             * Creates a vanilla general-purpose SELECT statement i.e. "SELECT columns, ... FROM table WHERE ....". JOIN, ORDER BY or GROUP BY is not included here.
             * 
             * @param string $columns  "*" or "id, name, ..."
             * @param string  $tableName.
             * @param mixed $where  An array OR object.
             * @param bool $isParameterized  Whether prepares parameterized query or not.
             * 
             * @return string  "SELECT columns, ... FROM table WHERE ...." 
             */
            public static function prepareSelectSql(string $columns, string $tableName, mixed $where, bool $isParameterized): string{
                $whereClause = "";
                //If $where is an object, make it an array-
                if(is_object($where)) $where = get_object_vars($where);
                    
                foreach ($where as $column => $value){
                    if($isParameterized)
                        $whereClause .= "$column=:$column AND ";
                    else
                        $whereClause .= "$column='$value' AND ";
                }
                $whereClause = "WHERE " . rtrim($whereClause, " AND ");
              
                return  "SELECT $columns FROM $tableName " . $whereClause;
            }


        #endregion
        
        #region Update

            /**
             * update()
             * 
             * Execute update sql and returns number of rows affected.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $data  null/object/array. 
             * 
             * @return int  Number of rows affected.
             * 
             * @throws PDOException.             
             */
            public function update(string $sql, mixed $data = null):int{
                try {
                    if ($data)
                    {
                        if(is_object($data)) $data = get_object_vars($data);
                        $statement = $this->prepare($sql) ;
                        $statement->execute($data);
                        return $statement->rowCount();               
                    }
                    else{
                        $statement = $this->query($sql);
                        return $statement->rowCount();               
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }


            /**
             * prepareUpdateSql()
             * 
             * Creates a vanilla general-purpose UPDATE statement i.e. "UPDATE myTable SET column1=value1, ...". 
             * 
             * 
             * @param string $tableToUpdate  The name of the destination table.
             * @param mixed $dataToUpdate  An array/object. 
             * @param mixed $where  An array/object/string. 
             * @param bool $isParameterized  Whether prepares parameterized query or not.
             * 
             * @return string  "UPDATE myTable SET column1=value1, ..." 
             */
            public static function prepareUpdateSql(string $tableToUpdate, mixed $dataToUpdate, mixed $where, bool $isParameterized): string{
                if(is_object($dataToUpdate)) $dataToUpdate = get_object_vars($dataToUpdate);
                $updateClause = "";
                foreach ($dataToUpdate as $column => $value){
                    if($isParameterized) $updateClause .= "$column=:$column, ";
                    else $updateClause .= "$column='$value', ";
                }

                $updateClause = rtrim($updateClause, ", ");

                $whereClause = "";
                //If $where has value, include it in where clause-
                if(isset($where)){
                    //If $where is an array/object-
                    if(is_object($where) || is_array($where)){
                        //If $where is an object, make it an array-
                        if(is_object($where)) $where = get_object_vars($where);
                        foreach ($where as $column => $value){
                            if($isParameterized) $whereClause .= "$column=:$column AND ";
                            else $whereClause .= "$column='$value' AND ";
                        }
                        $whereClause = "WHERE " . rtrim($whereClause, " AND ");
                    }
                    else{
                        //If $where is a plain string like "id=1 AND age<20"
                        $whereClause = "WHERE $where";
                    }
                }

                return "UPDATE $tableToUpdate SET $updateClause $whereClause";
            }

        #endregion

        #region Delete

            /**
             * delete()
             * 
             * Execute update sql and returns number of rows affected.
             * 
             * 
             * @param string $sql  SQL statement. parameterized/non-parameterized.
             * @param mixed $param  null/object/array. 
             * 
             * @return int  Number of rows affected.
             * 
             * @throws PDOException.             
             */
            public function delete(string $sql, mixed $param = null):int{
                try {
                    if ($param)
                    {
                        if(is_object($param)) $param = get_object_vars($param);
                        $statement = $this->prepare($sql) ;
                        $statement->execute($param);
                        return $statement->rowCount();               
                    }
                    else{
                        $statement = $this->query($sql);
                        return $statement->rowCount();               
                    }
                } catch (\Throwable $e) {
                    $backTraceLog = $this->debugBacktrace();
                    throw new PDOException("PDOException: ". $e->getMessage().". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
                }
            }
            

            /**
             * prepareDeleteSql()
             * 
             * Creates a vanilla general-purpose DELETE statement i.e. "DELETE FROM myTable WHERE column1=value1, ...". 
             * 
             * 
             * @param mixed $where  An array/object. 
             * @param string  $tableName.
             * @param bool $isParameterized  Whether prepares parameterized query or not.
             * 
             * @return string  "DELETE FROM myTable WHERE column1=value1, ..." 
             */
            public static function prepareDeleteSql(mixed $where, string $tableName, bool $isParameterized): string{
                
                if(is_object($where)) $where = get_object_vars($where);
                $whereClause = "";
                foreach ($where as $column => $value){
                    if($isParameterized) $whereClause .= "$column=:$column AND ";
                    else $whereClause .= "$column='$value' AND ";
                }
                $whereClause = rtrim($whereClause, " AND ");
                return  "DELETE FROM $tableName WHERE " . $whereClause;
            }

        #endregion
    
        #region Utility Methods: SQL Statement Preparation

            /**
             * prepareWhereClause()
             * 
             * Creates a independant, stand-alone WHERE clause i.e. "WHERE column1=value1, ...". 
             * 
             * Can be used to generate 'WHERE' clause for SELECT/UPDATE/DELETE sql.
             * 
             * 
             * @param mixed $where  An array/object. 
             * @param bool $isParameterized  Whether prepares parameterized query or not.
             * 
             * @return string  "DELETE FROM myTable WHERE column1=value1, ..." 
             */
            public static function prepareWhereClause(mixed $where, bool $isParameterized): string{
                if(is_object($where)) $where = get_object_vars($where);

                $sql = "";
                foreach ($where as $column => $value){
                    if($isParameterized){
                        $sql .= "$column=:$column AND ";
                    }
                    else{
                        $sql .= "$column='$value' AND ";
                    }
                }

                $sql = rtrim($sql, " AND ");
              
                return  " WHERE " . $sql;
            }
        #endregion
    
    }
?>