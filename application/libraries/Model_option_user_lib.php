<?php
/*
  +------------------------------------------------------------------------+
  | Copyright (C) 2016 R3nza.                                              |
  |                                                                        |
  | This program is free software; you can redistribute it and/or          |
  | modify it under the terms of the R3nza  License                        |
  |                                                                        |
  +------------------------------------------------------------------------+
  | o Developer : Rain                                                     |
  | o HomePage  : http://www.r3nza.com/                                    |
  | o Email     : admin@r3nza.com                                          |
  +------------------------------------------------------------------------+
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_Option_User_lib extends Common_lib {
  protected $CI = '';
  function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->database('default');
    $this->CI->load->model(array(
        'user/Option_User_Model',
    ));
      
  }
  
  function insert_option_user($user_id,$group_folder){
    $data = array();
    $data['user_id']  = $user_id;
    $data['group_folder']  = $group_folder;
    $data['group_current']  = 1;
    $id = $this->CI->Option_User_Model->insert_data($data);
    return $id;
  }

  function get_option_user($user_id){
    $select = array('user_id','group_folder','group_current');
    $where = array('user_id'=>$user_id);
    $option = $this->CI->Option_User_Model->get_data($select,$where,1);
    return $option?$option[0]:0;
  }

}