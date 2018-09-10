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
 * @product         Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Product extends Base_controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library(array(
            config_item('app_path').'/'.'product_lib'
        ));
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
        
    }
    public function save_post(){
        $param = $this->post();
        $upload_image = false;
        $stt=FALSE;
        $msg='';
        $param['price'] = isset($param['price_pure'])? $param['price_pure'] : 0;
        $param = $this->product_lib->validate_save_product($param);
        if($param){
            $param = $this->product_lib->handle_save_product($param);
            $id = $stt = $this->product_lib->save_product($param);
            if($stt){
                //B upload file
                if(isset($param['file']) ){
                    $file_exit = $this->product_lib->check_file_exit($param['file']);
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
                    $data = array();
                    $data['image_name']= $param['new_file']['name'];
                    $data['image_path']= $param['new_file']['path'];
                    $where = array("product_id"=> $id);
                    $stt = $this->product_model->update_data($data,$where); 
                    if(!$stt){
                        $msg = 'Error! Save image have problem.';
                    }
                }
            }else{
                $msg = 'Error! Cannot save product.';
            }
        }
        $response = array('status' => $stt?true:false,'msg'=> $msg);
        $this->custom_response($response);
    }
    public function product_list_get(){
        $param = $this->get();
        //B handle traffic
        $ip = $this->input->ip_address();
        $param['ip'] = $ip;
        $this->product_lib->handle_traffic($param);
        //E handle traffic
        $data = array();
        $pagination = array();
        $res = $this->product_lib->get_product_list($param);
        if($res){
            $stt=TRUE;
            $data = $res['data'];
            $pagination = $res['pagination'];
        }else {
            $stt=FALSE;
        }

        $this->set_response([
            'status' => $stt,
            'rows' => $data,
            'pagination'=> $pagination
        ], REST_Controller::HTTP_OK);
    }
    public function edit_post(){
        $upload_image = false;
        $stt=FALSE;
        $msg='';
        $param = $this->post();
        $param['price'] = isset($param['price_pure'])? $param['price_pure'] : 0;
        $param = $this->product_lib->validate_edit_product($param);
        if($param){
            $data = $this->product_model->get_product($param['product_id']);
            $param = $this->product_lib->handle_save_product($param);
            $stt = $this->product_lib->edit_product($param);
            if($stt && $data){
                //B upload file
                if(isset($param['file']) ){
                    $file_exit = $this->check_file_exit($param['file']);
                    if($file_exit){
                        $param['new_file'] = $this->move_file_to_post_folder($param['file']['name'],$param['file']['path']);
                        if($param['new_file']){
                            //remove old image if it have
                            if($param['image_path']){
                                $param['image_path'] = strpos($param['image_path'], ".") == 0 ? $param['image_path'] : ".".$param['image_path'];
                                @unlink(FCPATH.$data['image_path']);
                                @unlink( str_replace(IMAGE_BIG_FOLDER, IMAGE_LARGE_FOLDER, FCPATH.$data['image_path']));
                                @unlink( str_replace(IMAGE_BIG_FOLDER, IMAGE_SMALL_FOLDER, FCPATH.$data['image_path']));
                            }
                            $upload_image = true;
                            $tmp_folder = $this->get_path_folder($param['file']['path']);
                            $this->remove_files_in_folder_by_prefix($tmp_folder,session_id());
                        }
                    }
                }
                //E upload file
                if($upload_image){
                    $data = array();
                    $data['image_name']= $param['new_file']['name'];
                    $data['image_path']= $param['new_file']['path'];
                    $where = array("product_id"=> $param['product_id']);
                    $stt = $this->product_model->update_data($data,$where); 
                    if(!$stt){
                        $msg = 'Error! Save image have problem.';
                    }
                }
            }else{
                $msg = 'Error! Cannot save product.';
            }
        }
        $response = array('status' => $stt,'msg'=> $msg);
        $this->custom_response($response);
    }

    public function delete_post(){
        $params = $this->post();
        $product_ids = isset($params['product_delete']) && $params['product_delete']?$params['product_delete']:array();
        $msg = '';
        $status = false;
        $count_false = 0;
        if(count($product_ids)){
            foreach ($product_ids as $key => $id) {
                try {
                    $data = $this->product_model->get_product($id);
                    $stt = $this->product_lib->product_delete($id);
                    if($stt){
                        @unlink(FCPATH.$data['image_path']);
                        @unlink( str_replace(IMAGE_BIG_FOLDER, IMAGE_LARGE_FOLDER, FCPATH.$data['image_path']));
                        @unlink( str_replace(IMAGE_BIG_FOLDER, IMAGE_SMALL_FOLDER, FCPATH.$data['image_path']));

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
            $msg = "product cannot delete";
        }
        $response = array('status' => $status,'msg' => $msg);
        $this->custom_response($response);
    }
    public function invoice_post(){
        $params = $this->post();
        $param = $params['cart'];
        $checkout = $params['checkout'];
        $stt = FALSE;
        $msg ='';
        $amount = $this->product_lib->validate_save_product_invoice($param);
        if($amount){
            $id = $stt = $this->product_lib->save_product_invoice($amount);
            if($id){
                $this->product_lib->email_paid_update($checkout['to_email'], $amount);
                $checkout['invoice_id'] = $id;
                $this->handle_invoice_info($checkout);
                $this->handle_invoice_detail($id,$param);
            }else{
                $msg = 'Error! Cannot save invoice.';
            }
        }else{
            $msg = 'Error: Please check quantity'; 
        }
        $response = array('status' => $stt,'msg'=> $msg);
        $this->custom_response($response);
    }
    public function handle_invoice_info($params){
        if($params){
            $params = $this->product_lib->validate_save_invoice_info($params);
            if($params){
                $stt = $this->product_lib->save_invoice_info($params);
            }else{
                $msg = 'Error! Wrong data.';
            }
        }else{
            return 0;
        }
    }
    public function handle_invoice_detail($invoice_id,$products){
        if($products){
            foreach ($products as $key => $param) {
                $param['invoice_id'] = $invoice_id;
                $param = $this->product_lib->validate_save_invoice_detail($param);
                if($param){
                    $stt = $this->product_lib->save_invoice_detail($param);
                }else{
                    $msg = 'Error! Wrong data.';
                }
            }
        }else{
            return 0;
        }
    }
    public function invoice_list_get(){
        $params = $this->get();
        $stt=FALSE;
        $res = $this->product_lib->get_invoice_list($params);
        $data = array();
        $total = 0;
        if($res){
            $data = $res['data'];
            $total = $res['total'];
            $stt=TRUE;
        }
        $this->set_response([
            'status' => $stt,
            'rows' => $data,
            'total'=> $total
        ], REST_Controller::HTTP_OK);
    }
    public function invoice_delete_post(){
        $params = $this->post();
        $invoice_ids = isset($params['invoice_delete']) && $params['invoice_delete']?$params['invoice_delete']:array();
        $msg = '';
        $status = false;
        $count_false = 0;
        if(count($invoice_ids)){
            foreach ($invoice_ids as $key => $id) {
                try {
                    //$data = $this->product_invoice_model->get_product($id);
                    $stt = $this->product_lib->invoice_delete($id);
                    if(!$stt){
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
            $msg = "invoice cannot delete";
        }
        $response = array('status' => $status,'msg' => $msg);
        $this->custom_response($response);
    }
}