<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Pages
 *
 * @author Rain: serip88@gmail.com
 */
require APPPATH . '/libraries/BaseUI_controller.php';
class Client extends BaseUI_controller{
  public function __construct() {
    parent::__construct();
    //$this->load->database();
    $this->load->model('post/post_model');
    //$this->load->library(array(config_item('app_path').'/page_lib','common/CI_Smarty'));
    $this->baseURL =  base_url();
    $this->check_none_www();
  }
  public function index()
  { 
  	//$this->rz_debug($this->page_lib->get_main_menu());die;
  	//$this->page_info['main_menu']=$this->page_lib->get_main_menu();
    
    $this->load->view(sprintf('%s/index',config_item('app_path')));

  	//$this->ci_smarty->assign('page_info', $this->page_info);
    // $tpl_path = sprintf(APPPATH.'views\%s\index.tpl',config_item('app_path'));
    // $this->ci_smarty->display($tpl_path);
  }
  private function check_none_www(){
      $actual_link = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      $arr_para_url= explode(".",$actual_link);
      if($arr_para_url[0]!='www'){
          header('Location: '.$this->baseURL);
      }
  }
  public function change()
  { 
    //$this->rz_debug($this->page_lib->get_main_menu());die;
    $this->page_info['main_menu']=$this->page_lib->get_main_menu();
    //$this->load->view('index');
    $this->ci_smarty->assign('page_info', $this->page_info);
    $this->ci_smarty->display( APPPATH.'views\client\tpl2\index.tpl' );
  }
  public function home()
  { 
    //$this->rz_debug($this->page_lib->get_main_menu());die;
    $this->page_info['main_menu']=$this->page_lib->get_main_menu();
    //$this->load->view('index');
    $this->ci_smarty->assign('page_info', $this->page_info);
    $this->ci_smarty->display( APPPATH.'views\client\index-1.tpl' );
  }
  public function test()
  { 
    //$this->rz_debug($this->page_lib->get_main_menu());die;
    $this->page_info['main_menu']=$this->page_lib->get_main_menu();
    $this->load->view('index-1');
  }
  public function category()
  { 
    $this->page_info['main_menu']=$this->page_lib->get_main_menu();
    $this->ci_smarty->assign('page_info', $this->page_info);
    $this->ci_smarty->display( APPPATH.'views\client\category.tpl' );
  }
   
}
