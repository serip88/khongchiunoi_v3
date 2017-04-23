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
require APPPATH . '/libraries/Common_lib.php';
class User_lib extends Common_lib {
  protected $CI = null ;
  protected $_lang ;

  function __construct() {
      $this->CI =& get_instance();
      $this->CI->load->database('default');
      $this->CI->load->model(array('user/user_group_model','user/user_model','media/media_model','type/type_model'));
      $this->_config =  $this->CI->config;
      $this->init_lang();
  }

  function init_lang(){
      $default_lang = isset($_COOKIE['language'])?$_COOKIE['language']:"vietnamese";
      $this->CI->load->language('user/user_controller',$default_lang);
      $this->_lang = $this->CI->lang->language;
      //return $this->_lang;
  }
  function get_user_list($id_type){
    $select="A.user_id,A.username,A.status,A.date_added,A.user_group_id,A.firstname,A.lastname,A.email,A.image_path,B.image_path as media_path,B.image_name as media_name,B.media_id";
    $where = array('C.type'=>$id_type);
    $tb_join = array();
    $tb_join[] = array('table_name'=>'rz_media as B','condition'=>"A.user_id =B.parent_id", 'type'=>'left');
    $tb_join[] = array('table_name'=>'rz_user_group as C','condition'=>"A.user_group_id =C.user_group_id", 'type'=>'left');
    $data = $this->CI->user_model->get_data_join($select,$where,$tb_join);
    return $data;
  }
  function format_user_list($data){
    foreach ($data as $key => $value) {
      //list($date, $time) = explode(' ',$value['date_added']);
      $date = date("Y-m-d H:i", time());
      $data[$key]['ui_date_added'] = $date;
      $data[$key]['ui_status'] =  $value['status']?$this->_lang['user_status_enabled']:$this->_lang['user_status_disabled'];
      $data[$key]['image_path'] = implode("/", array($value['media_path'],'large',$value['media_name']));  
      if(!file_exists($data[$key]['image_path'])){
        $data[$key]['image_path'] = '';
      }
    }
    return $data;
  }
  function get_user($user_id){

    $select="A.user_id,A.user_group_id,A.username,A.salt,A.firstname,A.lastname,A.email,A.image_path,A.date_added,B.name";
    $tb_join = array();
    $tb_join[] = array('table_name'=>'rz_user_group as B','condition'=>"A.user_group_id =B.user_group_id", 'type'=>'left');
    $where = array("A.user_id"=>$user_id);
    $data = $this->CI->user_model->get_data_join($select,$where,$tb_join,1);
    if($data){
      return $data[0];
    }else{
      return 0;  
    }
  }
  function get_user_by_email($user_email){
    $select="A.user_id,A.user_group_id,A.username,A.salt,A.firstname,A.lastname,A.email,A.image_path,A.date_added,A.status,B.name";
    $tb_join = array();
    $tb_join[] = array('table_name'=>'rz_user_group as B','condition'=>"A.user_group_id =B.user_group_id", 'type'=>'left');
    $where = array("A.email"=>$user_email);
    $data = $this->CI->user_model->get_data_join($select,$where,$tb_join,1);
    if($data){
      return $data[0];
    }else{
      return 0;  
    }
  }
  function check_user($where = array()){
    if($where){
      $data = $this->CI->user_model->get_total($where);
      return $data;
    }else
      return 1;
      
  }
  function get_user_group($type){

    $select="user_group_id as id,name";
    $where = array('type'=>$type);
    $data = $this->CI->user_group_model->get_data($select,$where);
    return $data;
  }
  function validate_save_user($param){
    $requite = array('user_group_id','username','firstname','email','password','confirm');
    $param['user_group_id'] = isset($param['user_group_id']) && $param['user_group_id'] ?$param['user_group_id']: null;
    $param['username'] = isset($param['username']) && $param['username'] ?$param['username']: null;
    $param['firstname'] = isset($param['firstname']) && $param['firstname'] ?$param['firstname']: null;
    $param['lastname'] = isset($param['lastname']) && $param['lastname'] ?$param['lastname']: '';
    $param['email'] = isset($param['email']) && $param['email'] ?$param['email']: null;
    $param['password'] = isset($param['password']) && $param['password'] ?$param['password'] : null;
    $param['confirm'] = isset($param['confirm']) && $param['confirm'] ?$param['confirm']: null;
    $param['status'] = isset($param['status']) && $param['status'] ?$param['status']: 0;
    foreach ($requite as $key => $value) {
      if(!$param[$value]){
        return 0;
      }
    }
    if( !$param['password'] || ($param['password'] != $param['confirm'])){
      return 0;
    }
    return $param;
  }

  function save_user($param){
    $stt = false;
    $msg = "";
    $res = array();
    $data =array();
    $data['user_group_id'] = $param['user_group_id'];
    $data['username'] = $param['username'];
    $data['firstname'] = $param['firstname'];
    $data['lastname'] = $param['lastname']; 
    $data['email'] = $param['email'];
    $data['salt'] = $this->generateRandomString();
    $data['password'] = md5($data['salt'].$param['password']);
    $data['code'] = '';
    $data['ip']   = $_SERVER["REMOTE_ADDR"];//$param['ip'];
    $data['date_added'] = time();
    $data['status'] = $param['status'];
    //B check username,email
      $select = array('select'=>'user_id,username,email');
      $where = array('where'=> array(),'or_where'=> array());
      $where['where'] = array('username'=>$param['username']);
      $where['or_where'] = array('email'=>$param['email']);
      $limit = array('type'=>'rows','limit'=>1);
      $u_data = $this->CI->user_model->get_dt($select,$where,$limit);  
    //E check username,email
      if(!$u_data){
        $stt = $this->CI->user_model->insert_data($data);
      }else{
        foreach ($u_data as $key => $value) {
          if($value['username']==$param['username']){
            $msg = 'This username not available';
            break;
          }elseif ($value['email']==$param['email']) {
            $msg = 'This email already register';
            break;
          }
        }
      }
      $res['stt']= $stt;
      $res['msg']= $msg;
      $res['user_id']= $stt;
    return $res;
  }

  function validate_edit_user($param){
    $requite = array('user_group_id','firstname','email','user_id');

    $param['user_id'] = isset($param['user_id']) && $param['user_id'] ?$param['user_id']: null;
    $param['user_group_id'] = isset($param['user_group_id']) && $param['user_group_id'] ?$param['user_group_id']: null;
    $param['firstname'] = isset($param['firstname']) && $param['firstname'] ?$param['firstname']: null;
    $param['lastname'] = isset($param['lastname']) && $param['lastname'] ?$param['lastname']: '';
    $param['email'] = isset($param['email']) && $param['email'] ?$param['email']: null;
    $param['password'] = isset($param['password']) && $param['password'] ?$param['password']: null;
    $param['confirm'] = isset($param['confirm']) && $param['confirm'] ?$param['confirm']: null;
    $param['status'] = isset($param['status']) && $param['status'] ?$param['status']: 0;
    //if one in requite is null or '' or 0, it will be return false
    foreach ($requite as $key => $value) {
      if(!$param[$value]){
        return false;
      }
    }
    if($param['password'] && $param['password'] != $param['confirm']){
      return false;
    }
    return $param;
  }
  function edit_user($param){
    $data =array();
    $data['user_group_id'] = $param['user_group_id'];
    $data['firstname'] = $param['firstname'];
    $data['lastname'] = $param['lastname'];
    $data['email'] = $param['email'];
    if(isset($param['password']) && $param['password'] ){
      $data_user=$this->get_user($param['user_id']);
      if($data_user){
        if(!$data_user['salt']){
          $data_user['salt']= $this->generateRandomString();
          $data['salt'] = $data_user['salt'];
        }
        $data['password'] = md5($data_user['salt'].$param['password']);  
      }else{
        return FALSE;
      }
    }
    $data['code'] = 'code';
    $data['ip']   = $_SERVER["REMOTE_ADDR"];//$param['ip'];
    $data['updated_date'] = time();
    $data['status'] = $param['status'];
    //B check email
      $select = array('select'=>'user_id');
      $where = array('where'=> array());
      $where['where'] = array('email'=>$param['email']);
      $limit = array('type'=>'int','limit'=>1);
      $not_valid = $this->CI->user_model->get_dt($select,$where,$limit);  
    //E check email
    if(isset($param['user_id']) && $param['user_id'] && $not_valid<=1){
      $where = array("user_id"=> $param['user_id']);
      $stt = $this->CI->user_model->update_data($data,$where); 
      return $stt;
    }else{
      return FALSE;
    }
    
  }
  
  function user_delete($user_id){
    if($user_id){
      $where = array("user_id"=>$user_id);
      $stt = $this->CI->user_model->delete_data($where);
      return $stt;
    }else
      return false;
  }
  
  function set_user_session($user_data){      
      $_SESSION['user_data'] = $user_data;
  }
  function get_user_session(){  
      if(isset($_SESSION['user_data']) && $_SESSION['user_data']){
        return $_SESSION['user_data'];
      }else{
        return '';
      }   
  }
  function unset_user_session(){
    $_SESSION['user_data'] = '';
  }

  function validate_save_user_group($param){
    $requite = array('user_group_name','type_selected');
    $param['user_group_name'] = isset($param['user_group_name']) && $param['user_group_name'] ?$param['user_group_name']: null;
    $param['type_selected'] = isset($param['type_selected']) && $param['type_selected'] ?$param['type_selected']: 0;
    foreach ($requite as $key => $value) {
      if(!$param[$value]){
        return 0;
      }
    }
    if(!isset($param['access_selected']) || !isset($param['modify_selected']) ){
      return 0;
    }
    return $param;
  }
  function save_user_group($param){
    $data =array();
    $permission = array('access'=>$param['access_selected'],'modify'=>$param['modify_selected'] );
    $data['name'] = $param['user_group_name'];
    $data['slug'] = $this->CI->user_group_model->get_slug($param['user_group_name']);
    $data['type'] = $param['type_selected'];
    $data['permission'] = json_encode($permission);
    $id = $this->CI->user_group_model->insert_data($data);
    return $id;
  }
  function get_user_group_detail($group_id){

    $select="user_group_id as id,name,type,permission";
    $where = array('user_group_id'=>$group_id);
    $data = $this->CI->user_group_model->get_data($select,$where);
    if($data){
      return $data[0];
    }else{
      return 0;  
    }
  }

  function validate_edit_user_group($param){
    $requite = array('user_group_name','id','type_selected');
    $param['user_group_name'] = isset($param['user_group_name']) && $param['user_group_name'] ?$param['user_group_name']: null;
    $param['id'] = isset($param['id']) && $param['id'] ?$param['id']: 0;
    $param['type_selected'] = isset($param['type_selected']) && $param['type_selected'] ?$param['type_selected']: 0;
    foreach ($requite as $key => $value) {
      if(!$param[$value]){
        return 0;
      }
    }
    if(!isset($param['access_selected']) || !isset($param['modify_selected']) ){
      return 0;
    }

    return $param;
  }
  function edit_user_group($param){
    if(isset($param['id']) && $param['id']){
      $data =array();
      $permission = array('access'=>$param['access_selected'],'modify'=>$param['modify_selected'] );
      $data['name'] = $param['user_group_name'];
      $data['slug'] = $this->CI->user_group_model->get_slug($param['user_group_name'],'edit');
      $data['type'] = $param['type_selected'];
      $data['permission'] = json_encode($permission);
      $where = array("user_group_id"=> $param['id']);
      $stt = $this->CI->user_group_model->update_data($data,$where); 
      return $stt;
    }else{
      return false;
    }

  }
  function user_group_delete($group_id){
    if($group_id){
      $where = array("user_group_id"=>$group_id);
      $stt = $this->CI->user_group_model->delete_data($where);
      return $stt;
    }else
      return false;
  }
}