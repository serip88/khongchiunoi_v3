<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/Base_controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Email extends Base_controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library(config_item('app_path').'/'.'email_lib');
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
        
    }
    public function save_post(){
        $param = $this->post();
        $stt=FALSE;
        $msg='';
        $param = $this->email_lib->validate_save($param);
        //B check duplicate email
        $where = array('email'=>$param['email']);
        $data = $this->email_model->get_record($where);
        //E check duplicate email
        if(!$data){
            if($param){
                $stt = $this->email_lib->save($param);
                if(!$stt){
                    $msg = 'Error! Cannot save category.';
                }
            }
        }else{
            $msg = 'Error! Duplicate email.';
        }
        
        $response = array('status' => $stt,'msg'=> $msg);
        $this->custom_response($response);
    }

    public function edit_post(){
        $param = $this->post();
        $stt=FALSE;
        $msg='';
        $param = $this->email_lib->validate_edit($param);
        if($param){
            $stt = $this->email_lib->edit($param);
            if(!$stt){
                $msg = 'Error! Cannot save category.';
            }
        }
        $response = array('status' => $stt,'msg'=> $msg);
        $this->custom_response($response);
    }

    public function list_get(){
    	$data = $this->email_lib->get_list();
        if($data){
            $data = $this->email_lib->list_format($data);
            $stt=TRUE;
        }
        else 
            $stt=FALSE;

        $this->set_response([
            'status' => $stt,
            'rows' => $data
        ], REST_Controller::HTTP_OK);
    }
    public function delete_post(){
    	$params = $this->post();
        $ids = isset($params['email_delete']) && $params['email_delete']?$params['email_delete']:array();
        $msg = '';
        $status = false;
        $count_false = 0;
        if(count($ids)){
            foreach ($ids as $key => $id) {
                try {
                    $stt = $this->email_lib->delete($id);
                    if(!$stt)
                        $count_false = $count_false +1;    
                } catch (Exception $e) {
                    $count_false = $count_false +1;
                }
            }
        } 
        if($count_false == 0){
            $status = true;
            $msg = 'delete success';
        }else{
            $msg = $msg? $msg.'<br/> $count_false email cannot delete': "$count_false email cannot delete";
        }
        $response = array('status' => $status,'msg' => $msg);
        $this->custom_response($response);
    }
}