<?php
  /**
   * ConfigReader.php
   * written by Asif Chowdhury
   * 2007-06-01
   * used to read configuration files
   *
   * 2011-05-27
   * usurped by the WYCYDW franework (ACE)
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
class ConfigReader {
  private $configFile;

  /**
   * constructor - outputs nothing; used to set a configuration file and instantiate a config reader object
   * @parameter ($configFile) - the configuration file to read
   * @return an instantiated ConfigReader object
   */
  public function __construct($configFile) {
    $this->configFile = $configFile;

    if (! is_file($configFile)) {
      die ("$configFile does not exist\n");
    }
  }

  /**
   * readConfig - outputs nothing; used to read and parse a config file;
   * @return - a hash
   */
  public function readConfig() {
    // open the config file for the database configuration
    $fileInfo = file($this->configFile);
    
    // go through each of the values and set them in an array
    // the file is in simple format 'key = value'
    // comments are lines that begin with '#'
    // blank lines are permitted
    $info = array();
    foreach ($fileInfo as $line) {
      // make sure that we're not
      if (preg_match("%^#%", $line)) {
	next($fileInfo);
      }
      elseif (preg_match("%^\s*$%", $line)) {
	next($fileInfo);
      }
      else {
	// get the key value pair, but limit on one split
	list($key, $value) = preg_split("%\s*=\s*%", $line, 2);
	$info[$key] = preg_replace("%^\s+%", "", preg_replace("%\s+$%", "", $value));
      }
    }

    return $info;
  }
}
?>