<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/models/Starter_model.php';

class Type_Model extends Starter_Model{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->_tb_name="rz_type";
    }
    
    public function get_type($type){
    	$select="*";
	    $where = array('type'=>$type);
	    $data = $this->get_data($select,$where);
	    if($data){
	      return $data;
	    }else{
	      return FALSE;  
	    }
    }
    public function get_id_type($value){
        $select="*";
        $where = array('value'=>$value);
        $data = $this->get_data($select,$where,1);
        if($data){
          return $data[0]['id'];
        }else{
          return FALSE;  
        }
    }
    
}
