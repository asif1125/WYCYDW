<?php
  /**
   * Repeater.php
   * Written by Asif Chowdhury
   * 2009-12-11
   * used to be a set of classes to allow a developer to create parts of pages.
   *
   * 2011-05-27
   * usurped by the WYCYDW framework (ACE)
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


/**
 * Repeater Class	- used to render lists of data as HTML
 */ 
class Repeater {
  protected $aData;
  protected $aHeaderData;
  protected $aFooterData;

  private $oHeader;
  private $sItem;
  private $sAlternateItem;
  private $oFooter;
  private $oEmpty;

  /**
   * constructor	- returns a new repeater object
   * @parameter ($aData)	- optional value for what to display in the repeater
   * @parameter ($oHeader)	- optional value for the default header template object for this repeater
   * @parameter ($oItem)	- optional value for the default item template object for this repater
   * @parameter ($oAlternateItem)	- optional value for the detault alternate item template object for this repeater
   * @parameter ($oFooter)	- optional value for the default footer template object for this repeater
   * @parameter ($oEmpty)	- optional value for the default empty template object for this repeater
   */
  public function __construct($aData = array(), $oHeader = null, $oItem = null, $oAlternateItem = null, $oFooter = null, $oEmpty = null) {
    $this->aData	= $aData;
    $this->oHeader	= $oHeader;
    $this->oItem	= $oItem;
    $this->oAlternateItem	= $oAlternateItem;
    $this->oFooter	= $oFooter;
    $this->oEmpty	= $oEmpty;
    
  }

  /**
   * setHeader	- sets the Header object for the Repeater as well as setting the necessary header data to display
   * @parameter($sHeader)	- string that contains the header template file name
   * @parameter($aHeader)	- optional value for the data to place in the Header template.
   */
  public function setHeader($sHeader, $aHeader = array()) {
    $this->aHeaderData	= $aHeader;
    $this->oHeader	= new Template($sHeader);
  }
  
  /**
   * setHeaderData	- sets the data for a Repeater Header
   * @parameter($aData)	- an array containing the data.
   */
  public function setHeaderData($aHeader) {
    $this->aHeaderData	= $aHeader;
  }

  /**
   * setItem	- sets the Item row object for this Repeater
   * @parameter($sItem)	- the string contiaining the item template file name
   */
  public function setItem($sItem) {
    $this->sItem	= $sItem;
  }
  
  /**
   * setAlternateItem	- sets the Alternate Item row object for this Repeater
   * @parameter($sItem)	- the string contiaining the item template file name
   */
  public function setAlternateItem($sItem) {
    $this->sAlternateItem	= $sItem;
  }

  /**
   * setFooter	- sets the Footer object for the Repeater as well as setting the necessary footer data to display
   * @parameter($sFooter)	- string that contains the footer template file name
   * @parameter($aFooter)	- optional value for the data to place in the Footer template.
   */
  public function setFooter($sFooter, $aFooter = array()) {
    $this->aFooterData	= $aFooter;
    $this->oFooter	= new Template($sFooter);
  }
  
  /**
   * setFooterData	- sets the data for a Repeater Footer
   * @parameter($aData)	- an array containing the data.
   */
  public function setFooterData($aFooter) {
    $this->aFooterData	= $aFooter;
  }

  /**
   * setEmpty	- sets the Empty object for the Repeater as well as setting the necessary footer data to display
   * @parameter($sEmpty)	- string that contains the footer template file name
   * @parameter($aEmpty)	- optional value for the data to place in the Empty template.
   */
  public function setEmpty($sEmpty) {
    $this->aEmptyData	= $aEmpty;
  }
  
  /**
   * renderRepeater - renders the repeater including all data for the repeater
   * @return a string
   */
  public function renderRepeater() {
    $sReturn	= "";
    
    // header portion
    if(!is_null($this->oHeader)) {
      $this->oHeader->setData($this->aHeaderData);
      $sReturn	.= $this->oHeader->render();
    }

    // display the items
    $counter = 0;
    foreach($this->aData as $aData) {
      $oItem	= new Template((($counter % 2) == 1 and !is_null($this->sAlternateItem)) ? $this->sAlternateItem : $this->sItem);
      $oItem->setData($aData);
      $sReturn	 .= $oItem->render();
      $counter++;
    }

    // empty portion
    if($counter <= 0) {
      $sReturn	.= $this->oEmpty->render();
    }

    // footer portion
    if(!is_null($this->oFooter)) {
      $this->oFooter->setData($this->aFooterData);
      $sReturn	.= $this->oFooter->render();
    }

    return $sReturn;
  }
  
  /**
   * setData	- sets the dats for the repeater
   * @parameter($aData)	- sets the data for this repeater
   */
  public function setData($aData) {
    $this->aData	= $aData;
  }
  
  /**
   * getData	- returns the data for this repeater
   */
  public function getData() {
    return $this->aData;
  }
}


?>