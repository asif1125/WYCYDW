<?php
  /**
   * @author Asif Chowdhury
   * DB class
   * written 2007-06-28
   * used to be the main class that handles database activity
   *
   * 2011-05-27
   * usurped and modified by the WYCYDW framework (ACE)
   * 
   * Copyright (C) 2011  Asif Chowdhury
   * 
   * This program is free software: you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation, either version 3 of the License, or
   * (at your option) any later version.
   * 
   * This program is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   * GNU General Public License for more details.
   * 
   * You should have received a copy of the GNU General Public License
   * along with this program.  If not, see <http://www.gnu.org/licenses/>.
   */
class DBClass {
  private $DBHost;
  private $DBUser;
  private $DBPassword;
  private $DBDatabase;
  private $DBConnection;
  
  // added properties
  private $DBError = null;
  private $DBAffectedRows;
  private $DBResource;
  private $DBLogFile;

  /**
   * constructor - outputs nothing; used to create a connection to the database
   * @parameter ($dbInfo) array containing the database login info
   * @return object
   */
  public function __construct($dbInfo) {
    $this->DBHost = $dbInfo['database_host'];
    $this->DBUser = $dbInfo['database_username'];
    $this->DBPassword = $dbInfo['database_password'];
    $this->DBDatabase = $dbInfo['database_name'];
    $this->DBConnection = @mysql_connect($dbInfo['database_host'], $dbInfo['database_username'], $dbInfo['database_password']);
    
    if (! $this->DBConnection) {
      print "DBError: Could not connect to DB Server\n" . mysql_error();
      exit;
    }

    if (! mysql_select_db($dbInfo['database_name'], $this->DBConnection)) {
      print "DBError: Could not assign database\n";
      exit;      
    }
  }
   
  /**
   * getDBHost - outputs nothing; accessor to grab the db host
   * @return string
   */
  public function getDBHost() {
    return $this->DBHost;
  }

  /**
   * getDBUser - outputs nothing; accessor to grab the db username
   * @return string
   */
  public function getDBUser() {
    return $this->DBUser;
  }

  /**
   * getDBDatabase - outputs nothing; accessor to grab the db name
   * @return string
   */
  public function getDBDatabase() {
    return $this->DBDatabase;
  }

  /**
   * getDBConnection - outputs nothing; accessor to grab the DB Connection
   * @return string
   */
  public function getDBConnection() {
    return $this->DBConnection;
  }

  /**
   * getDBResource - outputs nothing; accessor to grab the last affected query result
   * @return resource
   */
  public function getDBResource() {
    return $this->DBResource;
  }

  /**
   * getDBHost - outputs nothing; modifier to set the db host
   * @parameter ($host) for the host
   */
  public function setDBHost($host) {
    $this->DBHost = $host;
  }
   
  /**
   * setDBUser - outputs nothing; modifier to set the username
   * @parameter ($user) for the database username
   */
  public function setDBUser($user) {
    $this->DBUser = $user;
  }

  /**
   * setDBPassword - outputs nothing; modifier to set the db password
   * @parameter ($password) for the db user password
   */
  public function setDBPassword($password) {
    $this->DBPassword = $password;
  }

  /**
   * setDBDatabase - outputs nothing; modifier to set the database name
   * @parameter ($db) for the database namme
   */
  public function setDBDatabase($db) {
    $this->DBDatabase = $db;
  }

  /**
   * setDBResource - outputs nothing; modifier to set the object's last affected query result
   * @parameter ($rs) as the last affected result
   */
  public function setDBResource($rs) {
    $this->DBResource = $rs;
  }

  /**
   * conntectDB - outputs a DB connection; used to connect to a database
   */
  public function connectDB() {
    $this->DBConnection = @mysql_connect($this->DBHost, $this->DBUsername, $this->DBPassword);
    if (! $this->DBConnection) {
      print "DBError: Cannot connect to the database server\n";
      exit;
    }

    if (! mysql_select_db($this->DBdatabase, $this->DBConnection)) {
      print "DBError: Cannot assign database\n";
      exit;
    }
  }
  
  /**
   * dbExec - outputs nothing; used to run an sql query
   * @parameter ($sSql) as the the sql query
   * @parameter ($nRowsPerPage) as the number of rows to select for the limit
   * @parameter ($nPage) as the starting page for the limit 
   * @parameter ($updateAffectedRows) boolean as to whether we should set the affectedRows variable
   * @return a database query resource (result)
   */
  public function dbExec($sSQL, $nRowsPerPage = 0, $nPage = 0, $UpdateAffectedRows=true) {
    if(preg_match("%^select%i", $sSQL) and (! preg_match("%limit%i", $sSQL)) and ($nRowsPerPage != 0) and ($nPage != 0)) {
      $sSQL .= " LIMIT " . ($nRowsPerPage * ($nPage - 1)) . ", " . $nRowsPerPage;
    }

    // grab the results
    $rs = mysql_query($sSQL, $this->DBConnection);
    $this->DBError = mysql_error();

    // udpate the affected row
    if ($UpdateAffectedRows) {
      $this->DBAffectedRows  = mysql_affected_rows();
    }

    if (! $rs) {
      print "DBError: There was a resource error: invalid resource {$this->DBError} in |$sSQL|\n";
      exit;
    }

    // set the resource for this object
    $this->DBResource = $rs;

    // returns the resource
    return $rs;
  }

  /*
   * dbQuery - outputs a modified database; used to run an sql query
   * @parameter ($sql) - the sql query to run
   * @return boolean
   */
  public function dbQuery($sql) {
    $rs = $this->dbExec($sql, 0, 0, true);
    if ($rs) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * dbDescribeTable - grabs all the columns for a table
   * @parameter($sTable) - the table to grab the columns for
   * @return array   
   */
  public function dbDescribeTable($sTable) {
    $rs = mysql_query("describe $sTable;");
    $aReturn = array();
    while($aRow	= mysql_fetch_assoc($rs)) {
      array_push($aReturn, $aRow['Field']);
    }
    return $aReturn;
  }

  /**
   * dbGetAllHash - outputs nothing; used to grab a list of hashes for a query
   * @parameter ($sSql) as the the sql query
   * @parameter ($numRowsPerPage) as the number of rows to select for the limit
   * @parameter ($nPage) as the starting page for the limit 
   * @return array of hashes
   */
  public function dbGetAllHash($sql, $numRowsPerPage = 0, $nPage = 0) {
    $rs = $this->dbExec($sql, $numRowsPerPage, $nPage, false);

    // error checking
    if (! $rs) {
      print "DBError: No Result was returned from the query |$sql|\n";
    }

    // build the hash
    $resArray = array();
    while ($row = mysql_fetch_assoc($rs)) {
      array_push($resArray, $row);
    }

    // return the array
    return $resArray;
  }

  /**
   * dbGetAllList - outputs nothing; used to grab the list of arrays for a query
   * @parameter ($sSql) as the the sql query
   * @parameter ($numRowsPerPage) as the number of rows to select for the limit
   * @parameter ($nPage) as the starting page for the limit 
   * @return array of lists
   */
  function dbGetAllList($sql, $numRowsPerPage = 0, $numPages = 0) {
    $rs = $this->dbExec($sql, $numRowsPerPage, $numPages, false);

    // build the hash
    $resArray = array();
    while ($row = mysql_fetch_row($rs)) {
      array_push($resArray, $row);
    }

    // return the array
    return $resArray;
  }
  
  /**
   * dbGetRowList- outputs nothing; used to grab a single row out of the database
   * @parameter ($sql) as the sql query
   * @return list
   */
  public function dbGetRowList($sql) {
    $rs = $this->dbExec($sql);
    return mysql_fetch_row($rs);
}

  /**
   * dbGetRowHash - outputs nothing; used to grab a single row out of the database
   * @parameter ($sql) as the sql query
   * @return hash
   */
  public function dbGetRowHash($sql) {
    $rs = $this->dbExec($sql);
    return mysql_fetch_assoc($rs);
  }

  /*
   * dbGetOne - outputs nothing; used to grab one value out of the database
   * @parameter ($sql) as teh sql query
   * @return string
   */
  public function dbGetOne($sql) {
    $rs	= $this->dbExec($sql);
    $resRow =  mysql_fetch_row($rs);
    return $resRow[0];
  }
 
  /**
   * dbNumRows - outputs nothing; used to grab the number of rows returned from a database query
   * @parameter ($rs) the last query result
   * @return number
   */
  public function dbNumRows($rs) {
    return mysql_num_rows($rs);
  }

  /**
   * dbNumRows - outputs nothing; used to grab the number of rows returned from a database query
   * @parameter ($rs) the last query result
   * @return number
   */
  public function dbGetNumRows() {
    return mysql_num_rows($this->DBResource);
  }

  /**
   * dbGetAffectedRows - outputs nothing; used to return the total number of affected rows from the last mod query
   * @return a number
   */
  public function dbGetAffectedRows() {
    return mysql_affected_rows($this->DBConnection);
  }

  /**
   * dbGetTables - get all the tables in teh database
   * @return list
   */
  public function dbGetTables() {
    $sql = "show tables;";
    $rs = $this->dbExec($sql);
    $aTables = array();
    while ($aRow = mysql_fetch_array($rs)) {
      array_push($aTables, $aRow[0]);
    }
    return $aTables;
  }

  /**
   * dbGetInsertID - outputs nothing; used to grab the last insert ID from an auto update.
   * @return number
   */
  public function dbGetInsertID() {
    return mysql_insert_id($this->DBConnection);
  }

  /**
   * dbGetError - outputs nothing; used to grab the latest database error
   * @return string
   */
  public function dbGetError() {
    return mysql_error($this->DBConnection);
  }

  /**
   * dbClearError - outputs nothing; used to clear the internal error string
   */
  public function dbClearError() {
    $this->DBError = null;
  }

  /**
   * dbDataSeek - outputs a modified data pointer; used to seek the data pointer to the approprate location
   * @parameter ($rs) the last query result
   * @parameter ($location) the location where to seek to
   * @return boolean
   */
  public function dbDataSeek($rs, $location) {
    return mysql_data_seek($rs, $location);
  }

  /**
   * dbSeek - outputs a modified data pointer; used to see the data pointer to the appropriate location
   * @parameter ($location) the location where to seek to
   * @return boolean
   */
  public function dbSeek($location) {
    return mysql_data_seek($this->DBResource, $location);
  }
  
  /**
   * Changes the given result set to an array
   * @param resultset to be turned into an array
   * @param string optional indicator of whether to produce an indexed or an associative array
   * @return array The two-dimensional array created from the result set
   */
  public function dbResultToArray($rs, $sType = "assoc") {
    $aResult = array();
    while($aRow = (($sType == "assoc") ? mysql_fetch_array($rs, MYSQL_ASSOC) : mysql_fetch_row($rs))) {
      $aResult[] = $aRow;
    }
    
    return $aResult;
  }

  /**
   * dbResToArray - outputs nothing; used to convert the last query resource to an array
   * basically a helper function in addition to getAll* or the getHash or getList functions
   * @parameter ($type) the type of array (hash or list)
   * @return array (hash or list)
   */
  public function dbResToArray($type) {
    $reRay = array();
    if (preg_match("%assoc%i", $type)) {
      while ($row = mysql_fetch_assoc($this->DBResource)) {
	array_push($reRay, $row);
      }
    }
    else {
      while ($row = mysql_fetch_array($this->DBResource)) {
	array_push($reRay, $row);
      }
    }

    return $reRay;
  }
    
}

?>