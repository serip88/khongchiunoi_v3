<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/Base_controller.php';

class Access extends Base_controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library(config_item('app_path').'/'.'User_lib');
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_post()
    {
        $response = $arrayName = array(
            'success' => 1,
            'rows'=>$this->post()
        );
        $this->response($response, REST_Controller::HTTP_OK);
    }
    public function login_post(){
        $status = 0;
        $msg = '';
        $param = $this->post();
        $email = isset($param['email']) && $param['email'] ? $param['email'] : '';
        $_user_type = isset($param['t']) && $param['t'] ? $param['t'] : '';
        $password = isset($param['password']) && $param['password'] ? $param['password'] : '';
        $user_data = array();
        if($_user_type=='m'){
            $_user_type = MEMBER_TYPE;
        }else{
            $_user_type = ADMIN_TYPE;
        }
        $id_type = $this->type_model->get_id_type($_user_type);
        if ($email) {
            $user_data = $this->user_lib->get_user_by_email($email);            
            if($user_data){
                if($user_data['type'] == $id_type){
                    if($user_data['status']==1){
                        $where = array();
                        $where['email'] = $email;
                        $where['password'] = md5($user_data['salt'].$password);
                        $status = $this->user_lib->check_user($where);
                        if($status){
                            $msg = 'Login success';
                            if($_user_type == MEMBER_TYPE){
                                $this->user_lib->set_member_session($user_data);
                            }else{
                                $this->user_lib->set_user_session($user_data);
                            }
                        }else{
                            $msg = 'Password Incorrect';   
                        }
                    }else{
                        $msg = 'This email/user deactived';
                    }    
                }else{//type not valid to login admin cannot login in client
                    $msg = 'User or Email not valid';
                }
            }else{
                $msg = 'Email is wrong';
            }
        }
        $response = array('status' => $status,'msg'=> $msg,'user_data'=>$user_data);
        $this->custom_response($response);
        
    }
    public function logout_post(){
        $_user_type = isset($param['t']) && $param['t'] ? $param['t'] : '';
        $status = false;
        if($_user_type=='a'){
            $this->user_lib->unset_user_session();
            if(!$_SESSION['user_data']){
                $status = true;
            }
        }else{
            $this->user_lib->unset_member_session();
                if(!$_SESSION['member_data']){
                $status = true;
            }
        }
        $response = array('status' => $status);
        $this->custom_response($response);
    }

   

}
