<?php

/**
 * Post.php
 * @author Asif Chowdhury
 * @date 2013-04-07
 * Used as a Post Object to be included in the controller object
 * 
 * Copyright (C) 2011,2012,2013  Asif Chowdhury
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
class Post 
{
    public $num_parameters;
    public $parameters;
    
    /**
     * Constructor - grabs all the values from the post array and save it in the Post object's
     *		parameters array...also instantiates the object 
     */
    public function __construct()
    {
	$this->parameters   = array();
	foreach($_POST as $var => $value)
	{
	    $this->parameters[$var] = $value;
	}
    }
    
    /**
     * get_safe_value	- gets the safe value of the post value. 
     * @author Asif Chowdhury'
     * @date 2013-03-4-07
     * 
     * @param $index
     * @param $filter 
     *		Can be one of
     *		- url
     *		- full_special_chars
     *		- int
     *		- float
     *		- encoded
     *		- email
     *		- unfiltered
     *		- string
     * @return string
     */
    public function get_safe_value($index, $filter = "string")
    {
	$return	    = '';
	switch($filter)
	{
	    case 'url':
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_URL);
		break;
	    case 'full_special_chars':
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		break;
	    case 'int':
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_NUMBER_INT);
		break;
	    case 'float':
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_NUMBER_FLOAT);
		break;
	    case 'encoded':
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_ENCODED);
		break;
	    case 'email':
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_EMAIL);
		break;
	    case 'unfiltered':
		$return	= filter_var(trim($this->parameters[$index]), FILTER_UNSAFE_RAW);
		break;
	    case 'string':
	    default:
		$return	= filter_var(trim($this->paramters[$index]), FILTER_SANITIZE_STRING);
		break;
	}
	return $return;
    }
}
?>
