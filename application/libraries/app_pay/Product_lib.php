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
      $requite = array('name');//description,status,parent_id
      $param['name']   = isset($param['name']) && $param['name'] ?$param['name']: '';
      $param['name']   = str_replace('/', '-', $param['name']);
      $param['price']     = isset($param['price']) && $param['price'] ? $param['price']: 0;
      $param['price']     = floatval($param['price']) ;
      $param['status']    = isset($param['status']) && $param['status'] ? $param['status']: 0;   
      $param['parent_id']   = isset($param['parent_id']) && $param['parent_id'] ? $param['parent_id']: 0;   
      $param['description'] = isset($param['description']) && $param['description'] ? $param['description']: '';   
      $param['date_discount'] = isset($param['date_discount']) && $param['date_discount'] ? $param['date_discount']: 0;
      $param['hours_discount'] = isset($param['hours_discount']) && $param['hours_discount'] ? $param['hours_discount']: 0;
      if($param['date_discount']){
        if(strlen($param['date_discount']) == 24){
          $tmp_date = strtotime($param['date_discount']);
        }else{
          $tmp_date = $param['date_discount']/1000;
        }
        if(strlen($param['hours_discount']) == 24){
          $tmp_time = strtotime($param['hours_discount']);  
        }else{
          $tmp_time = $param['hours_discount']/1000;
        }
        $tmp_time = date("H:i:s", $tmp_time);
        $time_discount = strtotime($tmp_time, $tmp_date);
      }else{
        $time_discount = 0;
      }
      
      $param['price_discount']     = isset($param['price_discount']) ? $param['price_discount']: 0;
      if($param['price_discount']){
        $param['time_discount'] = $time_discount;  
      }else{
        $param['time_discount'] = 0;
      }
      $param['price_discount']     = floatval($param['price_discount']) ;

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
    $data['price_discount']  = $param['price_discount'];
    $data['time_discount']  = $param['time_discount'];
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
  function get_product_list($param){
      $limit = isset($param['limit']) ? $param['limit'] :  9;
      $page = isset($param['page']) ? $param['page'] :  1;
      $mode = isset($param['mode']) ? $param['mode'] :  'all_client';
      $param['keyword'] = isset($param['keyword']) ? $param['keyword'] :  false;
      $start = ($page - 1) * $limit;
      $category_id = isset($param['category_id']) ? (int)$param['category_id'] :  0;

      $options = array();
      $options['limit'] = $limit;
      $options['start'] = $start;
      $select="A.*, B.name AS cate_name";
      $tb_join = array();
      $tb_join[] = array('table_name'=>'rz_tag as B','condition'=>"B.id =A.parent_id", 'type'=>'left');
      //$where = array('A.enabled' => 1);
      $where = array("A.price > 0");
      if($mode == 'discount'){
        $where[] = "AND A.price >0";
        $where[] = sprintf("AND A.time_discount > %s", time());
      }elseif($mode == 'all_client'){
        $where[] = "AND A.enabled = 1";
        //$where[] = sprintf("AND IF (A.price_discount > 0 , A.time_discount > %s, 1)", time());
        $where[] = sprintf("AND A.price_discount = 0", time());
      }
      if($param['keyword']){
        $where[] = "AND A.name LIKE '%".$param['keyword']."%'"; 
      }
      if($category_id != false && $category_id != -1){
        $where[] = "AND A.parent_id = $category_id";
      }
      $where = implode(" ", $where);
      $data = $this->CI->product_model->get_data_join($select,$where,$tb_join,$options);
      if($data){
        $data = $this->format_product_list($data);
        //B pagination
        $pagination = array();
        $options['count'] = 1;
        $pagination['total'] = $this->CI->product_model->get_data_join($select,$where,$tb_join,$options);
        $max_page = ceil($pagination['total']/ $limit);
        $pagination['max_page'] =  $max_page ? $max_page : 1 ;
        $pagination['current_page'] = $page;
        $pagination['limit'] = $limit;
        //E pagination
        return array('data'=>$data,'pagination'=>$pagination) ;
      }else{
        return false;
      }
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
      $data['price_discount']  = $param['price_discount'];
      $data['time_discount']  = $param['time_discount'];
      $data['slug']     = $param['name'];
      $data['parent_id']  = $param['parent_id'];
      $data['description'] = $param['description'];
      $data['path_category_id'] = $param['path_category_id'];
      $data['level']    = 0;
      $data['orders']    = 0;
      $data['posted_date'] = time();
      $data['enabled']  = $param['enabled']; 
      if(isset($param['file_name_mb']) && $param['file_name_mb'] && isset($param['file_path_mb']) && $param['file_path_mb'] ){
        $data['image_name']  = $param['file_name_mb']; 
        $data['image_path'] = $param['file_path_mb']; 
      }

      if(isset($param['product_id']) && $param['product_id']){
        $where = array("product_id"=> $param['product_id']);
        $stt = $this->CI->product_model->update_data($data,$where); 
        //echo  $this->CI->product_model->db->last_query();die;
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
        // $data[$key]['price_pure'] = (int)$value['price'];
        // $data[$key]['price'] = number_format($value['price'] , 0, ',', '.');
        $data[$key]['description_format'] = nl2br($value['description']) ;
        $data[$key]['price_pure'] = floatval($value['price']) ;
        $data[$key]['price'] = floatval($value['price']) ;
        $data[$key]['price_discount'] = floatval($value['price_discount']) ;
        if($data[$key]['price_discount']){
          $data[$key]['price_old'] = $data[$key]['price'];
          $data[$key]['price'] = $data[$key]['price_discount'];
        }else{
          $data[$key]['price_old'] = 0;
        }
        //$data[$key]['price'] = number_format($value['price'] , 2, '.', ',');
      }
      if($value['image_path']){
        $data[$key]['image_path'] = trim($value['image_path'],".");
      }
      $data[$key]['quantity'] = 1;
      $data[$key]['last_price'] = $data[$key]['price'];
      $data[$key]['time_discount_frm'] = date("Y/m/d H:i", $data[$key]['time_discount']);
      $data[$key]['time_discount'] = $value['time_discount'] ? $value['time_discount']*1000: NULL ;
      $data[$key]['date_discount'] = $data[$key]['time_discount'];
      $data[$key]['hours_discount'] = $data[$key]['time_discount'];
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