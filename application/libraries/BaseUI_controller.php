<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Pages
 *
 * @author Rain
 */

class BaseUI_controller extends CI_Controller{
   public function __construct() {
     	parent::__construct();
     	$this->load->helper('url');
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
   
}
