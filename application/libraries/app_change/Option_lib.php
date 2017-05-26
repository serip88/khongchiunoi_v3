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

class Option_lib extends Common_lib {
  protected $CI = '';
  function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->database('default');
    $this->CI->load->model(array(
        'option/Option_Model',
    ));
      
  }
  //$name= string, $data = array();
  function update_option_folder($name,$data){
    $stt = false;
    if(is_array($data)){
      $where = array("name"=> $name);
      $stt = $this->CI->Option_Model->update_data($data,$where); 
    }
    return $stt;
  }

  function update_option($name,$value){
    $where = array("name"=> $name);
    $data = array('value'=>$value);
    $stt = $this->CI->Option_Model->update_data($data,$where); 
    return $stt;
  }

}