<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/20/2017
 * Time: 4:01 PM
 */
require_once 'SQLObj.php';

class SQLHelper
{
    private $host = "";
    private $user = "";
    private $pass = "";
    private $db = "";
    private $port = 0;
    private $paramTypes = "";
    private $bindedParams = array();
    private $bindedResults = array();
    private $resultsDict = array();
    private $prepared = "";
    private $sql_obj = "";
    private $sql_objs = array();
    public function __construct($host, $username, $password, $db, $port)
    {
        $this->host = $host;
        $this->user = $username;
        $this->pass = $password;
        $this->db = $db;
        $this->port = $port;
        $this->sql_obj = new SQLObj(array());
    }

    public function getSQL() {
        return new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);
    }

    public function prepare($stmt) {
        $prepared = $stmt;
        $this->prepared = $prepared;
    }

    public function bindParams($paramTypes, ...$params) {
        $this->paramTypes = $paramTypes;
        $this->bindedParams = $params;
    }

    public $num_rows = 0;
    public function execute($insert=false, $params=true) {
        $mysqli = $this->getSQL();
        $stmt = $mysqli->prepare($this->prepared);
        if($params == true) {
            $stmt->bind_param($this->paramTypes, ...$this->bindedParams);
        }
        $executed = $stmt->execute();
        if($executed) {
            if($insert == false) {
                $meta = $stmt->result_metadata();
                while ($field = $meta->fetch_field()) {
                    $params[] = &$row[$field->name];
                }

                call_user_func_array(array($stmt, 'bind_result'), $params);

                while ($stmt->fetch()) {
                    foreach ($row as $key => $val) {
                        $c[$key] = $val;
                        $this->num_rows++;
                    }
                    $rows[] = $c;
                }
                $this->resultsDict = &$rows;
                // add results to SQLObj class
                //TODO Fix SQLObj to work better and more like MySQLi Objects
                if ($this->num_rows == 1) {
                    // Only works if there is 1 row to work with
                    $this->sql_obj = new SQLObj($this->resultsDict[0]);
                } else {
                    // Multiple rows
                    /*
                    for ($i = 0; $i < $this->num_rows; $i++) {
                        array_push($this->sql_objs, $this->resultsDict[$i]);
                    }
                    */
                }
            }
            $mysqli->close();
            return True;
        }
        $this->resultsDict = null;
        $this->bindedResults = null;
        $mysqli->close();
        return False;
    }
    private $assocIndex = -1;
    public function get_both_array_results() {
        $this->assocIndex++;
        if($this->assocIndex < sizeof($this->resultsDict)) {
            return $this->resultsDict[$this->assocIndex];
        }
        return null;
    }
    private $objsIndex = -1;
    public function get_results_as_objs() {
        $this->objsIndex++;
        if($this->objsIndex < sizeof($this->sql_objs)) {
            return $this->sql_objs[$this->objsIndex];
        }
        return null;
    }
    // Only works if there is 1 row
    public function get_sql_obj() {
        return $this->sql_obj;
    }
    /** /
    private function bind($stmt) {
        $byref_array_for_fields = array();
        call_user_func_array(array($stmt, "bind_result"), $byref_array_for_fields);

// returns a copy of a value
        $copy = create_function('$a', 'return $a;');

        $results = array();
        while ($stmt->fetch()) {
            // array_map will preserve keys when done here and this way
            $results[] = array_map($copy, $byref_array_for_fields);
        }
        return $results;
    }
     /**/

    /** /
    public function fetchAssocStatement($stmt)
    {
        if($stmt->num_rows>0)
        {
            $result = array();
            $md = $stmt->result_metadata();
            $params = array();
            while($field = $md->fetch_field()) {
                $params[] = &$result[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $params);
            $stmt->fetch();
            return $result;
        }

        return null;
    }
    public function get_result_mixed() {}
    public function get_result_object() {}
    /**/
}