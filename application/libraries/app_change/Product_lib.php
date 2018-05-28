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
        'product/product_invoice_model',
        'product/invoice_detail_model'
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
        $data[$key]['price_pure'] = $value['price'];
      }
      if($value['image_path']){
        $data[$key]['image_path'] = trim($value['image_path'],".");
      }
      $data[$key]['quantity'] = 0;
      $data[$key]['last_price'] = 0;
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
  
  function validate_save_product_invoice($param){ 
    $amount = 0;
    if($param){
      foreach ($param as $key => $value) {
        $param[$key]['quantity'] = str_replace(",", ".", $value['quantity']);
        if($param[$key]['quantity'] <= 0){
          return 0;
        }else{
          $amount += $value['quantity'] * $value['price_pure'];
        }
      }
    }else{
      return 0;
    }
    return $amount;
  }
  function save_product_invoice($amount){ 
    $data = array();
    $data['created_date'] = time();
    $data['amount'] = $amount;
    $data['memo']  = ''; 
    $id = $this->CI->product_invoice_model->insert_data($data);
    return $id;
  }
  function validate_save_invoice_detail($param){
      $requite = array('invoice_id','product_id','price','quantity','last_price');//description,status,parent_id
      $param['invoice_id']   = isset($param['invoice_id']) && $param['invoice_id'] ? $param['invoice_id']: 0;
      $param['product_id']   = isset($param['product_id']) && $param['product_id'] ? $param['product_id']: 0;
      $param['price']   = isset($param['price_pure']) && $param['price_pure'] ? $param['price_pure']: 0;
      $param['quantity']   = isset($param['quantity']) && $param['quantity'] ? $param['quantity']: 0;
      $param['last_price']   = isset($param['last_price']) && $param['last_price'] ? $param['last_price']: 0;
      $param['product_name']   = isset($param['name']) && $param['name'] ? $param['name']: '';
      foreach ($requite as $key => $value) {
        if(!$param[$value]){
          return 0;
        }
      }
      return $param;
  }
  function save_invoice_detail($param){ 
    $data = array();
    $expected = array('invoice_id','product_id','product_name','price','quantity','last_price');
    foreach ($expected as $key => $value) {
      if( isset($param[$value]) ){
        $data[$value] = $param[$value];
      }
    }
    $id = $this->CI->invoice_detail_model->insert_data($data);
    return $id;
  }
  function get_invoice_list($params){
      $select="*";
      $where = array();
      if(isset($params['start_date']) && isset($params['end_date'])){
        $params['start_date'] = $params['start_date']/1000;
        $params['end_date'] = $params['end_date']/1000;
        $end_date = date("Y-m-d", $params['end_date']);
        $params['end_date'] = strtotime($end_date.' 23:59:59');
        $where['created_date >='] = $params['start_date'];
        $where['created_date <='] = $params['end_date'];
      }
      $data = $this->CI->product_invoice_model->get_data($select,$where,100,0,'invoice_id DESC');      
      if($data){
        $data = $this->format_invoice_list($data);
      }
      return $data;
  }
  function format_invoice_list($data){
    $total = 0;
    foreach ($data as $key => $value) {
      $data[$key]['created_date'] = date ( "d-m-Y H:i", $value['created_date'] );
      $total += $value['amount'];
    }
    $total = number_format($total , 0, ',', '.');
    return array('data'=>$data,'total'=>$total) ;
  }
  function invoice_delete($invoice_id){
      if($invoice_id){
        $where = array("invoice_id"=>$invoice_id);
        $stt = $this->CI->product_invoice_model->delete_data($where);
        return $stt;
      }else
        return false;
  }
}