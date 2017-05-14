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

class Upload_lib extends Common_lib {
  protected $CI = '';
  function __construct()
  {
    $this->CI =& get_instance();
      
  }
  
  function validate_file_in_path($path,$filename){
    $path = str_replace("//", "/",FCPATH.$path);
    while ( file_exists($path.'/'.$filename) )
    {
      $filename = rand(1, 9).$filename;
      if(strlen($filename)>50){
          $filename=substr($filename, 10);
      }
    }
    return $filename;
  }

}