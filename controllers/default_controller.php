<?php
/**
 * default_controller.php
 * @author Asif Chowdhury
 * date: 2011-05-27
 * used to be the default page handler for an application
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
class Default_Controller extends Controller {
    public function index() {
	$this->view('default_view');
    }
}

?>
