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

    private $private_param = FALSE ;
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
        if($this->private_param){
            $param = $this->private_param;
        }else{
            $param = $this->post();
        }
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
        $param = $this->get();
    	$data = $this->email_lib->get_list($param);
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
    public function import_post(){
        $params = $this->post();
        $stt = false;
        $file = $params['file'];
        $pathinfo = pathinfo($file['path']);
        $data=$this->read_file_xls($file);
        $header = array();
        $body = array();
        if($data){
            $headers = $this->get_header($data[0]);
            $body = array_slice($data,1);
            $this->add_multiple_email($body);
            $stt = true;
        }
        $response = array('status' => $stt, 'msg'=> '');
        return $response;
    }
    public function add_multiple_email($data){
        $new_data = array();
        if($data){
            foreach ($data as $key => $value) {
                if($value[0] && $value[1]){
                    $param = array();
                    $param['email'] = $value[0];
                    $param['password'] = $value[1];
                    $param['mode'] = 'add';
                    $param['status'] = 1;
                    $this->private_param = $param;
                    $this->save_post();
                    $new_data[] = $param;
                }
            }
        }
        return $new_data;
    }
    private function get_header($data){
        $header_formated = array();
        $required = array('email','passwrod');
        foreach ($data as $key => $value) {
            $_key = strtolower($value);
            if(in_array($_key,$required)){
                $header_formated[] = $value;
            }
        }
        return $header_formated;
    }
    private function read_file_xls($file){
        $rows = array();
        if ($file['name']){
            $mimes = array('text/csv', 'text/plain', 'application/csv',
                'text/comma-separated-values', 'application/excel',
                'application/vnd.ms-excel', 'application/vnd.msexcel',
                'text/anytext', 'application/octet-stream', 'application/txt');

            //if (in_array($file['type'], $mimes))
            if ($file['name']){
                $sheets = $this->_read_xls_data($file['path'], 0);
                if ( ! empty($sheets)){
                    foreach ($sheets as $key => $data){
                        foreach ($data as $item){
                            $row = array();
                            $itemvalue = array_values($item);
                            $count = count($itemvalue);
                            for ($i = 0; $i < $count; $i++){
                                if ($itemvalue[$i] == 'null'){
                                    $itemvalue[$i] = "";
                                }
                                $row[] = $itemvalue[$i];
                            }
                            $rows[] = $row;
                        }
                    }
                }
            }
        }
        return $rows;
    }
    private function _read_xls_data($file, $key_row_index = 0){
        require_once APPPATH."/third_party/PHPExcel.php";
        //read file from path
        $objPHPExcel = PHPExcel_IOFactory::load(FCPATH.$file);

        /*
        //get only the Cell Collection
        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

        //extract to a PHP readable array format
        foreach ($cell_collection as $cell)
        {
            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
            $arr_data[$row][$column] = $data_value;
        }
        */
        $iterator = $objPHPExcel->getWorksheetIterator();
        $sheets = array();
        while ($iterator->valid()){
            $objWorksheet = $iterator->current();
            $sheet_title = $objWorksheet->getTitle();

            $sheet = array ();
            foreach ($objWorksheet->getRowIterator() as $row){
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $row_index = $row->getRowIndex();

                // 배열의 key로 사용할 제목행 앞의 행은 무시한다.
                if ($row_index < $key_row_index){
                    continue;
                }

                // $key_row_index에 설정된 줄의 값을 불러와서 배열의 key로 사용한다.
                if ($row_index == $key_row_index){
                    $column_title_arr = array ();
                    foreach ($cellIterator as $cell){
                        $column_index = $cell->getColumn();
                        $column_title_arr[$column_index] = str_replace("\n", '', trim($cell->getCalculatedValue()));
                    }
                    continue;
                }

                foreach ($cellIterator as $cell) {
                    $column_index = $cell->getColumn();
                    if ($key_row_index > 0){
                        $column_title = $column_title_arr[$column_index];
                        if ($column_title == ''){
                            continue;
                        }
                        $sheet[$row_index][$column_title] = $cell->getCalculatedValue();
                    }
                    else{
                        $sheet[$row_index][$column_index] = $cell->getCalculatedValue();
                        if($row_index > 1 && $column_index == 'F' && $sheet[$row_index][$column_index] == null){
                            $sheet[$row_index][$column_index] = sprintf("%s_%s_",$column_index,$row_index);
                        }
                    }
                }
            }
            $sheets[$sheet_title] = $sheet;

            $iterator->next();
        }

        foreach ($sheets as $sheet_title => $sheet){
            foreach ($sheet as $key => $data){
                $empty_arr = TRUE;
                foreach ($data as $value){
                    if (!empty($value)){
                        $empty_arr = FALSE;
                    }
                }
                if ($empty_arr){
                    unset($sheets[$sheet_title][$key]);
                }
            }
        }
        return $sheets;
    }
}