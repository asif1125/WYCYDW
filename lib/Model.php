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
  protected $sBaseTable;
  
  /**
   * constructor	- creates a Model Object using a given database object
   * @paramter $oDB	- the main database object for this model
   */
  public function __construct($oDB) {
    $this->oDB	= $oDB;
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