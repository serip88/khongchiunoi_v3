<?php
/*
  +------------------------------------------------------------------------+
  | Copyright (C) 2016 Toigiaitri.                                         |
  |                                                                        |
  | This program is free software; you can redistribute it and/or          |
  | modify it under the terms of the Toigiaitri  License                   |
  |                                                                        |
  +------------------------------------------------------------------------+
  | o Developer : Rain                                                     |
  | o HomePage  : http://www.toigiaitri.net/                               |
  | o Email     : serip88@gmail.com                                        |
  +------------------------------------------------------------------------+
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_lib extends Common_lib {
  protected $CI = null;
  function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->database('default');
    $this->CI->load->model(array(
        'product/product_model',
    ));
      
  }
  function validate_save_product($param){
      $requite = array('name','parent_id');//description,status,parent_id
      $param['name']   = isset($param['name']) && $param['name'] ?$param['name']: '';
      $param['name']   = str_replace('/', '-', $param['name']);
      $param['price']     = isset($param['price']) && $param['price'] ? str_replace('.','',$param['price']): 0;
      $param['price']     = intval(str_replace(',','',$param['price'])) ;
      $param['status']    = isset($param['status']) && $param['status'] ? $param['status']: 0;   
      $param['parent_id']   = isset($param['parent_id']) && $param['parent_id'] ? $param['parent_id']: 0;   
      $param['description'] = isset($param['description']) && $param['description'] ? $param['description']: '';   
      foreach ($requite as $key => $value) {
        if(!$param[$value]){
          return 0;
        }
      }
      return $param;
  }

  function handle_save_product($param){
    //path_parent_id
    $this->CI->load->library(array(config_item('app_path').'/'.'category_lib'));
    $param['path_category_id']=0;
    $param['level']=0;
    if($param['parent_id']){
      $category = $this->CI->category_lib->get_category($param['parent_id']);
      if($category['path_parent']){
        $param['path_category_id'] = $category['path_parent'].','.$param['parent_id'];
      }else{
        $param['path_category_id'] = $param['parent_id'];
      }
    }
    return $param;
  }
  function save_product($param){ 
    $data = array();
    $data['name']  = $param['name'];
    $data['price']  = $param['price'];
    $data['slug']     = $param['name'];
    $data['parent_id']  = $param['parent_id'];
    $data['description'] = $param['description'];
    $data['path_category_id'] = $param['path_category_id'];
    $data['level']    = 0;
    $data['orders']    = 0;
    $data['posted_date'] = time();
    $data['enabled']  = $param['status']; 
    $id = $this->CI->product_model->insert_data($data);
    return $id;
  }
  function get_product_list(){
      $select="product_id,name,description,image_name,image_path,price,enabled as status,parent_id";
      $where = array();
      $data = $this->CI->product_model->get_data($select,$where);      
      if($data){
        $data = $this->format_product_list($data);
      }
      return $data;
  }
  function validate_edit_product($param){
      $param = $this->validate_save_product($param);
      if($param){
        $param['product_id']  = isset($param['product_id']) && $param['product_id'] ?$param['product_id']: 0;
        if(!$param['product_id'])
          return 0;
      }
      return $param;
  }
  function edit_product($param){ 
      $data = array();
      $data['name']  = $param['name'];
      $data['price']  = $param['price'];
      $data['slug']     = $param['name'];
      $data['parent_id']  = $param['parent_id'];
      $data['description'] = $param['description'];
      $data['path_category_id'] = $param['path_category_id'];
      $data['level']    = 0;
      $data['orders']    = 0;
      $data['posted_date'] = time();
      $data['enabled']  = $param['status']; 
      if(isset($param['file_name_mb']) && $param['file_name_mb'] && isset($param['file_path_mb']) && $param['file_path_mb'] ){
        $data['image_name']  = $param['file_name_mb']; 
        $data['image_path'] = $param['file_path_mb']; 
      }

      if(isset($param['product_id']) && $param['product_id']){
        $where = array("product_id"=> $param['product_id']);
        $stt = $this->CI->product_model->update_data($data,$where); 
        return $stt;
      }else{
        return FALSE;
      }
  }
  function product_delete($product_id){
      if($product_id){
        $where = array("product_id"=>$product_id);
        $stt = $this->CI->product_model->delete_data($where);
        return $stt;
      }else
        return false;
  }
  //SUPPORT FUNCTION
  function format_product_list($data){
    foreach ($data as $key => $value) {
      if($value['price']){
        $data[$key]['price'] = number_format($value['price'] , 0, ',', '.');
      }
      if($value['image_path']){
        $data[$key]['image_path'] = trim($value['image_path'],".");
      }
    }
    return $data;
  }
  //$file = array('name'=>'','path'=>'');
  function check_file_exit($file){
    $requite = array('name','path');
    foreach ($requite as $key => $value) {
      if(! isset($file[$value])){
        return 0;
      }
    }
    if(file_exists(FCPATH.$file['path'])){
      return 1;
    }else{
      return 0;
    }
  }
  

}