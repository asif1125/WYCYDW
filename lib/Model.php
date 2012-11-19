<?php
  /**
   * Model.php
   * written by Asif Chowdhury
   * 2009-12-11
   * used as a Model Base Object for database specific information
   *
   * 2011-05-27
   * usurped by the WYCYDW framework
   * 
   * 2012-09-23 - added another abstraction layer to help generate queries without as much SQL.
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
class Model {
  private $oDB;
  protected $sDBType;
  protected $sBaseTable;
  protected $sSQL;
  protected $sSelect;
  protected $sFrom;
  protected $aJoins;
  protected $aWhere;
  protected $sGroupBy;
  protected $sHaving;
  protected $sOrderBy;
  protected $sLimit;
  
  /**
   * constructor	- creates a Model Object using a given database object
   * @paramter $oDB	- the main database object for this model
   */
  public function __construct($oDB) {
    $this->oDB	= $oDB;
    $this->aJoins   = array();
    $this->aWhere   = array();
  }
  
  /**
   * select - create the select portion of a query
   * @param type $s_select 
   */
  public function select($s_select) {
      $this->sSelect	= $s_select;
  }
  
  /**
   * from - create the from potsion of a query
   * @param type $s_table 
   */
  public function from($s_table) {
      $this->sFrom  = $s_table;
  }
  
  /**
   * join - add a join portion to a generated query
   * @param type $s_table
   * @param type $s_on
   * @param type $s_join_type 
   */
  public function join($s_table, $s_on, $s_join_type = "LEFT OUTER") {
      $s_join	= $s_join_type . " JOIN " . $s_table . " ON " . $s_on;
      array_push($this->aJoins, $s_join);
  }

  /**
   * where - add a portion of the where to a generated sql
   * @param type $s_column
   * @param type $s_value 
   */
  public function where($s_column, $s_value = '') {
      if(empty($s_value)) {
	  $s_where  = $s_column;
      }
      else {
	  $s_where  = $s_colume . " = '" . $s_value . "'";
      }
      if(empty($this->aWhere)) {
	  array_push($this->aWhere, $s_where . " ");
      }
      else {
	  array_push($this->aWhere, "AND " . $s_where . " ");
      }
  }
  
  /**
   * where_in - add a portion of the where to a generated sql using a where in
   * @param type $s_column
   * @param type $a_values 
   */
  public function where_in($s_column, $a_values) {
      $s_values = "'" . implode("', '", $a_values) . "'";
      if(empty($this->aWhere)) {
	  $s_where  = $s_column . " IN (" . $s_values . ") ";
      }
      else {
	  $s_where = "AND " . $s_where . " IN {" . $_values . ") "; 
      }
  }
  
  /**
   * group_by - add a group by portion of a generated query;
   * @param type $s_group_by 
   */
  public function group_by($s_group_by) {
      $this->sGroupBy	= $s_group_by;
  }
  
  /**
   * having - add a having portion of a generated query
   * @param type $s_having 
   */
  public function having($s_having) {
      $this->sHaving	= $s_having;
  }
  
  /**
   * order_by - add the order by portion of a generated sql query
   * @param type $s_order_by 
   */
  public function order_by($s_order_by) {
      $this->sOrderBy	= $s_order_by;
  }
  
  /**
   * limit - (MySQL only) adds a limit portion to a generated query.
   * @param type $s_limit 
   */
  public function limit($s_limit) {
      $this->sLimit = $s_limit;
  }
  
  /**
   * delete - removes a row from a table given some wheres.
   * @param $s_table
   * @return boolean
   */
  public function delete($s_table) {
      $s_where	= '';
      foreach($this->aWhere as $s_single_where) {
	  $s_where .= $s_single_where;
      }
      $s_delete = "DELETE FROM " . $s_table . " WHERE " . $s_where;
      return $this->oDB->dbQuery($s_delete);
  }
  
  /**
   * insert - generate insert sql and run it.
   * @param type $s_table
   * @param type $a_values
   * @return type 
   */
  public function insert($s_table, $a_values) {
      $s_columns    = '';
      $s_values	    = '';
      
      // grab the key for each column
      foreach($a_values as $s_column => $s_value) {
	  $s_columns .= ((!empty($s_columns)) ? ', ' : '') . $s_column;
	  $s_values .= ((!empty($s_values)) ? "', '" : '') . $s_value;
      }
      
      $s_sql	= "INSERT INTO {$s_table} ({$s_columns} VALUES ('{$s_values}')";
      $this->oDB->dbQuery($s_sql);
      return $this->oDB->dbGetInsertID();
  }
  
  /**
   * update - generates an update query and runs it given a where.
   * @param type $s_table
   * @param type $a_values 
   */
  public function update($s_table, $a_values) {
      $s_update	= '';
      $s_where	= '';
      foreach($this->aWhere as $s_single_where) {
	  $s_where .= $s_single_where;
      }
      foreach ($a_values as $s_column => $s_value) {
	  $s_update .= ((!empty($s_update)) ? ", " : '') . $s_column . "='" . $s_value . "'";
      }
      $s_sql	= "UPDATE " . $s_table . " SET " . $s_update . " WHERE " . $s_where;
  }
  
  /**
   * get - gets the generated query
   * @return a database result object
   */
  public function get() {
      $s_join	= '';
      foreach($this->aJoins as $s_single_join) {
	  $s_join .= $s_single_join;
      }
      $s_where	= '';
      foreach($this->aWhere as $s_single_where) {
	  $s_where .= $s_single_where;
      }
      $s_sql	= "SELECT " . $this->sSelect . " FROM " . $this->sFrom . ' ' . 
			$s_join . " " . 
			((!empty($s_where)) ? 'WHERE ' . $s_where : '') . ' ' .
			((!empty($this->sGroupBy)) ? 'GROUP BY ' . $this->sGroupBy : '') . ' ' .
			((!empty($this->sHaving)) ? 'HAVING ' . $this->sHaving : '') . ' ' . 
			((!empty($this->sOrderBy)) ? 'ORDER BY ' . $this->sOrderBy : '') . ' ' .
			((!empty($this->sLimit)) ? 'LIMIT ' . $this->sLimit : '');
      return new DBResult($this->oDB->dbExec($s_sql), $this->oDB->dbGetDBType());
  }
  
  /**
   * get_query - gets the generated query string
   * @return a string
   */
  public function get_query() {
      $s_join	= '';
      foreach($this->aJoins as $s_single_join) {
	  $s_join .= $s_single_join;
      }
      $s_where	= '';
      foreach($this->aWhere as $s_single_where) {
	  $s_where .= $s_single_where;
      }
      $s_sql	= "SELECT " . $this->sSelect . " FROM " . $this->sFrom . ' ' . 
			$s_join . " " . 
			((!empty($s_where)) ? 'WHERE ' . $s_where : '') . ' ' .
			((!empty($this->sGroupBy)) ? 'GROUP BY ' . $this->sGroupBy : '') . ' ' .
			((!empty($this->sHaving)) ? 'HAVING ' . $this->sHaving : '') . ' ' . 
			((!empty($this->sOrderBy)) ? 'ORDER BY ' . $this->sOrderBy : '') . ' ' .
			((!empty($this->sLimit)) ? 'LIMIT ' . $this->sLimit : '');
      return $s_sql;
  }
  
  /**
   * @access protected
   * getSingleValue - grabs one single value from an expected resultset
   * @param <type> $sQuery 
   * @return string
   */
  protected function getSingleValue($sQuery) {
      $sResult	= $this->oDB->dbGetOne($sQuery);
      return $sResult;
  }

  /**
   * @access protected
   * getSingleRow - grabs a single row from database and returns the result to the caller
   * @param $sQuery - the SQL query you want to run
   * @return array list
   */
  protected function getSingleRowList($sQuery) {
    $aResult	= $this->oDB->dbGetRowList($sQuery);
    return $aResult;
  }

  /**
   * @access protected
   * getSingleRowHash - grabs a single hash from the database and returns it to the caller
   * @param $sQuery - the SQL query you want to run
   * @return array hash
   */
  protected function getSingleRowHash($sQuery) {
      $aResult	= $this->oDB->dbGetRowHash($sQuery);
      return $aResult;
  }

  /**
   * @access protected
   * getAllHash - grabs all rows as hashes from the database and returns it to the caller
   * @param $sQuery - the SQL query you want to run
   * @return array hash
   */
  protected function getAllHash($sQuery) {
      $aResult	= $this->oDB->dbGetAllHash($sQuery);
      return $aResult;
  }

  /**
   * @access protected
   * getAllList	- grabs all rows as lists from the database and returns it to the caller
   * @param $sQuery - the SQL query you want to run
   * @return array list
   */
  protected function getAllList($sQuery) {
	$aResult	= $this->oDB->dbGetAllList($sQuery);
	return $aResult;
  }

  /**
   * @access protected
   * runQuery	- runs a query without expecting a result.
   * @param $sQuery   - the query to run
   * @return boolean
   */
  protected function runQuery($sQuery) {
      return $this->oDB->dbQuery($sQuery);
  }

  /**
   * @access protected
   * getDBObject    - gets the database object for this Model
   * @return a DBClass Object reference
   */
  protected function getDBObject() {
      return $this->oDB;
  }

  /**
   * @access protected
   * getDBError	- gets the error for the last query if there was one
   * @return string 
   */
  protected function getDBError() {
      return $this->oDB->dbGetError();
  }

  /**
   * @access protected
   * getNumAffectedRows	- gets the number of affected rows by the last query
   * @return number
   */
  protected function getNumAffectedRows() {
      return $this->oDB->dbGetNumAffectedRow();
  }

  /**
   * @access protected
   * getNumRows	- grab the number of rows in the last query.
   * @return number
   */
  protected function getNumRows() {
	return $this->oDB->dbGetNumRows();
  }

  /**
   * @access protected
   * getInsertID    - get the last insertID from the last insert
   * @return number
   */
  protected function getInsertID() {
	return $this->oDB->dbGetInsertID();
  }

  /**
   * @access protected
   * setBaseTable   - sets the main table for this model
   * @param $sTable - the able to set this
   */
  protected function setBaseTable($sTable) {
	$this->sBaseTable	= $sTable;
  }
}
?>