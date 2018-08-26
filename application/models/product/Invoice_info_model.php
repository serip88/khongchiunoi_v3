<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/models/Starter_model.php';

class Invoice_Info_Model extends Starter_Model{
    //put your code here
    private $permission_del = false; 
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->_tb_name="rz_product_invoice_info";
    }

}
