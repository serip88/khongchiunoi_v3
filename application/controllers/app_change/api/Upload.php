<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/Base_controller.php';

class Upload extends Base_controller {
    private $dir_path="";
    private $dir_tmp="";
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->dir_path='./app/images';
        $this->dir_tmp= $this->dir_path . '/tmp';
        $this->load->library('upload_lib');
    }

    /*public function upload_img_user_post(){
        $msg = '';
        $status = false;
        $answer = array();
        $data_user = $this->get_user_session();
        if( !empty($_FILES) && $data_user && $data_user['username']) {
            try {
                $option = $this->handle_get_option_post_folder();
                $this->handle_check_option_folder_is_created($this->dir_path_post,$option);
                $path_image = $this->dir_path_post.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value'];
                $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
                $file_name = $this->upload_lib->validate_file_in_path($path_image, $_FILES[ 'file' ][ 'name' ]);
                $uploadPath = $path_image . '/' . $file_name;
                $stt = move_uploaded_file( $tempPath, $uploadPath );
                if($stt){
                    $this->handle_check_folder_is_over_load($this->dir_path_post,$option);
                }
                $answer = array( 'name'=>$file_name,'path'=>$uploadPath );
                //$json = json_encode( $answer );
                $msg = 'File transfer completed';
                $status = true;
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage();
            }
        }
        $response = array_merge(array('status' => $status,'msg' => $msg),$answer) ;
        $this->custom_response($response);
    }*/

    public function upload_img_user_post_old(){
        $msg = '';
        $status = false;
        $answer = array();
        $data_user = $this->get_user_session();
        $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
        $file_is_valid =  $this->sup_check_file_info($tempPath);
        if(!$file_is_valid)
            $msg = 'invalid file';
        if( !empty($_FILES) && $data_user && $file_is_valid ) {
            try {
                $option = $this->handle_get_option_user_folder();
                $this->handle_check_user_folder_tmp('',$option,$data_user['user_id']);
                $child_folder_user = $this->dir_path_user .'/'.$option['current_store_user'].'/'.$data_user['user_id'].'/'.$this->dir_path_user_tmp;
                $file_name = $this->upload_lib->validate_file_in_path($child_folder_user, $_FILES[ 'file' ][ 'name' ]);
                $uploadPath = $child_folder_user . '/' . $file_name;
                move_uploaded_file( $tempPath, FCPATH.$uploadPath );
                $answer = array( 'name'=>$file_name,'path'=>$uploadPath );
                //$json = json_encode( $answer );
                $msg = 'File transfer completed';
                $status = true;
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage();
            }
        }
        $response = array_merge(array('status' => $status,'msg' => $msg),$answer) ;
        $this->custom_response($response);
    }
    public function upload_img_user_post(){
        $msg = '';
        $status = false;
        $answer = array();
        $data_user = $this->get_user_session();
        $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
        $file_is_valid =  $this->sup_check_file_info($tempPath);
        if(!$file_is_valid)
            $msg = 'invalid file';
        if( !empty($_FILES) && $data_user && $file_is_valid ) {
            try {
                $year_month = $this->get_tmp_year_month_path();
                $this->check_and_create_tmp_child($year_month); //make sure folder is available
                $session_id = session_id();
                $tmp_folder = $this->dir_path_tmp."/".$year_month;
                $file_name = $this->upload_lib->validate_file_in_path($tmp_folder, $session_id."_".$_FILES[ 'file' ][ 'name' ]);
                $uploadPath = $tmp_folder . '/' . $file_name;
                move_uploaded_file( $tempPath, FCPATH.$uploadPath );
                $answer = array( 'name'=>$file_name,'path'=>$uploadPath );
                //$json = json_encode( $answer );
                $msg = 'File transfer completed';
                $status = true;
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage();
            }
        }
        $response = array_merge(array('status' => $status,'msg' => $msg),$answer) ;
        $this->custom_response($response);
    }

    public function sup_check_file_info($tmp_name){
        $stt = false;
        $valid_ext = array('jpg','jpeg','png');
        $image_info = getimagesize($tmp_name);
        if($image_info){
            $type = $ext = false;
            list($type,$ext) = explode("/", $image_info['mime']);
            if($type=='image' && in_array($ext, $valid_ext)){
                $stt = true;
            }
        }
        return $stt;
    }

    
}
