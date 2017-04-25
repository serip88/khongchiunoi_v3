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
define('MEMBER_TYPE', 'member');
define('ADMIN_TYPE', 'admin');
class User extends Base_controller {

    private $_user_type = '' ;
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library(['user_lib','model_option_user_lib','media_lib']);
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
        
    }

    public function user_get()
    {
        
        /*if (!empty($user))
        {
            $this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }*/
    }
    public function member_list_get()
    {
        $this->_user_type = MEMBER_TYPE;
        $this->user_list_get();
    }
    public function user_list_get()
    {
        $_user_type = $this->_user_type ? $this->_user_type : ADMIN_TYPE;
        $id_type = $this->type_model->get_id_type($_user_type);
        $data = $this->user_lib->get_user_list($id_type);
        if($data){
            $stt=TRUE;
            $data = $this->user_lib->format_user_list($data);
        }
        else 
            $stt=FALSE;

        $this->set_response([
            'status' => $stt,
            'rows' => $data
        ], REST_Controller::HTTP_OK);
    }
    public function user_detail_get()
    {
        $param = $this->get();
        $user_id = isset($param['user_id']) && $param['user_id'] ? $param['user_id'] : 0;
        $data_user = $this->user_lib->get_user($user_id);
        $data_group = $this->user_lib->get_user_group();
        if($data_user && $data_group)
            $stt=TRUE;
        else 
            $stt=FALSE;

        $this->set_response([
            'status' => $stt,
            'user' => $data_user,
            'user_group' => $data_group,
        ], REST_Controller::HTTP_OK);
    }
    public function user_ss_get()
    {
        $status = false;
        $data_user = $this->user_lib->get_user_session();
        if($data_user){
            $status = true;
        }
        $response = array('status' => $status, 'user_data'=> $data_user);
        $this->custom_response($response);
    }
    public function member_group_get()
    {
        $this->_user_type = MEMBER_TYPE;
        $this->user_group();
    }
    public function admin_group_get()
    {
        $this->_user_type = ADMIN_TYPE;
        $this->user_group();
    }
    private function user_group()
    {
        $_user_type = $this->_user_type ? $this->_user_type : MEMBER_TYPE;
        $id_type = $this->type_model->get_id_type($_user_type);
        $data = $this->user_lib->get_user_group($id_type);
        if($data)
            $stt=TRUE;
        else 
            $stt=FALSE;

        $this->set_response([
            'status' => $stt,
            'rows' => $data
        ], REST_Controller::HTTP_OK);
    }
    public function user_save_post(){
        $param = $this->post();
        $stt=FALSE;
        $msg='';
        $param = $this->user_lib->validate_save_user($param);
        if($param){
            $res = $this->user_lib->save_user($param);
            if(!$res['stt']){
                $msg = $res['msg'];
            }else{
                $msg = 'Add User Success';
                $stt = true;
                $option_key = array('max_in_a_group','max_group','current_store_user','group_full_add_more');
                $option = $this->get_option_key($option_key);
                $response = $this->init_user_category($res['user_id'],$option);
            }
            if($res['user_id']){
                //B upload file
                $upload_image = false;
                if(isset($param['file']) ){
                    $file_exit = $this->check_file_exit($param['file']);
                    if($file_exit){
                        $param['new_file'] = $this->move_file_to_post_folder($param['file']['name'],$param['file']['path']);
                        if($param['new_file']){
                            $upload_image = true;
                            $tmp_folder = $this->get_path_folder($param['file']['path']);
                            $this->remove_files_in_folder_by_prefix($tmp_folder,session_id());
                        }
                    }
                }
                //E upload file
                if($upload_image){
                    //B insert media
                    $user_session = $this->user_lib->get_user_session();
                    $data = array();
                    $data['image_name'] = $param['new_file']['name'];
                    $data['image_path'] = $this->get_path_folder($param['new_file']['path'],0,-2);
                    $data['parent_id']  = $res['user_id'];
                    $data['user_created']  = $user_session['user_id'];
                    $data['created_date']  = time();
                    $data['is_main']  = 1;
                    $data['type']  = MEDIA_USER_TYPE;
                    $this->media_model->insert_data($data);
                    //E insert media
                    if(!$stt){
                        $msg = 'Error! Save image have problem.';
                    }
                }
            }
        }
        $response = array('status' => $stt,'msg'=> $msg);
        $this->custom_response($response);
    }
    public function user_edit_post(){
        $stt=FALSE;
        $upload_image = false;
        $param = $this->post();
        $param = $this->user_lib->validate_edit_user($param);
        if($param){
            $stt = $this->user_lib->edit_user($param);
            $have_option = $this->check_option_user($param['user_id']);
            //init_user_category
            if($stt && !$have_option){
                $option_key = array('max_in_a_group','max_group','current_store_user','group_full_add_more');
                $option = $this->get_option_key($option_key);
                $response = $this->init_user_category($param['user_id'],$option);
            }
            if($stt){
                //B upload file
                if(isset($param['file']) ){
                    $file_exit = $this->check_file_exit($param['file']);
                    if($file_exit){
                        $param['new_file'] = $this->move_file_to_post_folder($param['file']['name'],$param['file']['path']);
                        if($param['new_file']){
                            //remove old image if it have
                            if(isset($param['image_path']) && $param['image_path']){
                                list($media_path,$media_name) = $this->sup_get_media_path_name($param['image_path']);
                                $this->unlink_media($media_path,array(IMAGE_SMALL_FOLDER,IMAGE_LARGE_FOLDER,IMAGE_BIG_FOLDER),$media_name);
                            }
                            $upload_image = true;
                            $tmp_folder = $this->get_path_folder($param['file']['path']);
                            $this->remove_files_in_folder_by_prefix($tmp_folder,session_id());
                        }
                    }
                }
                //E upload file
                if($upload_image){
                    //B insert media
                    $user_session = $this->user_lib->get_user_session();
                    $data = array();
                    $data['image_name'] = $param['new_file']['name'];
                    $data['image_path'] = $this->get_path_folder($param['new_file']['path'],0,-2);
                    $data['parent_id']  = $param['user_id'];
                    $data['user_created']  = $user_session['user_id'];
                    $data['created_date']  = time();
                    $data['is_main']  = 1;
                    $data['type']  = MEDIA_USER_TYPE;
                    if(isset($param['media_id']) && $param['media_id']){
                        $where = array("media_id"=> $param['media_id']);    
                        $stt = $this->media_model->update_data($data,$where);
                    }else{
                        $stt = $this->media_model->insert_data($data);
                    }
                    //E insert media
                    if(!$stt){
                        $msg = 'Error! Save image have problem.';
                    }
                }
            }
        }else{
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);// BAD_REQUEST (400) being the HTTP response code
        }
        $this->set_response([
            'status' => $stt
        ], REST_Controller::HTTP_OK);
    }

    public function move_file_to_user_folder($file_name, $file_path, $user_id){
        $this->load->library('upload_lib');
        $option = $this->model_option_user_lib->get_option_user($user_id);
        //Importance check folder before upload
        $this->handle_check_user_folder_is_created($this->dir_path_user,$option,$user_id);
        $path_image = $this->dir_path_user.'/'.$option['group_folder'].'/'.$user_id.'/'.$option['group_current'];
        $file_name = $this->upload_lib->validate_file_in_path($path_image.'/'.IMAGE_BIG, $file_name);
        //$uploadPath = $path_image . '/' . $file_name;
        $uploadPath = $path_image .'/'.IMAGE_BIG . '/' . $file_name;
        //move file to new location
        $stt = rename( FCPATH.$file_path, FCPATH.$uploadPath );
        if($stt){
            try {
                $phpThumb = new phpThumb();
                $phpThumb->setSourceFilename(FCPATH.$uploadPath);
                $phpThumb->setParameter('w', IMAGE_LARGE_SIZE);
                if($phpThumb->GenerateThumbnail()){
                    if(!$phpThumb->RenderToFile(FCPATH.$path_image .'/'.IMAGE_LARGE . '/' . $file_name)){
                        return false;
                    }
                }else{
                    return false;
                }
                //Importance check full folder before upload
                //$this->handle_check_folder_is_over_load($this->dir_path_post,$option);
                return array( 'name'=>$file_name,'path'=>$uploadPath );
            } catch (Exception $e) {
                return false;
            }
        }else{
            return false;
        }
    }
    /*public function users_post()
    {
        // $this->some_model->update_user( ... );
        $message = [
            'id' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }*/
    public function user_delete_post(){
        $params = $this->post();
        $users_id = isset($params['user_delete']) && $params['user_delete']?$params['user_delete']:array();
        $msg = '';
        $status = false;
        $count_false = 0;
        if(count($users_id)){
            foreach ($users_id as $key => $id) {
                try {
                    $stt = $this->user_lib->user_delete($id);
                    if($stt){
                        //REMOVE PROFILE PICTURE
                        $this->media_lib->delete_media($id);
                    }else{
                        $count_false = $count_false +1;    
                    }
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
    public function users_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

    public function test_get(){
        echo $this->user_lib->test();die;
    }
   
}
