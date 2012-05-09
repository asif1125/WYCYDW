<?php
  /**
   * Template.php
   * written by Asif Chowdhury
   * 2008-05-28
   * a new, slightly modified version of the Original Template class I wrote back in 2004
   *
   * modified 2009-12-13
   * added a method to use a different templating structure while still supporting the old
   *
   * 2011-05-27
   * usurped by the WYCYDW framework (ACE)
   * @todo remove the excess bulkware in this file
   * 
   * WYCYDW TODO list
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
class Template {
  protected $aData;

  private $templateFile;
  private $tags;

  /**
   * constructor - instantiates a template object
   * @parameter ($file) - the template file to read in
   */
  public function __construct($file) {
    $this->templateFile = $file;
    $this->tags = array();
    $this->aData    = array();
  }
  
  /**
   * setVar - sets a key, value combination for the template
   * @parameter ($name) - the key (tag) to replace in the template
   * @parameter ($value) - the string to replace the tag with
   */
  public function setVar($name, $value) {
    $this->tags[$name] = $value;
  }

  /**
   * parse - parse the template and replace tags
   * @return a string
   */
  public function parse() {
    $string = file_get_contents($this->templateFile);
    $html = $string;
    foreach ($this->tags as $tag => $value) {
      $html = str_replace("[" . $tag . "]", $value, $html);
    }

    return $html;
  }

  /**
   * render	- an alternate way to print out a template where the templates are php rather than the templating syntax
   * @return a string
   */
  public function render() {
    ob_start();
    extract($this->aData, EXTR_PREFIX_SAME, 'col_');
    include($this->templateFile);
    return ob_get_clean();
  }
  
  /**
   * setData	- sets any data for this template
   * @parmeter($aData)	- associative array for the data
   */
  public function setData($aData) {
    $this->aData	= $aData;
  }
}
?>