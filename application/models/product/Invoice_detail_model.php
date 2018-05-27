<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/models/Starter_model.php';

class Invoice_Detail_Model extends Starter_Model{
    //put your code here
    private $permission_del = false; 
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->_tb_name="rz_product_invoice_detail";
    }

	function get_invoice($id){
        $select="*";
        $where = array('invoice_id'=>$id);
        $data = $this->get_data($select,$where,1);      
        if($data){
            return $data[0];
        }
        return 0;
    }
    
}
