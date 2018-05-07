<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/models/Starter_model.php';

class Product_Model extends Starter_Model{
    //put your code here
    private $permission_del = false; 
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->_tb_name="rz_product";
    }

    function get_product($id){
	    $select="product_id,name,description,image_name,image_path,price,enabled as status,parent_id";
	    $where = array('product_id'=>$id);
	    $data = $this->get_data($select,$where,1);      
	    if($data){
	        return $data[0];
	    }
	    return 0;
	}

	function check_delete($uid,$pid){
		
	}
    
}
