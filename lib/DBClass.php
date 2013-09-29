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
   * 2012-09-23
   * Updated to be more database agnostic at least allow for mysqli, postgres, oracle, and mssql
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
  private $DBType;
  private $oOracleResVal	= null;
  
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
    $this->DBPassword	= $dbInfo['database_password'];
    $this->DBDatabase	= $dbInfo['database_name'];
    $this->DBType	= ((isset($dbInfo['database_type']) and !empty($dbInfo['database_type'])) ? $dbInfo['database_type'] : 'mysql');
    $this->DBPort	= ((isset($dbInfo['database_port']) and !empty($dbInfo['database_port']) and is_numeric($dbInfo['database_port'])) ? $dbInfo['database_port'] : 3306);

    switch($this->DBType) {
	case 'mssql':
	    $this->DBConnection	= @mssql_connect();
	    if (! $this->DBConnection) {
	      print "DBError: Could not connect to DB Server\n";
	      exit;
	    }
	    mssql_select_db($this->DBDatabase, $this->DBConnection);
	    break;
	case 'oracle':
	    $this->DBConnection	= @oci_connect($this->$DBUser, $this->DBPassword, "//{$this->DBHost}:{$this->DBPort}/{$this->DBDatabase}");
	    if (! $this->DBConnection) {
	      print "DBError: Could not connect to DB Server: " . oci_error() . "\n";
	      exit;
	    }
	    break;
	case 'postgres':
	    $this->DBConncetion	= @pg_connect("host={$this->DBHost} port={$this->DBPort} dbname={$this->DBDatabse} user={$this->DBUser} password={$this->DBPassword}");
	    if (! $this->DBConnection) {
	      print "DBError: Could not connect to DB Server\n";
	      exit;
	    }
	    break;
	case 'mysql':
	    // mysql will now always use mysqli
	case 'mysqli':
	default:
	    $this->DBConnection	=  mysqli_connect($this->DBHost, $this->DBUser, $this->DBPassword, $this->DBDatabase, $this->DBPort);
	    if (!$this->DBConnection) {
		print "DBError: Could not connect to DB Server: " . mysqli_connect_error() . "\n";
		exit;
	    }
	    break;
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
    switch($this->DBType) {
	case 'mssql':
	    $this->DBConnection	= @mssql_connect();
	    if (! $this->DBConnection) {
	      print "DBError: Could not connect to DB Server\n";
	      exit;
	    }
	    mssql_select_db($this->DBDatabase, $this->DBConnection);
	    break;
	case 'oracle':
	    $this->DBConnection	= @oci_connect($this->$DBUser, $this->DBPassword, "//{$this->DBHost}:{$this->DBPort}/{$this->DBDatabase}");
	    if (! $this->DBConnection) {
	      print "DBError: Could not connect to DB Server: " . oci_error() . "\n";
	      exit;
	    }
	    break;
	case 'postgres':
	    $this->DBConncetion	= @pg_connect("host={$this->DBHost} port={$this->DBPort} dbname={$this->DBDatabse} user={$this->DBUser} password={$this->DBPassword}");
	    if (! $this->DBConnection) {
	      print "DBError: Could not connect to DB Server\n";
	      exit;
	    }
	    break;
	case 'mysql':
	    // mysql will now always use mysqli
	case 'mysqli':
	default:
	    $this->DBConnection	=  mysqli_connect($this->DBHost, $this->DBUser, $this->DBPassword, $this->DBDatabase, $this->DBPort);
	    if (!$this->DBConnection) {
		print "DBError: Could not connect to DB Server: " . mysqli_connect_error() . "\n";
		exit;
	    }
	    break;
    }
  }
  
  /**
   * dbExec - outputs nothing; used to run an sql query
   * @parameter ($sSql) as the the sql query
   */
  public function dbExec($sSQL) {
	$rs_result    = false;
	switch($this->DBType) {
		case 'mssql':
			$rs_result	= mssql_query($sSQL, $this->DBConnection);
			break;
		case 'oracle':
			$oci_std	= oci_parse($this->DBConnection, $sSQL);
			$this->oOracleResVal		= OCINewDescriptor($this->DBConnection, OCI_D_LOB);
			oci_bind_by_name($oci_std, ":res", $this->oOracleResVal, -1, OCI_B_CLOB);
			$rs_result	= oci_execute($oci_std);
			break;
		case 'postgres':
			$rs_result	= pg_query($sSQL, $this->DBConnection);
			break;
		case 'mysqli':
		case 'mysql':
		default:
			$rs_result	= mysqli_query($this->DBConnection, $sSQL);
			break;
	}
	$this->DBResource	= $rs_result;
	return $rs_result;
  }
  
  /*
   * dbQuery - outputs a modified database; used to run an sql query
   * @parameter ($sql) - the sql query to run
   * @return boolean
   */
  public function dbQuery($sql) {
    $rs = $this->dbExec($sql);
    if ($rs == true) {
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
      $aReturn	= array();
      switch($this->DBType) {
	  case 'mssql':
	      $rs   = mssql_query($this->DBConnection, "SELECT * FROM information_schema.columns WHERE table_name = '{$sTable}' ORDER BY ordinal_position");
	      while($aRow	= myssql_fetch_assoc($rs)) {
		  array_push($aReturn, $aRow['Field']);
	      }	      
	      break;
	  case 'oracle':
	      $rs   = oci_parse($this->DBConnection, "DESCRIBE {$sTable}");
	      while($aRow	= oci_fetch_assoc($rs)) {
		  array_push($aReturn, $aRow['Field']);
	      }	      
	      break;
	  case 'postgres':
	      $rs   = pg_query($this->DBConnection, "\d+ {$sTable}");
	      while($aRow	= pg_fetch_assoc($rs)) {
		  array_push($aReturn, $aRow['Field']);
	      }	      
	      break;
	  case 'mysql':
	  case 'mysqli':
	  default:
	      $rs = mysqli_query($this->DBConnection, "DESCRIBE {$sTable}");
	      while($aRow	= mysqli_fetch_assoc($rs)) {
		  array_push($aReturn, $aRow['Field']);
	      }
	      break;      
      }
      return $aReturn;
  }

  /**
   * dbGetDBType    - gets the database type
   * @return string
   */
  public function dbGetDBType() {
      return $this->DBType;
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
    switch($this->DBType) {
	case 'mssql':
	    while ($row = mssql_fetch_assoc($rs)) {
	      array_push($resArray, $row);
	    }	    
	    break;
	case 'oracle':
	    while ($row = oci_fetch_assoc($rs)) {
	      array_push($resArray, $row);
	    }	    
	    break;
	case 'postgres':
	    while ($row = pg_fetch_assoc($rs)) {
	      array_push($resArray, $row);
	    }
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    while ($row = mysqli_fetch_assoc($rs)) {
	      array_push($resArray, $row);
	    }
	    break;
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
    switch($this->DBType) {
	case 'mssql':
	    while ($row = mssql_fetch_row($rs)) {
	      array_push($resArray, $row);
	    }	    
	    break;
	case 'oracle':
	    while ($row = oci_fetch_row($rs)) {
	      array_push($resArray, $row);
	    }	    
	    break;
	case 'postgres':
	    while ($row = pg_fetch_row($rs)) {
	      array_push($resArray, $row);
	    }
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    while ($row = mysqli_fetch_row($rs)) {
	      array_push($resArray, $row);
	    }
	    break;
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
    $a_row  = array();
    
    switch($this->DBType) {
	case 'mssql':
	    $a_row = mssql_fetch_row($rs);
	    break;
	case 'oracle':
	    $a_row = oci_fetch_row($rs);
	    break;
	case 'postgres':
	    $a_row = pg_fetch_row($rs);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $a_row = mysqli_fetch_row($rs);
	    break;
    }

    return $a_row;
}

  /**
   * dbGetRowHash - outputs nothing; used to grab a single row out of the database
   * @parameter ($sql) as the sql query
   * @return hash
   */
  public function dbGetRowHash($sql) {
    $rs = $this->dbExec($sql);
    $a_row  = array();
    
    switch($this->DBType) {
	case 'mssql':
	    $a_row = mssql_fetch_assoc($rs);
	    break;
	case 'oracle':
	    $a_row = oci_fetch_assoc($rs);
	    break;
	case 'postgres':
	    $a_row = pg_fetch_assoc($rs);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $a_row = mysqli_fetch_assoc($rs);
	    break;
    }

    return $a_row;
  }

  /*
   * dbGetOne - outputs nothing; used to grab one value out of the database
   * @parameter ($sql) as teh sql query
   * @return string
   */
  public function dbGetOne($sql) {
    $rs	= $this->dbExec($sql);
    $a_row  = array();
    
    switch($this->DBType) {
	case 'mssql':
	    $a_row = mssql_fetch_row($rs);
	    break;
	case 'oracle':
	    $a_row = oci_fetch_row($rs);
	    break;
	case 'postgres':
	    $a_row = pg_fetch_row($rs);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $a_row = mysqli_fetch_row($rs);
	    break;
    }

    return ((!empty($a_row)) ? $a_row[0] : false);
  }
 
  /**
   * dbNumRows - outputs nothing; used to grab the number of rows returned from a database query
   * @parameter ($rs) the last query result
   * @return number
   */
  public function dbNumRows($rs) {
    $n_num_rows   = 0;

    switch($this->DBType) {
	case 'mssql':
	    $n_num_rows = mssql_num_rows($rs);
	    break;
	case 'oracle':
	    while($row	= oci_fetch_row($rs)) {
		$n_num_rows++;
	    }
	    break;
	case 'postgres':
	    $n_num_rows = pg_num_rows($rs);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $n_num_rows = mysqli_num_rows($rs);
	    break;
    }
      
    return $n_num_rows;
  }

  /**
   * dbNumRows - outputs nothing; used to grab the number of rows returned from a database query
   * @parameter ($rs) the last query result
   * @return number
   */
  public function dbGetNumRows() {
    $n_num_rows   = 0;

    switch($this->DBType) {
	case 'mssql':
	    $n_num_rows = mssql_num_rows($this->DBResource);
	    break;
	case 'oracle':
	    while($row	= oci_fetch_row($rs)) {
		$n_num_rows++;
	    }
	    break;
	case 'postgres':
	    $n_num_rows = pg_num_rows($this->DBResource);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $n_num_rows = mysqli_num_rows($this->DBResource);
	    break;
    }
      
    return $n_num_rows;
  }

  /**
   * dbGetAffectedRows - outputs nothing; used to return the total number of affected rows from the last mod query
   * @return a number
   */
  public function dbGetAffectedRows() {
    $n_num_rows   = 0;

    switch($this->DBType) {
	case 'mssql':
	    $n_num_rows = mssql_rows_affected($this->DBResource);
	    break;
	case 'oracle':	    
	    $n_num_rows	= oci_num_rows($this->DBResource);
	    break;
	case 'postgres':
	    $n_num_rows = pg_affected_rows($this->DBResource);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $n_num_rows = mysqli_affected_rows($this->DBResource);
	    break;
    }
      
    return $n_num_rows;
  }

  /**
   * dbGetTables - get all the tables in teh database
   * @return list
   */
  public function dbGetTables() {
      $a_tables	= array();
      
      switch($this->DBType) {
	case 'mssql':
	    $rs   = $this->dbExec("SELECT name FROM {$this->DBDatabase}..sysobjects WHERE xtype = 'U'");
	    while ($aRow = mssql_fetch_row($rs)) {
		array_push($a_tables, $aRow[0]);
	    }
	    break;
	case 'oracle':
	    $rs	= $this->dbExec("SELECT * FROM dict");
	    while ($aRow = oci_fetch_row($rs)) {
		array_push($a_tables, $aRow[0]);
	    }
	    break;
	case 'postgres':
	    $rs	= $this->dbExec("\d");
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $rs = $this->dbExec("show tables;");
	    while ($aRow = mysqli_fetch_array($rs)) {
	      array_push($a_tables, $aRow[0]);
	    }	    
      }
      return $a_tables;
  }

  /**
   * dbGetInsertID - outputs nothing; used to grab the last insert ID from an auto update.
   * @return number
   */
  public function dbGetInsertID() {
    $n_id   = false;
    switch($this->DBType) {
	case 'mssql':
	    $n_id	= $this->dbGetOne("SELECT @@IDENTITY AS 'Identity'");
	    break;
	case 'oracle':	    
	    echo "Cannot get an ID from an Oracle Insert!";
	    break;
	case 'postgres':
	    $n_id	= $this->dbGetOne("SELECT LASTVAL()");
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $n_id = mysqli_insert_id($this->DBConnection);
	    break;
    }
      
    return $n_id;
  }

  /**
   * dbGetError - outputs nothing; used to grab the latest database error
   * @return string
   */
  public function dbGetError() {
    $s_error   = '';

    switch($this->DBType) {
	case 'mssql':
	    $s_error = mssql_get_last_message();
	    break;
	case 'oracle':
	    $a_error	= oci_error($this->DBConnection);
	    $s_error	= ((!empty($a_error) and isset($a_error['message'])));
	    break;
	case 'postgres':
	    $n_num_rows = pg_result_error($this->DBResource);
	    break;
	case 'mysql':
	case 'mysqli':
	default:
	    $n_num_rows = mysqli_error($this->DBConnection);
	    break;
    }
 
    return $s_error;
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
    return mysqli_data_seek($rs, $location);
  }

  /**
   * dbSeek - outputs a modified data pointer; used to see the data pointer to the appropriate location
   * @parameter ($location) the location where to seek to
   * @return boolean
   */
  public function dbSeek($location) {
    return mysqli_data_seek($this->DBResource, $location);
  }
  
  /**
   * Changes the given result set to an array
   * @param resultset to be turned into an array
   * @param string optional indicator of whether to produce an indexed or an associative array
   * @return array The two-dimensional array created from the result set
   */
  public function dbResultToArray($rs, $sType = "assoc") {
    $aResult = array();
    while($aRow = (($sType == "assoc") ? mysqli_fetch_array($rs, MYSQL_ASSOC) : mysqli_fetch_row($rs))) {
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
      while ($row = mysqli_fetch_assoc($this->DBResource)) {
	array_push($reRay, $row);
      }
    }
    else {
      while ($row = mysqli_fetch_array($this->DBResource)) {
	array_push($reRay, $row);
      }
    }

    return $reRay;
  }
    
  /**
   * dbQuote - mysql quote a string
   * @parameter ($value)
   * @return string 
   */
  public function dbQuote($value) {
      return mysqli_real_escape_string($this->DBConnection, $value);
  }
}

?>