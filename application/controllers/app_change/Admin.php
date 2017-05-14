<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of AdminController
 *
 * @author Rain
 */
class Admin extends CI_Controller {
	public function __construct()
  {
      parent::__construct();
      //$this->load->helper('url');
      $this->check_none_www();
  }
  public function index(){
    //$this->load->view('tpl/header',$data);
    //$this->load->view('admin/login_page');
    $this->load->view('app_change_ad/template_1st.php');
    //$this->load->view('tpl/footer',$data);
  }
  public function dashboard(){
    //$this->load->view('admin/header',$data);
    //$this->load->view('tpl/sidebar');
    $this->load->view('admin/template_1st.php');
    //$this->load->view('tpl/footer',$data);
  }

  private function check_none_www(){
    $actual_link = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $arr_para_url= explode(".",$actual_link);
    if($arr_para_url[0]!='www'){
      echo "<script> window.location='http://www.$actual_link';</script>";die;
    }
  }
}
