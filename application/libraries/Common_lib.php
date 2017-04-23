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
if (!class_exists('Common_lib')){
  class Common_lib {
      function token($length = 32) {
      // Create token to login with
      $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      
      $token = '';
      
      for ($i = 0; $i < $length; $i++) {
        $token .= $string[mt_rand(0, strlen($string) - 1)];
      } 
      
      return $token;
    }
    function generateRandomString($length = 9) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }
    function rz_debug() {
        $trace = debug_backtrace();
        $rootPath = dirname(dirname(__FILE__));
        $file = str_replace($rootPath, '', $trace[0]['file']);
        $line = $trace[0]['line'];
        $var = $trace[0]['args'][0];
        $lineInfo = sprintf('<div><strong>%s</strong> (line <strong>%s</strong>)</div>', $file, $line);
        $debugInfo = sprintf('<pre>%s</pre>', print_r($var, true));
        print_r($lineInfo.$debugInfo);
    }
    function name_on_bar($string) {
      $string = $this->m_unhtmlchars(stripslashes($this->utf8_to_ascii($string)));
      $string = str_replace ( ' ', '-', $string );
      $string = str_replace ( '~', '', $string );
      $string = str_replace ( "!", "", $string );
      $string = str_replace ( "@", "", $string );
      $string = str_replace ( '#', '', $string );
      $string = str_replace ( '$', '', $string );
      $string = str_replace ( '%', '', $string );
      $string = str_replace ( '^', '', $string );
      $string = str_replace ( '&', '', $string );
      $string = str_replace ( '*', '', $string );
      $string = str_replace ( '(', '', $string );
      $string = str_replace ( ')', '', $string );
      $string = str_replace ( '+', '', $string );
      $string = str_replace ( '`', '', $string );
      $string = str_replace ( '=', '', $string );
      $string = str_replace ( '_', '', $string );
      $string = str_replace ( '|', '', $string );
      $string = str_replace ( '}', '', $string );
      $string = str_replace ( '{', '', $string );
      $string = str_replace ( '"', '', $string );
      $string = str_replace ( ':', '', $string );
      $string = str_replace ( '?', '', $string );
      $string = str_replace ( '>', '', $string );
      $string = str_replace ( '<', '', $string );
      $string = str_replace ( "'", "", $string );
      $string = str_replace ( "/", "-", $string );
      $string = str_replace ( ']', '', $string );
      $string = str_replace ( '[', '', $string );
      $string = str_replace ( ';', '', $string );
      $string = str_replace ( '.', '', $string );
      $string = str_replace ( ',', '', $string );
      $string = str_replace ( '–', '-', $string );

      $string = str_replace ( '“', '', $string );
      $string = str_replace ( '”', '', $string );
      $string = str_replace ( '---', '-', $string );
      $string = str_replace ( '--', '-', $string );
      return $string;
    }
    function m_unhtmlchars($str) {
      return str_replace(array('&lt;', '&gt;', '&quot;', '&amp;', '&#92;', '&#39'), array('<', '>', '"', '&', chr(92), chr(39)), $str);
    }
    function utf8_to_ascii($str) {
      $chars = array(
        'a' =>  array('ấ','ầ','ẩ','ẫ','ậ','Ấ','Ầ','Ẩ','Ẫ','Ậ','ắ','ằ','ẳ','ẵ','ặ','Ắ','Ằ','Ẳ','Ẵ','Ặ','á','à','ả','ã','ạ','â','ă','Á','À','Ả','Ã','Ạ','Â','Ă'),
        'e' =>  array('ế','ề','ể','ễ','ệ','Ế','Ề','Ể','Ễ','Ệ','é','è','ẻ','ẽ','ẹ','ê','É','È','Ẻ','Ẽ','Ẹ','Ê'),
        'i' =>  array('í','ì','ỉ','ĩ','ị','Í','Ì','Ỉ','Ĩ','Ị'),
        'o' =>  array('ố','ồ','ổ','ỗ','ộ','Ố','Ồ','Ổ','Ô','Ỗ','Ộ','ớ','ờ','ở','ỡ','ợ','Ớ','Ờ','Ở','Ỡ','Ợ','ó','ò','ỏ','õ','ọ','ô','ơ','Ó','Ò','Ỏ','Õ','Ọ','Ô','Ơ'),
        'u' =>  array('ứ','ừ','ử','ữ','ự','Ứ','Ừ','Ử','Ữ','Ự','ú','ù','ủ','ũ','ụ','ư','Ú','Ù','Ủ','Ũ','Ụ','Ư'),
        'y' =>  array('ý','ỳ','ỷ','ỹ','ỵ','Ý','Ỳ','Ỷ','Ỹ','Ỵ'),
        'd' =>  array('đ','Đ'),
      );
      foreach ($chars as $key => $arr) 
        foreach ($arr as $val)
          $str = str_replace($val,$key,$str);
      return strtolower($str);
    } 
  }
}