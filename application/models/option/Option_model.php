<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/models/Starter_model.php';

class Option_Model extends Starter_Model{
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->_tb_name="rz_option";
    }
    
}
