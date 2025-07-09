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
        $charset = 'utf8';
        // Fix DSN charset
        $dsn = "mysql:host=$server;dbname=$databaseName;charset=$charset";
        parent::__construct($dsn, $databaseUser, $databasePassword, $options);
    }

    //Parameters: are the names listed in the function's definition. 
    //Arguments: are the real values passed to the function.

    private function debugBacktrace_old()
    {
        $backTraces = debug_backtrace();
        $backTraceLog = "";
        foreach ($backTraces as $trace) {
            $fileName = isset($trace["file"]) ? basename($trace["file"]) : '[internal]';
            $lineNo = $trace["line"] ?? '';
            $backTraceLog .= "File: $fileName, Line: $lineNo <br>";
        }
        return $backTraceLog;
    }

    /**
     *
     * Returns a formatted string of the backtrace to help debugging.
     * The trace starts from the file that called the ExPDO method.
     * @return string A formatted string of the call stack.
     */
    private function debugBacktrace(): string
    {
        $backTraces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        
        // Shift twice to remove the call to this method and the ExPDO public method (e.g., executeInsert).
        // This makes the trace start from the user's code, which is more relevant for debugging.
        if (isset($backTraces[0]) && $backTraces[0]['function'] === __FUNCTION__) {
            array_shift($backTraces); // Removes call to debugBacktrace()
        }
        if (isset($backTraces[0]['class']) && $backTraces[0]['class'] === __CLASS__) {
            array_shift($backTraces); // Removes call from within ExPDO (e.g., executeInsert)
        }

        $backTraceLog = [];
        foreach ($backTraces as $i => $trace) {
            $file = $trace['file'] ?? '[internal function]';
            $line = $trace['line'] ?? 0;
            $function = $trace['function'] ?? '';
            $class = $trace['class'] ?? '';
            $type = $trace['type'] ?? '';
            
            $backTraceLog[] = sprintf("#%d %s(%d): %s%s%s()", $i, $file, $line, $class, $type, $function);
        }
        return " Backtrace:\n" . implode("\n", $backTraceLog);
    }

    #region Insert

    /**
     * @deprecated Use executeInsert() instead.
     *
     * insert
     * 
     * Executes an SQL INSERT statement and returns the last inserted ID.
     * If $data is provided, a prepared statement is used with the given parameters.
     * Otherwise, the SQL is executed directly.
     * 
     * @param string $sql  The SQL INSERT statement (parameterized or not).
     * @param array|object|null $data  Optional. Parameters for the prepared statement as an array or object, or null for direct execution.
     * @return int  The last inserted ID.
     * @throws PDOException on error.
     */
    public function insert(string $sql, mixed $data = null): int
    {
        try {
            // Removed unnecessary: $statement = new PDOStatement;
            if ($data) {
                if (is_object($data)) $data = get_object_vars($data);
                $statement = $this->prepare($sql);
                $statement->execute($data);

                return $this->lastInsertId();
            } else {
                $statement = $this->query($sql);
                return $this->lastInsertId();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }


    /**
     * Executes an SQL INSERT statement and returns the last inserted ID.
     * If $data is provided, a prepared statement is used with the given parameters.
     * Otherwise, the SQL is executed directly.
     *
     * @param string $sql  The SQL INSERT statement (parameterized or not).
     * @param array|object|null $data  Optional. Parameters for the prepared statement as an array or object, or null for direct execution.
     * @return int  The last inserted ID.
     * @throws PDOException on error.
     */
    public function executeInsert(string $sql, mixed $data = null): int
    {
        try {
            // Removed unnecessary: $statement = new PDOStatement;
            if ($data) {
                if (is_object($data)) $data = get_object_vars($data);
                $statement = $this->prepare($sql);
                $statement->execute($data);

                return $this->lastInsertId();
            } else {
                $statement = $this->query($sql);
                return $this->lastInsertId();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }

    /**
     * @deprecated Use insertRecord() instead.
     *
     * insertAuto
     * 
     * Automatically generates and executes an INSERT statement for the specified table and data.
     * Uses parameterized queries by default.
     * 
     * @param string $tableName  The name of the table to insert into.
     * @param array|object $dataToInsert  The data to insert as an associative array or object.
     * @param bool $isParameterized  Optional. Whether to use parameterized queries (default: true).
     * @return int  The last inserted ID.
     * @throws PDOException on error.
     */
    public function insertAuto(string $tableName, array|object $dataToInsert, bool $isParameterized = true): int
    {
        try {
            // Removed unnecessary: $statement = new PDOStatement;
            if (is_object($dataToInsert)) $dataToInsert = get_object_vars($dataToInsert);
            $sql = $this->prepareInsertSql($dataToInsert, $tableName, $isParameterized);
            $statement = $this->prepare($sql);
            $statement->execute($dataToInsert);

            return $this->lastInsertId();
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }

    /**
     * Automatically generates and executes an INSERT statement for the specified table and data.
     * Uses parameterized queries by default.
     *
     * @param string $tableName  The name of the table to insert into.
     * @param array|object $dataToInsert  The data to insert as an associative array or object.
     * @param bool $isParameterized  Optional. Whether to use parameterized queries (default: true).
     * @return int  The last inserted ID.
     * @throws PDOException on error.
     */
    public function insertRecord(string $tableName, array|object $dataToInsert, bool $isParameterized = true): int
    {
        try {
            // Removed unnecessary: $statement = new PDOStatement;
            if (is_object($dataToInsert)) $dataToInsert = get_object_vars($dataToInsert);
            $sql = $this->prepareInsertSql($dataToInsert, $tableName, $isParameterized);
            $statement = $this->prepare($sql);
            $statement->execute($dataToInsert);

            return $this->lastInsertId();
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
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
    public static function prepareInsertSql(mixed $dataToInsert, string $tableName, bool $isParameterized = true): string
    {
        if (is_object($dataToInsert)) $dataToInsert = get_object_vars($dataToInsert);
        $columns = "`" . implode("`, `", array_keys($dataToInsert)) . "`";
        $values = "";
        if ($isParameterized) {
            $values = ":" . implode(", :", array_keys($dataToInsert));
        } else {
            foreach ($dataToInsert as $key => $value) {
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
    public function fetchAssoc(string $sql, mixed $args = null)
    {
        return $this->executeFetch($sql, $args, PDO::FETCH_ASSOC);
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
    public function fetchAssocs(string $sql, mixed $args = null): array
    {
        return $this->executeFetch($sql, $args, PDO::FETCH_ASSOC, true);
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
    public function fetchObject(string $sql, mixed $args = null)
    {
        return $this->executeFetch($sql, $args, PDO::FETCH_OBJ);
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
    public function fetchObjects(string $sql, mixed $args = null): array
    {
        return $this->executeFetch($sql, $args, PDO::FETCH_OBJ, true);
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
    public function fetchClass(string $sql, string $className, mixed $args = null): mixed
    {
        try {
            if ($args) {
                if (is_object($args)) $args = get_object_vars($args);
                $statement =  $this->prepare($sql);
                $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
                $statement->execute($args);
                return $statement->fetch();
            } else {
                $statement = $this->query($sql);
                $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
                return $statement->fetch();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
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
    public function fetchClasses(string $sql, string $className, mixed $args = null): mixed
    {
        try {
            if ($args) {
                if (is_object($args)) $args = get_object_vars($args);
                $statement =  $this->prepare($sql);
                $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
                $statement->execute($args);
                return $statement->fetchAll();
            } else {
                $statement = $this->query($sql);
                $statement->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
                return $statement->fetchAll();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
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
    public static function prepareSelectSql(string $columns, string $tableName, mixed $where, bool $isParameterized): string
    {
        $whereClause = "";
        //If $where is an object, make it an array-
        if (is_object($where)) $where = get_object_vars($where);

        foreach ($where as $column => $value) {
            if ($isParameterized)
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
     * @deprecated Use executeUpdate() instead.
     *
     * update()
     * 
     * Execute update sql and returns number of rows affected. 
     * 
     * $id = 1; //DON'T use $data["id"]
     * $data["name"] = "Saumitra";
     * $data["ghonta"] = "Suborno";
     * $sql = $db->prepareUpdateSql("table", $data, "id=:id", true);
     * $data["id"] = $id; //add $id, just before update()
     * $result = $db->update($sql,$data);
     * 
     * @param string $sql  SQL statement. parameterized/non-parameterized.
     * @param mixed $data  null/object/array. 
     * 
     * @return int  Number of rows affected.
     * 
     * @throws PDOException.             
     */
    public function update(string $sql, mixed $data = null): int
    {
        try {
            if ($data) {
                if (is_object($data)) $data = get_object_vars($data);
                $statement = $this->prepare($sql);
                $statement->execute($data);
                return $statement->rowCount();
            } else {
                $statement = $this->query($sql);
                return $statement->rowCount();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }

    /**
     * Executes an SQL UPDATE statement and returns the number of rows affected.
     * If $data is provided, a prepared statement is used with the given parameters.
     * Otherwise, the SQL is executed directly.
     *
     * @param string $sql  The SQL UPDATE statement (parameterized or not).
     * @param array|object|null $data  Optional. Parameters for the prepared statement as an array or object, or null for direct execution.
     * @return int  Number of rows affected.
     * @throws PDOException on error.
     */
    public function executeUpdate(string $sql, mixed $data = null): int
    {
        try {
            if ($data) {
                if (is_object($data)) $data = get_object_vars($data);
                $statement = $this->prepare($sql);
                $statement->execute($data);
                return $statement->rowCount();
            } else {
                $statement = $this->query($sql);
                return $statement->rowCount();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }

    /**
     * @deprecated Use updateRecord() instead.
     *
     * updateAuto()
     * 
     * Prepare & Execute update sql and returns number of rows affected. 
     * 
     * @param string $tableName Table name to update
     * @param array|object $dataToUpdate array or object. 
     * @param array|object $where array or object. 
     * @param bool $isParameterized true or false, default = true. 
     * 
     * @return int  Number of rows affected.
     * 
     * @throws PDOException.             
     */
    public function updateAuto(string $tableName, array|object $dataToUpdate, array|object $where, bool $isParameterized = true): int
    {
        try {
            $sql = $this->prepareUpdateSql($tableName, $dataToUpdate, $where, $isParameterized);
            //convert object to array-
            if (is_object($dataToUpdate)) $dataToUpdate = get_object_vars($dataToUpdate);
            if (is_object($where)) $where = get_object_vars($where);

            //combine -
            $params = array_merge($dataToUpdate, $where);

            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement->rowCount();
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }

    /**
     * Prepare & Execute update sql and returns number of rows affected. 
     *
     * @param string $tableName Table name to update
     * @param array|object $dataToUpdate array or object. 
     * @param array|object $where array or object. 
     * @param bool $isParameterized true or false, default = true. 
     * @return int  Number of rows affected.
     * @throws PDOException.             
     */
    public function updateRecord(string $tableName, array|object $dataToUpdate, array|object $where, bool $isParameterized = true): int
    {
        try {
            $sql = $this->prepareUpdateSql($tableName, $dataToUpdate, $where, $isParameterized);
            //convert object to array-
            if (is_object($dataToUpdate)) $dataToUpdate = get_object_vars($dataToUpdate);
            if (is_object($where)) $where = get_object_vars($where);

            //combine -
            $params = array_merge($dataToUpdate, $where);

            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement->rowCount();
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }


    /**
     * prepareUpdateSql()
     * 
     * Creates a vanilla general-purpose UPDATE statement i.e. "UPDATE myTable SET column1=value1, ...". 
     * 
     * $id = 1; //DON'T use $data["id"]
     * $data["name"] = "Saumitra";
     * $data["ghonta"] = "Suborno";
     * $sql = $db->prepareUpdateSql("table", $data, "id=:id", true);
     * $data["id"] = $id; //add $id, just before update()
     * $result = $db->update($sql,$data);
     * 
     * @param string $tableToUpdate  The name of the destination table.
     * @param mixed $dataToUpdate  An array/object. 
     * @param mixed $where  An array/object/string. 
     * @param bool $isParameterized  Whether prepares parameterized query or not.
     * 
     * @return string  "UPDATE myTable SET column1=value1, ..." 
     */
    public static function prepareUpdateSql(string $tableToUpdate, mixed $dataToUpdate, mixed $where, bool $isParameterized): string
    {
        if (is_object($dataToUpdate)) $dataToUpdate = get_object_vars($dataToUpdate);
        $updateClause = "";
        foreach ($dataToUpdate as $column => $value) {
            if ($isParameterized) $updateClause .= "$column=:$column, ";
            else $updateClause .= "$column='$value', ";
        }

        $updateClause = rtrim($updateClause, ", ");

        $whereClause = "";
        //If $where has value, include it in where clause-
        if (isset($where)) {
            //If $where is an array/object-
            if (is_object($where) || is_array($where)) {
                //If $where is an object, make it an array-
                if (is_object($where)) $where = get_object_vars($where);
                foreach ($where as $column => $value) {
                    if ($isParameterized) $whereClause .= "$column=:$column AND ";
                    else $whereClause .= "$column='$value' AND ";
                }
                $whereClause = "WHERE " . rtrim($whereClause, " AND ");
            } else {
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
    public function delete(string $sql, mixed $param = null): int
    {
        try {
            if ($param) {
                if (is_object($param)) $param = get_object_vars($param);
                $statement = $this->prepare($sql);
                $statement->execute($param);
                return $statement->rowCount();
            } else {
                $statement = $this->query($sql);
                return $statement->rowCount();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
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
    public static function prepareDeleteSql(mixed $where, string $tableName, bool $isParameterized): string
    {

        if (is_object($where)) $where = get_object_vars($where);
        $whereClause = "";
        foreach ($where as $column => $value) {
            if ($isParameterized) $whereClause .= "$column=:$column AND ";
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
    public static function prepareWhereClause(mixed $where, bool $isParameterized): string
    {
        if (is_object($where)) $where = get_object_vars($where);

        $sql = "";
        foreach ($where as $column => $value) {
            if ($isParameterized) {
                $sql .= "$column=:$column AND ";
            } else {
                $sql .= "$column='$value' AND ";
            }
        }

        $sql = rtrim($sql, " AND ");

        return  " WHERE " . $sql;
    }
    #endregion

    private function executeFetch(string $sql, mixed $args = null, int $fetchMode = PDO::FETCH_ASSOC, bool $fetchAll = false)
    {
        try {
            if ($args) {
                if (is_object($args)) $args = get_object_vars($args);
                $statement = $this->prepare($sql);
                $statement->setFetchMode($fetchMode);
                $statement->execute($args);
                return $fetchAll ? $statement->fetchAll() : $statement->fetch();
            } else {
                $statement = $this->query($sql);
                $statement->setFetchMode($fetchMode);
                return $fetchAll ? $statement->fetchAll() : $statement->fetch();
            }
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }

    /**
     * fetchColumnValues()
     * 
     * Fetches a single column from all rows in the result set.
     * 
     * @param string $sql  SQL statement (parameterized or not).
     * @param mixed $args  null/object/array. 
     * @param int $columnNumber  The column number to fetch (0-indexed).
     * @return array  Array of values from the specified column.
     * @throws PDOException.
     */
    public function fetchColumnValues(string $sql, mixed $args = null, int $columnNumber = 0): array
    {
        try {
            if ($args) {
                if (is_object($args)) $args = get_object_vars($args);
                $statement = $this->prepare($sql);
                $statement->execute($args);
            } else {
                $statement = $this->query($sql);
            }
            return $statement->fetchAll(PDO::FETCH_COLUMN, $columnNumber);
        } catch (\Throwable $e) {
            $backTraceLog = $this->debugBacktrace();
            throw new PDOException("PDOException: " . $e->getMessage() . ". SQL: $sql, $backTraceLog", (int) $e->getCode(), $e);
        }
    }
}
