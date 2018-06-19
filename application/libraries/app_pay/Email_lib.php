<?php
/*
  +------------------------------------------------------------------------+
  | Copyright (C) 2016 Toigiaitri.                                        |
  |                                                                        |
  | This program is free software; you can redistribute it and/or          |
  | modify it under the terms of the Toigiaitri  License                      |
  |                                                                        |
  +------------------------------------------------------------------------+
  | o Developer : Rain                                                     |
  | o HomePage  : http://www.toigiaitri.net/                               |
  | o Email     : serip88@gmail.com                                        |
  +------------------------------------------------------------------------+
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_lib extends Common_lib {
  	protected $CI = null ;
  	protected $_lang ;
	protected $_tag_type = 'email' ;
  	function __construct() {
      	$this->CI =& get_instance();
      	$this->CI->load->database('default');
      	$this->CI->load->model(array(
          	'statistic/email_model',
        ));
      	$this->_config =  $this->CI->config;
  	}
  	function validate_save($param){
	    $requite = array('email','password');//description,description_en,status,parent_id
	    $param['email'] 	= isset($param['email']) && $param['email'] ?$param['email']: '';
	    $param['description'] = isset($param['description']) && $param['description'] ? $param['description']: '';   
	    $param['status'] 	= isset($param['status']) && $param['status'] ?$param['status']: 1;
	    foreach ($requite as $key => $value) {
	      if(!$param[$value]){
	        return 0;
	      }
	    }
	    return $param;
	}

	function save($param){ 
	    $data = array();
	    $required = array('email');
	    $data['salt'] = $this->generateRandomString();
	    $data['email'] 	= $param['email'];
	    $data['password'] = md5($data['salt'].$param['password']);
	    $data['status'] 	= $param['status']; 
	    $id = $this->CI->email_model->insert_data($data);
	    return $id;
	}

	function validate_edit_category($param){
	    $param = $this->validate_save_category($param);
	    if($param){
	    	$param['id'] 	= isset($param['id']) && $param['id'] ?$param['id']: 0;
	    	if(!$param['id'])
	    		return 0;
	    }
	    return $param;
	}
	function handle_save_category($param){
		//path_parent,level,path_parent_name
		$param['path_parent']=0;
		$param['path_parent_name']=$param['name'];
		$param['level']=0;
		if($param['parent_id']){
			$category = $this->get_category($param['parent_id']);
			if($category['path_parent'] && $category['level']){
				$param['path_parent'] = $category['path_parent'].','.$category['id'];
				$param['path_parent_name'] = $category['path_parent_name'].'/'.$param['name'];
				$param['level'] = $category['level']+1;
			}else{
				$param['path_parent'] = $category['id'];
				$param['path_parent_name'] = $category['name'].'/'.$param['name'];
				$param['level']=1;
			}
		}
		return $param;
	}
	function edit_category($param){ 
	    $data = array();
	    $data['name'] 	= $param['name'];
	    $data['slug'] 		= $param['name'];
	    $data['parent_id'] 	= $param['parent_id'];
	    $data['description'] = $param['description'];
	    $data['path_parent'] = $param['path_parent'];
	    $data['path_parent_name'] = $param['path_parent_name'];
	    $data['type'] 		= $this->_tag_type;
	    $data['level'] 		= $param['level'];
	    $data['orders'] 		= 0;
	    $data['enabled'] 	= $param['status']; 
	    if(isset($param['id']) && $param['id']){
	      $where = array("id"=> $param['id']);
	      $stt = $this->CI->email_model->update_data($data,$where); 
	      return $stt;
	    }else{
	      return FALSE;
	    }
	}
	function get_category($id){
	    $select="id,name,description,enabled as status,path_parent,level,path_parent_name";
	    $where = array('id'=>$id, 'type'=>$this->_tag_type);
	    $data = $this->CI->email_model->get_data($select,$where,1);
	    if($data){
	    	return $data[0];
	    }else{
	    	return 0;
	    }
	}
	function get_list(){
	    $select="*";
	    $where = array();
	    $data = $this->CI->email_model->get_data($select,$where);
	    return $data;
	}
	function delete($id){
	    if($id){
	      	$where = array("id"=>$id);
	      	$stt = $this->CI->email_model->delete_data($where);
	      	return $stt;
	    }else
	      	return false;
	}
	function category_check_have_child($category_id){
		if($category_id){
	    	//$where = array('path_parent'=>$category_id,'type'=>$this->_tag_type);
	    	$where = "FIND_IN_SET($category_id,path_parent) limit 1";
	    	$total = $this->CI->email_model->get_total($where);	
	      	return $total;
	    }
	    return 0;
	}
	function category_check_have_product($category_id){
		if($category_id){
			$select = array('table_join'=>array());
			$select['table_join'] = array();
			$select['table_join'][] = array('table_name'=>'rz_product as B','condition'=>"A.id =B.parent_id", 'type'=>'left');
			$where = array('where'=> array());
			$where['where'] = array('B.parent_id'=>$category_id);
			$limit = array('type'=>'int','limit'=>1);
			$have_product = $this->CI->email_model->get_dt($select,$where,$limit);	
			return $have_product;
		}
		return 0;
	}

	//SUPPORT FUNCTION
	//$data :is array
	/*function format_path_parent($data){
		foreach ($data as $key => $value) {
			$data[$key]['parent_id']=0;
			if($value['path_parent']){
				$path_parent = explode(',', $value['path_parent']);
				$data[$key]['parent_id'] = end($path_parent);
			}
		}
		return $data;
	}*/
}