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
    //private $resultsDict = array();
    private $resultArray = array();
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
    public function execute($insert=false, $areThereParams=true)
    {
        $mysqli = $this->getSQL();
        $stmt = $mysqli->prepare($this->prepared);
        if ($areThereParams == true) {
            $stmt->bind_param($this->paramTypes, ...$this->bindedParams);
        }
        $executed = $stmt->execute();
        if ($executed) {
            if ($insert == false) {
                $meta = $stmt->result_metadata();
                $params = array();
                $cols = array();
                //$rowArr = array();
                $sql_obj = null;
                while ($field = $meta->fetch_field()) {
                    $params[] = &$cols[$field->name];
                }
                call_user_func_array(array($stmt, 'bind_result'), $params);
                while ($stmt->fetch()) {
                    $sql_obj = new SQLObj();
                    $rowArr = array();
                    foreach ($cols as $key => $val) {
                        $sql_obj->$key = $val;
                        $rowArr[$key] = $val;
                        $this->num_rows++;
                    }
                    $this->resultArray[] = $rowArr;
                    $this->sql_objs[] = $sql_obj;
                }
            }
            $mysqli->close();
            return True;
        }
        $this->resultArray = null;
        $this->bindedResults = null;
        $mysqli->close();
        return False;
    }
    private $assocIndex = -1;
    public function get_both_array_results() {
        $this->assocIndex++;
        if($this->assocIndex < sizeof($this->resultArray)) {
            return $this->resultArray[$this->assocIndex];
        }
        return false;
    }
    private $objsIndex = -1;
    public function get_results_as_objs() {
        $this->objsIndex++;
        if($this->objsIndex < sizeof($this->sql_objs)) {
            return $this->sql_objs[$this->objsIndex];
        }
        return false;
    }
    // Only should be used when there is 1 row
    public function get_sql_obj() {
        return $this->sql_objs[0];
    }
