<?php
/**
 * Controller.php
 * @author Asif Chowdhury
 * date: 2011-05-27
 * Used to be the base Controller for an application
 * @TODO 
 *  - Add ability to filter input from User and web pages...I believe Rasmus Lerdof suggested something like the PECL filter package
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
class Controller {
    protected $oPage;
    protected $oLayout;
    protected $oDB;
    protected $aModels;
    protected $oView;

    /**
     * constructor
     * @param $aConf - base configuration array
     */
    public function __construct($aConf) {
	$this->oPage	= new Page($aConf);
	$this->oLayout	= $this->createLayout('default_layout');
	$this->aModels	= array();
    }

    /** ------ Accessors ------ **/

    /**
     * @access public
     * getPage	- gets the page object for this controller
     * @return Page Object reference
     */
    public function getPage() {
	return $this->oPage;
    }

    /**
     * @access public
     * getLayout    - gets the layout object for this particular method
     * @return  Layout Object reference
     */
    public function getLayout() {
	return $this->oLayout;
    }

    /**
     * @access public
     * getModels    - get the list of Models associated with this controller
     * @return	array
     */
    public function getModels() {
	return $this->aModels;
    }

    /** ------ Modifiers ----- **/

    /**
     * @access public
     * setPage	- sets a new page object for this Controller (dunno why you'd want to.)
     * @param $oPage - a page object to replace what we have.
     */
    public function setPage($oPage) {
	$this->oPage =  $oPage;
    }

    /**
     * @access protected
     * createLayout	- sets the layout to the new layout defined in the views/layouts directory and specified by the paramter
     * @param $sLayout - string specifying the name of the layout to use corresponding directly with the filename in the layouts directory
     * @return Template Object reference
     */
    protected function createLayout($sLayout) {
	$aInfo	= $this->oPage->getConfigInfo();
	$oReturn    = null;

	// a layout is a special view located in the views/layouts directory containing only one value ($content)
	// we basically use the template class to create the view and use the requested file
	$slash	= DIRECTORY_SEPARATOR;

	$sLayoutsDir	= $aInfo['baseDir'] . $slash . $aInfo['load_view_path'] . $slash . $aInfo['load_layout_path'] . $slash;

	if(file_exists($sLayoutsDir . $sLayout . '.php') and is_file($sLayoutsDir . $sLayout . '.php'))
	    $oReturn	= new Template($sLayoutsDir . $sLayout . '.php');
	return $oReturn;
    }

    /**
     * @access public
     * layout	- sets the layout for this instantiation of the controller.
     * @param $sLayout	- the name of the layout
     */
    public function layout($sLayout) {
	$this->oLayout	= $this->createLayout($sLayout);
    }

    /**
     * @access public
     * model	- adds a model to the models list
     * @param $sModel	- the name of the model to add to the models list and return
     * @return Model Object reference
     */
    public function model($sModel) {
	$aInfo	= $this->oPage->getConfigInfo();
	$oReturn    = null;

	// check to see if we have a database connection or not, if not, then create one.
	if(!isset($this->oDB))
	    $this->oDB	= $this->connectDB($aInfo);

	// find the model we're talking about and instantiate it.
	$slash	= DIRECTORY_SEPARATOR;
	$sModelPath = $aInfo['baseDir'] . $slash . $aInfo['load_model_path'] . $slash;
	if(file_exists($sModelPath . $sModel . '.php') and is_file($sModelPath . $sModel . '.php')) {
	    include($sModelPath . $sModel . '.php');
	    $aClassPath	= preg_split('%[/\\\]%', $sModel);
	    $nLastIndex	= count($aClassPath) - 1;
	    $sClass = $aClassPath[$nLastIndex];
	    eval("\$oReturn	= new " . ucWords(strToLower($sClass)) . "(\$this->oDB);");
	}

	// add it to the models list and return the model
	if(isset($oReturn))
	    array_push($this->aModels, $oReturn);
	return $oReturn;
    }

    /**
     * @access protected
     * connectDB    - make a connection to the database
     * @return DBClass Object reference
     */
    protected function connectDB() {
	return new DBClass($this->oPage->getConfigInfo());
    }

    /**
     * @access public
     * view - loads a view and echos it out to the screen
     * @param $sView	- the name of the view we want to load
     * @param $aData	- optional associative data array we want to pass into the view
     * @param $bReturn	- optional boolean as to whether we want to return the output rather that echo it (default: false)
     * @param $bInfo	- optional associative array to hold special values to send to the layout
     * @return string
     */
    public function view($sView, $aData = array(), $bReturn = false, $aLayoutInfo = array()) {
	$aInfo	= $this->oPage->getConfigInfo();
	$sReturn    = '';

	// find the view we want and create the template
	$slash	= DIRECTORY_SEPARATOR;
	$sViewPath  = $aInfo['baseDir'] . $slash . $aInfo['load_view_path'] . $slash;
	if(file_exists($sViewPath . $sView . '.php') and is_file($sViewPath . $sView . '.php'))
	    $this->oView    = new Template($sViewPath . $sView . '.php');

	// now set the data and render it, and add it to the layout
	if(isset($this->oView)) {
	    $this->oView->setData($aData);
	    $sViewContent  = $this->oView->render();
            $aLayoutInfo['content'] = $sViewContent;
	    $this->oLayout->setData($aLayoutInfo);
	}

	// render the whole page and save it to the return value
	$sReturn    = $this->oLayout->render();

	// if we want to return it then return it, otherwise echo it out
	if($bReturn)
	    return $sReturn;
	else
	    echo $sReturn;
    }
    
    /**
     * library	- load a library and return a library object
     * @author Asif Chowdhury
     * @date 2013-09-28
     * 
     * @param $libary
     * @return lib object
     */
    public function library($library)
    {
	    $aInfo		= $this->getPage()->getConfigInfo();
	    $slash		= DIRECTORY_SEPARATOR;
	    $library_path	= $aInfo['baseDir'] . $slash . $aInfo['load_other_library_path'] . $slash;
	    $lib		= null;
	    if(is_file($library_path . $library . ".php"))
	    {
		    include($library_path . $library . ".php");
		    $lib	= new $library();
	    }
	    return $lib;
    }    
}
?>
