<?php
/**
 * page class	- base class for any page in an appication
 * written by Asif Chowdhury
 * 2009-12-12
 * used to be the workhorse for an application
 *
 * 2011-05-27
 * Usurped by the new WYCYDW Frameword (ACE)
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
class Page {
  private $aInfo;
  private $baseDir;
  private $baseURL;
  private $cssDir;
  private $imageDir;
  private $flashDir;
  private $scriptDir;

  /**
   * constructor       - creates a page with standard objects like database conncetion and base loaction information set up.
   */
  public function __construct($aInfo = array()) {
      $this->aInfo  = $aInfo;
      $slash	= DIRECTORY_SEPARATOR;

      if(isset($aInfo['INSTALLDIR']))
	  $this->baseDir    = $aInfo['INSTALLDIR'];
      if(isset($aInfo['baseURL']))
	  $this->baseURL    = $aInfo['baseURL'];
      $this->cssDir	= ((!empty($this->baseDir)) ? $this->baseDir . $slash . "css" : '');
      $this->imageDir	= ((!empty($this->baseDir)) ? $this->baseDir . $slash . "images" : '');
      $this->flashDir	= ((!empty($this->baseDir)) ? $this->baseDir . $slash . "flash" : '');
      $this->scriptDir	= ((!empty($this->baseDir)) ? $this->baseDir . $slash . "js" : '');
  }

  /**
   * **************** Accessors ******************
   */
  
  /**
   * getConfigInfo	- Configuration information for this page/app
   * @return an array
   */
  public function getConfigInfo() {
    return $this->aInfo;
  }

  /**
   * getBaseDir	- grab the application base directory
   * @return a string
   */
  public function getBaseDir() {
    return $this->baseDir;
  }

  /**
   * getBaseURL	- grab the application base URL
   * @return a string
   */
  public function getBaseURL() {
    return $this->baseURL;
  }

  /**
   * getCSSDir	- grab the application CSS directory
   * @return a string
   */
  public function getCSSDir() {
    return $this->cssDir;
  }

  /**
   * getImageDir	- grab the application Image Directory
   * @return a string
   */
  public function getImageDir() {
    return $this->imageDir;
  }

  /**
   * getScriptDir	- grab the application Script Directory
   * @return a string
   */
  public function getScriptDir() {
    return $this->scriptDir;
  }

  /**
   * getFlashDir	- grab the application Flash Directory
   * @return a string
   */
  public function getFlashDir() {
    return $this->flashDir;
  }


  /**
   * ***************** Modifiers ******************
   */

  /**
   * setConfigInfo	- Configuration information for this page/app
   * @param ($aInfo)
   */
  public function setConfigInfo($aInfo) {
    $this->aInfo	= $aInfo;
  }

  /**
   * setBaseDir	- set the application base directory
   * @param ($baseDir)
   */
  public function setBaseDir($baseDir) {
    $this->baseDir	= $baseDir;
  }

  /**
   * setBaseURL	- set the application base directory
   * @param ($baseURL)
   */
  public function setBaseURL($baseURL) {
    $this->baseURL	= $baseURL;
  }

  /**
   * setCSSDir	- set the application CSS directory
   * @param ($cssDir)
   */
  public function setCSSDir($cssDir) {
    $this->cssDir	= $cssDir;
  }

  /**
   * setImageDir	- grab the application Image Directory
   * @param ($imageDir)
   */
  public function setImageDir($imageDir) {
    $this->imageDir	= $imageDir;
  }

  /**
   * setScriptDir	- grab the application Script Directory
   * @param ($scriptDir)
   */
  public function setScriptDir($scriptDir) {
    $this->scriptDir	= $scriptDir;
  }

  /**
   * setFlashDir	- grab the application Flash Directory
   * @param ($flashDir)
   */
  public function setFlashDir($flashDir) {
    $this->flashDir	= $flashDir;
  }

  /**
   * ****************** Redirection methods *********************
   */

  /**
   * redirect	- redirects the page to the URL
   * @parameter ($url)
   */
  public function redirect($url) {
    header("Location: $url");
    exit;
  }

  /**
   * HTTP_Post - outputs a post to a new page; used to send a post to
   * remote page without any pear extensions installed...using curl
   * This was partially ripped out of the php.net discussion under curl, then re-written to use curl
   * @parameter ($URL) - the remote page to get to
   * @parameter ($data) - the data to send to the page (in the form of a hash)
   * @parameter ($referer) - 
   */
  public function HTTP_Post($URL, $data, $referrer = "") {    
    // use curl
    $ch = curl_init($URL);
    
    // Building referrer
    if($referrer == "") { // if not given use this script as referrer
      $referrer = $_SERVER["SCRIPT_URI"];
    }

    // making string from $data
    $values	= array();
    foreach($data as $key=>$value) {
      array_push($values, "$key=" . urlencode($value));
    }

//     // debugging
//     print __CLASS__ . ": " . __FUNCTION__ . ": <pre>" . print_r($values, true) . "</pre>\n";
//     exit;

    $data_string = implode("&",$values);

    // building POST-request:
    curl_setopt($ch, CURLOPT_REFERER, $referrer);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    $result = curl_exec($ch);

    return $result;
  }

}

?>