<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/Base_controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @category        Controller
 * @author          Rain
 */
class User_group extends Base_controller {

	function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library('user_lib');
    }
	
	public function permissions_get(){
        $stt = false;
		$list_action = $this->get_list_action();
        $type_group = $this->type_model->get_type('user_group');
        if($type_group){
            $stt = true;
        }
		$response = array('status' => $stt,'rows' => array('list_permissions'=>$list_action,'type_group'=>$type_group));
		$this->custom_response($response);
	}

	public function save_post(){
		$param = $this->post();
		$stt=FALSE;
        $msg='';
        $param = $this->user_lib->validate_save_user_group($param);
        if($param){
            $stt = $this->user_lib->save_user_group($param);
            if(!$stt){
                $msg = 'Error! Cannot create user group.';
            }
        }
        $response = array('status' => $stt,'msg'=> $param);
        $this->custom_response($response);
	}

	public function detail_get(){
		$param = $this->get();
        $group_id = isset($param['id']) && $param['id'] ? $param['id'] : 0;
        $data_group = $this->user_lib->get_user_group_detail($group_id);
        $list_action = $this->get_list_action();
        $type_group = $this->type_model->get_type('user_group');
        if($data_group && $list_action && $type_group){
            $stt=TRUE;
            $data_group['type_group']= $type_group;
            $data_group['permission']=  json_decode($data_group['permission'],true);
            $data_group['list_permissions'] = $list_action;
            $data_group['permission']['access'] = array_uintersect($data_group['permission']['access'],$list_action,array($this,'array_matches'));
            $data_group['permission']['modify'] = array_uintersect($data_group['permission']['modify'],$list_action,array($this,'array_matches'));
        }
        else 
            $stt=FALSE;
        $response = array('status' => $stt,'msg'=> $param,'data'=>$data_group);
        $this->custom_response($response);
	}
	public function edit_post(){
        $param = $this->post();
        $param = $this->user_lib->validate_edit_user_group($param);
        $stt = FALSE;
        $msg ='';
        if($param){
            $stt = $this->user_lib->edit_user_group($param);
            $msg = 'Edit success!';
        }else{
            $msg = 'Edit not success!';
        }
            
        $response = array('status' => $stt,'msg'=> $msg);
        $this->custom_response($response);
    }
    public function delete_post(){
        $params = $this->post();
        $group_id = isset($params['group_delete']) && $params['group_delete']?$params['group_delete']:array();
        $msg = '';
        $status = false;
        $count_false = 0;
        if(count($group_id)){
            foreach ($group_id as $key => $id) {
                try {
                    $stt = $this->user_lib->user_group_delete($id);
                    if(!$stt)
                        $count_false = $count_false +1;    
                } catch (Exception $e) {
                    $count_false = $count_false +1;
                    //echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
        } 
        if($count_false == 0){
            $status = true;
            $msg = 'delete success';
        }else{
            $msg = 'delete false';
        }
        $response = array('status' => $status,'msg' => $msg);
        $this->custom_response($response);
    }
}