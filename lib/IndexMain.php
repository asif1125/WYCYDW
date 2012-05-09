<?php
/**
 * WYCYDW IndexMain.php
 * Written by Asif Chowdhury
 * 2011-05-26
 * Used to be the base controller for a page by loading up the appropriate subController
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
include('./lib/ConfigReader.php');
include('./lib/DBClass.php');
include('./lib/Model.php');
include('./lib/Template.php');
include('./lib/Page.php');
include('./lib/Controller.php');
class Index {
    /**
     * constructor - starts the whole process of finding out what we've called and loading up the appropriate page.
     */
    public function __construct() {
	// we always start a session first
	session_start();
	
	// open up the configuration
	$slash	    = DIRECTORY_SEPARATOR;
	$oCR	= new ConfigReader('./config/wycydw.conf');
	$aConf	= $oCR->readConfig();
	$baseDir	= str_replace('/', $slash, $aConf['INSTALLDIR']) . $slash;
	$baseURL	= $aConf['baseURL'];

	// find out what's being called.
	$sHttp	    = ((array_key_exists('HTTPS', $_SERVER) and $_SERVER['HTTPS'] == 'https') ? 'https://' : 'http://');
	$sServer    = $_SERVER['SERVER_NAME'];
	$sRequest   = $_SERVER['REQUEST_URI'];
	$sQuery	    = $_SERVER['QUERY_STRING'];

	// load up any libraries called for in the configuration
	if(array_key_exists('load_library_path', $aConf) and !empty($aConf['load_library_path'])) {
	    $this->loadLibraries($baseDir . $aConf['load_library_path']);
	}

	// look at the request URI and grab the appropriate controller
	$sCallingURL	= $sHttp . $sServer . $sRequest;
	$sCallingURL	= str_replace($baseURL, '', $sCallingURL);
	$sCallingURL	= preg_replace('%^/%', '', $sCallingURL);
	$aPieces    = (!empty($sCallingURL))? explode('/', $sCallingURL) : array();

	// go through each level and find the controller with the remaining pieces as parameters
	$bFoundController   = false;
	$sPath	= '';
	$aParameters = array();
	$sClass	= '';
	$sControllerPath    = $aConf['load_controller_path'] . $slash;

	foreach($aPieces as $sPiece) {
	    if(!$bFoundController) {
		if(file_exists($baseDir . $sControllerPath . $sPath . $sPiece) and is_dir($sControllerPath. $sPath . $sPiece)) {
		    $sPath .= $sPiece . $slash;
		}
		elseif(file_exists($sControllerPath . $sPath . $sPiece . '.php') and is_file($sControllerPath . $sPath . $sPiece . '.php')) {
		    $sPath .= $sPiece;
		    $sClass = ucWords($sPiece);
		    $bFoundController	= true;
		}
		// we really should never get here.
		else
		    die("Controller: " . $sControllerPath . $sPath . $sPiece . ".php not found!");
	    }
	    else
		array_push($aParameters, $sPiece);
	}
	$sPath	= ((empty($sPath)) ? $sControllerPath . 'default_controller.php' : $sControllerPath . $sPath . '.php');
	$sClass	= ((empty($sClass)) ? 'Default_Controller' : $sClass);

	// include the controller file and instantiate the specified controller
	include($baseDir . $sPath);
	$oController	= null;
	eval("\$oController = new $sClass" . "(\$aConf);");

	// now see if the first parameter(if it exists) is actually a method...if it is, then launch it with the rest of the parameters as argumrents
	if(is_array($aParameters) and !empty($aParameters) and method_exists($oController, $aParameters[0])) {
	    $sMethod	= array_shift($aParameters);
	    $sParameters    = implode($aParameters, ',');
	    eval("\$oController" . "->$sMethod" . "($sParameters);");
	}
	// the first parameter does not exist, load the index with the parameters as arguments
	elseif(is_array($aParameters) and !empty($aParameters)) {
	    $sParameters    = implode($aParameters, '" ,"');
	    eval("\$oController" . "->index(\"$sParameters\");");
	}
	// no parameters were given just launch the index.
	else
	    $oController->index();
    }

    /**
     * @access private
     * loadLibraries - recursively look for library files that will be included in the application.
     * @param $sDir - the full path to look for libraries and subdirectories.
     */
    private function loadLibraries($sDir) {
	$rsLibDir   = opendir($sDir);
	$slash	= DIRECTORY_SEPARATOR;
	while($sFile = readdir($rsLibDir)) {
	    if(!preg_match('%^\.+$%', $sFile) and preg_match('%\.php$%', $sFile) and is_file($sDir . $slash . $sFile)) {
		include_once($sDir . $slash . $sFile);
	    }
	    elseif(!preg_match('%^\.+$%', $sFile) and is_dir($sDir . $slash . $sFile)) {
		$this->loadLibraries($sDir . $slash . $sFile);
	    }
	}
	closeDir($rsLibDir);
    }
}
?>
