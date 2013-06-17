<?php
/**
 * @author: Asif Chowdhury
 * date: 2012-05-08
 * default_model.php
 * default model - a default model
 */
class Default_Model extends Model {
    /**
     * @access public
     * constructor
     * @param $oDB - database object
     */
    public function __construct($oDB) {
		parent::__construct($oDB);
		$this->sBaseTable   = 'article';
    }
}

?>
