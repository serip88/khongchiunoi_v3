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
class Media_lib extends Common_lib {
  protected $CI = null ;

  function __construct() {
      $this->CI =& get_instance();
      $this->CI->load->database('default');
      $this->CI->load->model(array('media/media_model'));
      
  }
  
  function delete_media($user_id){
      $media_data = $this->CI->media_model->get_media_by_parent($user_id,MEDIA_USER_TYPE);
      if($media_data){
        foreach ($media_data as $key => $media) {
          $this->CI->unlink_media($media['image_path'],array(IMAGE_SMALL_FOLDER,IMAGE_LARGE_FOLDER,IMAGE_BIG_FOLDER),$media['image_name']);
        }
        $this->CI->media_model->delete_media($media['media_id']);
      }
  }

}