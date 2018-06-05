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

class Post_lib extends Common_lib {
  	protected $CI = null ;
  	protected $_lang ;
	protected $_tag_type = 'post' ;
  	function __construct() {
      	$this->CI =& get_instance();
      	$this->CI->load->database('default');
      	$this->CI->load->model(array(
          	'post/post_model',
        ));
      	$this->_config =  $this->CI->config;
  	}
  	function validate_save($param){
	    $requite = array('title');//description,description_en,status,parent_id
	    $param['title'] 	= $this->validate_input_text($param,'title');
	    foreach ($requite as $key => $value) {
	      if(!$param[$value]){
	        return 0;
	      }
	    }
	    return $param;
	}
	function handle_save($param){
		$param['slug'] = $param['title'];
		$param['description'] = $param['description'];
		$param['tag'] = $param['tag'];
		$param['posted_date'] = time();
		$param['enabled'] = 1;
		$param['parent_id'] = 0;
		$param['level'] = 0;
		$param['post_type'] = $this->_tag_type;
		return $param;
	}
	function save($param){
		$expected = array('title','slug','parent_id','description','tag','posted_date','enabled','post_type','order','post_author');
	    $data = array();
	    foreach ($expected as $key => $value) {
	    	if(isset($param[$value])){
	    		$data[$value] 		= $param[$value];
	    	}
	    }
	    $id = $this->CI->post_model->insert_data($data);
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
	      $stt = $this->CI->tag_model->update_data($data,$where); 
	      return $stt;
	    }else{
	      return FALSE;
	    }
	}
	function get_category($id){
	    $select="id,name,description,enabled as status,path_parent,level,path_parent_name";
	    $where = array('id'=>$id, 'type'=>$this->_tag_type);
	    $data = $this->CI->tag_model->get_data($select,$where,1);
	    if($data){
	    	return $data[0];
	    }else{
	    	return 0;
	    }
	}
	function get_category_list(){
	    $select="id,parent_id,name,description,enabled as status,path_parent,level,path_parent_name";
	    $where = array('type'=>$this->_tag_type);
	    $data = $this->CI->tag_model->get_data($select,$where);
	    /*if($data){
	    	$data = $this->format_path_parent($data);
	    }*/
	    return $data;
	}
	function categorys_delete($category_id){
	    if($category_id){
	      $where = array("id"=>$category_id,'type'=>$this->_tag_type);
	      $stt = $this->CI->tag_model->delete_data($where);
	      return $stt;
	    }else
	      return false;
	}
	function category_check_have_child($category_id){
		if($category_id){
	    	//$where = array('path_parent'=>$category_id,'type'=>$this->_tag_type);
	    	$where = "FIND_IN_SET($category_id,path_parent) limit 1";
	    	$total = $this->CI->tag_model->get_total($where);	
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
			$have_product = $this->CI->tag_model->get_dt($select,$where,$limit);	
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