<?php

  /**
   * DBResult.php
   * written by Asif Chowdhury
   * 2012-09-25
   * used as a DB Result class for iterating through 
   * 
   * Copyright (C) 2012  Asif Chowdhury
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
class DBResult {
	private $s_db_type	= '';
	private $rs_result	= null; 
	private $n_num_rows	= 0;
	private $a_result	= array();
	
	public function __construct($rs_result, $s_db_type = 'mysql') {
		$this->s_db_type    = $s_db_type;
		$this->rs_result    = $rs_result;
	
		switch($this->s_db_type) {
			case "mssql":
				$this->n_num_rows	= mssql_num_rows($this->rs_result);
				break;
			case 'oracle':
				$a_results	= $this->seek(0);
				$this->n_num_rows	= count($a_results);
				OCI-Lob::rewind();
				break;
			case 'postgres':
				$this->n_num_rows	= pg_num_rows($this->rs_result);
				break;
			case 'mysql':
			case 'mysqli':
			default:
				if($this->rs_result !== false)
				    $this->n_num_rows	= mysqli_num_rows($this->rs_result);
				break;
		}
	}
	
	/**
	 * get_db_type - retrieve the database type for this result.
	 * @return string
	 */
	public function get_db_type() {
		return $this->s_db_type;
	}
	
	/**
	 * get_resource - retreive the result resource
	 * @return database resource
	 */
	public function get_resource() {
		return $this->rs_result;
	}

	/**
	 * get_num_rows - returns the number of rows in the resultset
	 * @return number
	 */
	public function get_num_rows() {
		return $this->n_num_rows;
	}
	
	/**
	 * seek - grab a row based off of index...if index is null, then return the next row.
	 * @param $n_index
	 * @param $s_return_type
	 * @return array
	 */
	public function seek($n_index = null, $s_return_type = 'object') {
		$row	= null;
		
		// choose the database type and get the appropriate data
		switch($this->s_db_type) {
			case "mssql":
				// check if we need to relcoate the index pointer.
				if($n_index !== null)
					mssql_data_seek($this->rs_result, $n_index);
				
				// grab the specified format of data
				switch($s_return_type) {
					case 'array':
						$row	= mssql_fetch_row($this->rs_result);
						break;
					case 'assoc':
						$row	= mssql_fetch_assoc($this->rs_result);
						break;
					case 'object':
					default:	
						$row	= mssql_fetch_object($this->rs_result);
						break;
				}
				break;
			case 'oracle':
				// check if we need to relcoate the index pointer.
				if($n_index !== null)
					OCI-Lob::seek($n_index);
				
				// grab the specified format of data
				switch($s_return_type) {
					case 'array':
						$row	= oci_fetch_row($this->rs_result);
						break;
					case 'assoc':
						$row	= oci_fetch_assoc($this->rs_result);
						break;
					case 'object':
					default:	
						$row	= oci_fetch_object($this->rs_result);
						break;
				}
				break;
			case 'postgres':
				// check if we need to relcoate the index pointer.
				if($n_index !== null)
					pg_result_seek($this->rs_result, $n_index);
				
				// grab the specified format of data
				switch($s_return_type) {
					case 'array':
						$row	= pg_fetch_row($this->rs_result);
						break;
					case 'assoc':
						$row	= pg_fetch_assoc($this->rs_result);
						break;
					case 'object':
					default:	
						$row	= pg_fetch_object($this->rs_result);
						break;
				}
				break;
			case 'mysql':
			case 'mysqli':
			default:
				// check if we need to relcoate the index pointer.
				if($n_index !== null)
					mysqli_data_seek($this->rs_result, $n_index);
				
				// grab the specified format of data
				switch($s_return_type) {
					case 'array':
						$row	= mysqli_fetch_row($this->rs_result);
						break;
					case 'assoc':
						$row	= mysqli_fetch_assoc($this->rs_result);
						break;
					case 'object':
					default:	
						$row	= mysqli_fetch_object($this->rs_result);
						break;
				}
				break;
		}
		return $row;
	}

	/**
	 * result - grab the entire dataset as a list of objects, arrays, or hashes 
	 * @param $s_return_type
	 * @return array
	 */
	public function result($s_return_type = 'object') {
		$this->a_result	= array();
		while($row = $this->seek(null, $s_return_type)) {
			array_push($this->a_result, $row);
		}
		return $this->a_result;
	}

	/**
	 * first - grab the first row from the dataset
	 * @param $s_return_type
	 * @return objcet, array, or hash 
	 */
	public function first($s_return_type = 'object') {
		return $this->seek(0, $s_return_type);
	}

	/**
	 * next - grab the next row from the dataset
	 * @param $s_return_type
	 * @return objcet, array, or hash 
	 */
	public function next($s_return_type = 'object') {
		return $this->seek(null, $s_return_type);
	}

	/**
	 * last - grab the last row from the dataset
	 * @param $s_return_type
	 * @return objcet, array, or hash 
	 */
	public function last($s_return_type = 'object') {
		return $this->seek($this->n_num_rows - 1, $s_return_type);
	}
	
}

?>
