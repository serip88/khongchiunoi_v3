<?php

defined('BASEPATH') OR exit('No direct script access allowed');
session_start();
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';
require_once(APPPATH . '/third_party/phpThumb/phpthumb.class.php') ;
define('IMAGE_SMALL_FOLDER', 'small');
define('IMAGE_LARGE_FOLDER', 'large');
define('IMAGE_BIG_FOLDER', 'big');
define('IMAGE_SMALL_SIZE', 320);
define('IMAGE_LARGE_SIZE', 700);
define('IMAGE_BIG_SIZE', 1600);
define('TMP_CHILD', 'Y/m');
define('MEDIA_USER_TYPE', 1);

class Base_controller extends REST_Controller {
    
    protected $_is_login = false;
    protected $dir_path_tmp="app/images/tmp";
    protected $dir_path_user="/app/images/user";
    protected $dir_path_post="/app/images/post";
    protected $dir_path_user_tmp="tmp";
    protected $CI = '';
	function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->CI =& get_instance();
        $this->load->library('user_lib');
        $this->is_login();
        $this->htaccess_emulator();
    }

   
    public function is_login(){
        $data_user = $this->user_lib->get_user_session();
        if($data_user){
            $this->_is_login = true;
        }
        return $this->_is_login;
    }
    private function htaccess_emulator(){
        $actual_link = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $para_url=trim($_SERVER['REQUEST_URI'],'/'); 
        $para_url=explode("?",$para_url);
        $para_url=explode("/api/",$para_url[0]);
        $status = $this->auth_check('api/'.$para_url[1],$this->_is_login);
        if(!$status){
            $this->access_deny();
        }
    }
    private function auth_check($para_url,$is_login){
        $status = false;
        $para_url = str_replace("//", "/", $para_url);
        $arr_para_url = explode("/", $para_url);
        if(count($arr_para_url)==3){ 
            if($arr_para_url[0]=='api'){
                $api = implode("/", array($arr_para_url[1],$arr_para_url[2]));
                $all_api = $this->get_all_api();
                if(isset($all_api[$api]) && $all_api[$api]['api'] == $api){
                    if($is_login && !$all_api[$api]['deny_user']){
                        $status = true; 
                    }elseif (!$all_api[$api]['deny_guest']) { 
                        $status = true;
                    }
                }
                
            } 
        } 
        return $status;
    }
    public function access_deny($extra = [])
    {
        $this->response(array_merge(array(
            'status'  => FALSE,
            'msg' => 'You can not access!'
        ), $extra), REST_Controller::HTTP_BAD_REQUEST);
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
    public function custom_response($arr_reponse){
    	if(isset($arr_reponse['status']) && $arr_reponse['status']){
    		$this->set_response($arr_reponse, REST_Controller::HTTP_OK);
    	}else{ 
    		 $this->set_response($arr_reponse, REST_Controller::HTTP_OK);
    	}
    }

    public function get_list_action(){
        $list_action = array(
            'user/user',
            'user/user_group',
            'user/login',
            'user/logout',
            'user/forgotten',
            'user/reset',         
            'dashboard/index'
        );
        return $list_action;
    }
    public function get_all_api(){
        $api = array(
            'area/edit'             =>['api'=>'area/edit','deny_guest'=>1,'deny_user'=>0],
            'area/save'             =>['api'=>'area/save','deny_guest'=>1,'deny_user'=>0],
            'area/list'             =>['api'=>'area/list','deny_guest'=>1,'deny_user'=>0],
            'area/district_list'    =>['api'=>'area/district_list','deny_guest'=>1,'deny_user'=>0],
            'area/delete'           =>['api'=>'area/delete','deny_guest'=>1,'deny_user'=>0],
            'main/base_info'        =>['api'=>'main/base_info','deny_guest'=>0,'deny_user'=>0],

            'user/user_save'        =>['api'=>'user/user_save','deny_guest'=>1,'deny_user'=>0],
            'user/user_ss'          =>['api'=>'user/user_ss','deny_guest'=>0,'deny_user'=>0],
            
            'login/login'           =>['api'=>'login/login','deny_guest'=>0,'deny_user'=>1],
            'login/logout'          =>['api'=>'login/logout','deny_guest'=>1,'deny_user'=>0],

            'user/member_list'      =>['api'=>'user/member_list','deny_guest'=>1,'deny_user'=>0],
            'user/member_group'     =>['api'=>'user/member_group','deny_guest'=>1,'deny_user'=>0],

            'user/user_list'        =>['api'=>'user/user_list','deny_guest'=>1,'deny_user'=>0],
            'user/admin_group'       =>['api'=>'user/admin_group','deny_guest'=>1,'deny_user'=>0],
            'user/user_detail'      =>['api'=>'user/user_detail','deny_guest'=>1,'deny_user'=>0],
            'user/user_edit'        =>['api'=>'user/user_edit','deny_guest'=>1,'deny_user'=>0],
            'user/user_delete'      =>['api'=>'user/user_delete','deny_guest'=>1,'deny_user'=>0],
            'user_group/permissions'=>['api'=>'user_group/permissions','deny_guest'=>1,'deny_user'=>0],
            'user_group/save'       =>['api'=>'user_group/save','deny_guest'=>1,'deny_user'=>0],
            'user_group/detail'     =>['api'=>'user_group/detail','deny_guest'=>1,'deny_user'=>0],
            'user_group/edit'       =>['api'=>'user_group/edit','deny_guest'=>1,'deny_user'=>0],
            'user_group/delete'     =>['api'=>'user_group/delete','deny_guest'=>1,'deny_user'=>0],
            'category/save'         =>['api'=>'category/save','deny_guest'=>1,'deny_user'=>0],
            'category/edit'         =>['api'=>'category/edit','deny_guest'=>1,'deny_user'=>0],
            'category/category_list'=>['api'=>'category/category_list','deny_guest'=>1,'deny_user'=>0],
            'category/delete'       =>['api'=>'category/delete','deny_guest'=>1,'deny_user'=>0],

            'product/save'          =>['api'=>'product/save','deny_guest'=>1,'deny_user'=>0],
            'product/edit'          =>['api'=>'product/edit','deny_guest'=>1,'deny_user'=>0],
            'product/delete'        =>['api'=>'product/delete','deny_guest'=>1,'deny_user'=>0],
            'product/product_list'  =>['api'=>'product/product_list','deny_guest'=>1,'deny_user'=>0],

            'upload/upload_img_user'=>['api'=>'upload/upload_img_user','deny_guest'=>1,'deny_user'=>0],

            'user/test'             =>['api'=>'user/test','deny_guest'=>1,'deny_user'=>0],

        );
        return $api;
    }

    public function array_matches($a,$b)
    {
        if ($a===$b){
            return 0;
        }
        return ($a>$b)?1:-1;
    }
    public function get_user_session(){  
      if(isset($_SESSION['user_data']) && $_SESSION['user_data']){
        return $_SESSION['user_data'];
      }else{
        return '';
      }   
    }

    

    public function get_option_key($option_key){
        $this->load->model(array('option/option_model'));
        $select = array();
        $where = array('where_in'=>array('key'=>'name','value'=>array()));
        $where['where_in']['value']=$option_key;
        $filter = array('type'=>'rows','limit'=>0);
        $options = $this->option_model->get_dt($select,$where,$filter);    
        $option_format = array();
        if(count($option_key) == count($options) && $options){
            foreach ($options as $key => $value) {
                $option_format[$value['name']]=$value['value'];
            }
        }
        return $option_format;
    }
    public function init_user_category($user_id,$option){
        $this->load->library('model_option_user_lib','option_lib');
        $msg = '';
        $stt = true;
        $stt_user_group = false;
        $stt_current_store = false;
        try {
            //check if folder dir_path_user is empty set default current_store_user (this is the fist time when init new website)
            $num_file = $this->count_files_in_folder($this->dir_path_user);
            if($num_file==0){
                $option['current_store_user'] =1;
                $this->update_option_folder(1,$option);
            }
            //check current_store_user is over max_in_a_group (update current_store_user, max_in_a_group)
            list($option,$stt_option)  = $this->check_update_current_store($num_file,$option,'all');
            if($stt_option)
                $this->update_option_folder($stt_option,$option);
            //check current_store_user is available if not create
            if (!file_exists(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'])) {
                $stt_current_store = mkdir(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'], 0777);
                
            }else{
                $stt_current_store = true;
            }
            // check user_category if not create by id
            if ($stt_current_store && !file_exists(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'].'/'.$user_id)) {
                $num_file_current_folder = $this->count_files_in_folder($this->dir_path_user.'/'.$option['current_store_user']);
                list($option,$stt_option_folder)  = $this->check_update_current_store($num_file_current_folder,$option,'all');
                if($stt_option_folder)
                    $this->update_option_folder($stt_option_folder,$option);
                $stt_user_group = mkdir(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'].'/'.$user_id, 0777);
                
            }else{
                $stt_user_group = true;
            }
            //check if user have no option insert it
            if($stt_user_group){
                $have_option = $this->check_option_user($user_id);
                $stt_current_folder_of_user = false;
                if(!$have_option){
                    $stt_current_folder_of_user = mkdir(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'].'/'.$user_id.'/1', 0777);
                    $stt_current_folder_of_user = mkdir(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'].'/'.$user_id.'/'.$this->dir_path_user_tmp, 0777);
                }
                if($stt_current_folder_of_user){
                    $this->CI->db->trans_start();
                    $this->model_option_user_lib->insert_option_user($user_id,$option['current_store_user']);
                    $this->CI->db->trans_complete();
                    $stt_option_user = $this->db->trans_status();
                    if(!$stt_option_user){
                        throw new Exception("Have problem when insert_option_user id: $user_id");
                    }
                }
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $stt = false;
        }
        return array('stt'=>$stt,'msg'=>$msg);
    }
    //current_store_user, max_in_a_group
    function update_option_folder($stt,$option){
        $this->load->library('option_lib');
        if($stt){
            $data = array('value'=>$option['current_store_user']);
            $this->option_lib->update_option_folder('current_store_user',$data);
            if($stt==2){
                $data = array('value'=>$option['max_in_a_group']);
                $this->option_lib->update_option_folder('max_in_a_group',$data);
            }
        }
    }
    //$return = option|stt|all
    function check_update_current_store($num_file,$option,$return){
        $stt = 0;
        if($num_file>=$option['max_in_a_group']){
            $option['current_store_user'] = intval($option['current_store_user']) + 1;
            $stt=1;
            if($option['current_store_user'] > $option['max_group']){
                $option['current_store_user'] = 1;
                $option['max_in_a_group']=intval($option['max_in_a_group'])+intval($option['group_full_add_more']);
                $stt=2;
            }
            if (!file_exists(FCPATH.$this->dir_path_user.'/'.$option['current_store_user']))
                mkdir(FCPATH.$this->dir_path_user.'/'.$option['current_store_user'], 0777);
        }
        if($return == 'stt'){
            return $stt;
        }elseif ($return == 'option') {
            return $option;
        }else{
            //plz not change order of parameters
            return array($option,$stt) ;
        }
    }
    //check user have option folder?
    function check_option_user($user_id){
        $this->load->model(array('user/Option_User_Model'));
        $select = array();
        $where = array('where'=> array());
        $where['where'] = array('user_id'=>$user_id);
        $limit = array('type'=>'int','limit'=>1);
        $have_option = $this->Option_User_Model->get_dt($select,$where,$limit);    
        return $have_option;
    }
    //count files in group current
    function count_files_in_folder($path){
        $fi = new FilesystemIterator(FCPATH.$path, FilesystemIterator::SKIP_DOTS);
        return iterator_count($fi);
    }
    //bỏ
    public function check_create_user_detail_cate($user_id){
        $option_key = array('max_in_a_group','max_group','current_group','group_full_add_more');
        $option = $this->get_option_key($option_key);

    }
    public function handle_get_option_post_folder(){
        $option_key = array('max_in_a_group','max_group','group_full_add_more','current_store_post','current_group_post','current_child_post');
        $option = $this->get_option_key($option_key);
        if($option){
            foreach ($option_key as $order => $key) {
                if (!isset($option[$key])) {
                    return array();
                }
            }
            $option['store_key'] = 'current_store_post';
            $option['store_value'] = $option['current_store_post'];
            $option['group_key'] = 'current_group_post';
            $option['group_value'] = $option['current_group_post'];
            $option['child_key'] = 'current_child_post';
            $option['child_value'] = $option['current_child_post'];
            return $option;
        }else{
            return array();
        }
    }
    public function handle_get_option_user_folder(){
        $option_key = array('max_in_a_group','max_group','group_full_add_more','current_store_user');
        $option = $this->get_option_key($option_key);
        if($option){
            foreach ($option_key as $order => $key) {
                if (!isset($option[$key])) {
                    return array();
                }
            }
            $option['store_key'] = 'current_store_user';
            $option['store_value'] = $option['current_store_user'];
            return $option;
        }else{
            return array();
        }
    }
    //$option = array(store_value=>1,store_key=>'',group_value=>1,group_key=>'',child_value=>1,child_key=>'');
    //sử dụng mắc định thông số trong db là luôn đúng, nếu init theo tham số truyền vào, ko init theo số lượng trên thư mục
    // chỉ sử dụng hàm count file là đếm trên server thôi
    //=>before add image
    public function handle_check_option_folder_is_created($root_path,$option){
        $root_path = $root_path ? $root_path : $this->dir_path_post;
        $this->check_and_create_folder($root_path,$option['store_value']);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'],$option['group_value']);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'].'/'.$option['group_value'],$option['child_value']);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value'],IMAGE_SMALL_FOLDER);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value'],IMAGE_LARGE_FOLDER);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value'],IMAGE_BIG_FOLDER);
    }
    //$option = array(store_value=>1,store_key=>'',group_value=>1,group_key=>'',child_value=>1,child_key=>'',max_group_value=>'',max_group_key=>'');
    //after add image
    public function handle_check_folder_is_over_load($root_path,$option){
        //check child
        $num_file = $this->count_files_in_folder($root_path.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value']);
        if($num_file>=$option['max_group']){
            $option['child_value'] = $option['child_value'] +1;
            mkdir(FCPATH.$root_path.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value'], 0777);
            $this->update_option($option['child_key'],$option['child_value']);
            return $this->handle_check_folder_is_over_load($root_path,$option);

            //check group
            $num_file = $this->count_files_in_folder($root_path.'/'.$option['store_value'].'/'.$option['group_value']);
            if($num_file>=$option['max_group']){
                $option['group_value'] = $option['group_value'] +1;
                $option['child_value'] = 1;
                mkdir(FCPATH.$root_path.'/'.$option['store_value'].'/'.$option['group_value'], 0777);
                $this->update_option($option['group_key'],$option['group_value']);
                $this->update_option($option['child_key'],$option['child_value']);
                return $this->handle_check_folder_is_over_load($root_path,$option);

                //check store
                $num_file = $this->count_files_in_folder($root_path.'/'.$option['store_value']);
                if($num_file>=$option['max_group']){
                    $option['store_value'] = $option['store_value'] +1;
                    $option['group_value'] = 1;
                    $option['child_value'] = 1;
                    mkdir(FCPATH.$root_path.'/'.$option['store_value'], 0777);
                    $this->update_option($option['store_key'],$option['store_value']);
                    $this->update_option($option['group_key'],$option['group_value']);
                    $this->update_option($option['child_key'],$option['child_value']);
                }
            }
        }
        return $option;
    }
    //return 1 if folder created before
    public function check_and_create_folder($path,$foldername){
        $stt = 0;
        if (!file_exists(FCPATH.$path."/".$foldername)) {
            mkdir(FCPATH.$path."/".$foldername, 0777);
        }else{
            $stt = 1;
        }
        return $stt;
    }
    public function check_and_create_multi_folder($root_path,$child_path){
        $child_path = trim($child_path,"/");
        $child_folder = explode("/", $child_path);
        if (!file_exists(FCPATH.$root_path."/".$child_folder[0])) {
            mkdir(FCPATH.$root_path."/".$child_folder[0], 0777);
        }
        if(count($child_folder)>1){
            $child_path = implode("/", array_slice($child_folder,1));
            return $this->check_and_create_multi_folder($root_path."/".$child_folder[0],$child_path);
        }else{
            return true;
        }
    }
    public function check_and_create_tmp_child($child_path){
        if($child_path){
            $root_path = $this->dir_path_tmp;
            if (!file_exists(FCPATH.$root_path."/".$child_path)) {
                return $this->check_and_create_multi_folder($root_path,$child_path);
            }
            return true;
        }else{
            return false;
        }
            
    }
    public function get_tmp_year_month_path(){
        return  date(TMP_CHILD, time());
    }
    public function handle_check_user_folder_tmp($root_path,$option,$user_id){
        $root_path = $root_path ? $root_path : $this->dir_path_user;
        $this->check_and_create_folder($root_path,$option['store_value']);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'],$user_id);
        $this->check_and_create_folder($root_path.'/'.$option['store_value'].'/'.$user_id,$this->dir_path_user_tmp);
    }
    public function handle_check_user_folder_is_created($root_path,$option,$user_id){
        $root_path = $root_path ? $root_path : $this->dir_path_user;
        $this->check_and_create_folder($root_path,$option['group_folder']);
        $this->check_and_create_folder($root_path.'/'.$option['group_folder'],$user_id);
        $this->check_and_create_folder($root_path.'/'.$option['group_folder'].'/'.$user_id,$option['group_current']);
        $this->check_and_create_folder($root_path.'/'.$option['group_folder'].'/'.$user_id.'/'.$option['group_current'],IMAGE_SMALL_FOLDER);
        $this->check_and_create_folder($root_path.'/'.$option['group_folder'].'/'.$user_id.'/'.$option['group_current'],IMAGE_LARGE_FOLDER);
        $this->check_and_create_folder($root_path.'/'.$option['group_folder'].'/'.$user_id.'/'.$option['group_current'],IMAGE_BIG_FOLDER);
    }
    public function get_dir_path_user_tmp(){
        $data_user = $this->get_user_session();
        $option = $this->handle_get_option_user_folder();
        $folder_user_tmp = $this->dir_path_user .'/'.$option['current_store_user'].'/'.$data_user['user_id'].'/'.$this->dir_path_user_tmp;
        return $folder_user_tmp;
    }
    public function remove_all_files_in_folder($path){
        $files = glob(FCPATH.$path.'/*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }
    }

    public function remove_files_in_folder_by_prefix($path,$prefix){
        $files = glob(FCPATH.$path."/$prefix*"); // get all file with $prefix
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }
    }
    public function unlink_media($media_path,$folders,$media_name){
        if(is_array($folders)){
            foreach ($folders as $key => $folder) {
                @unlink(FCPATH.$media_path."/".$folder."/".$media_name);
            }
        }else{
            @unlink(FCPATH.$media_path."/".$folders."/".$media_name);
        }
    }

    public function check_file_exit($file){
        $requite = array('name','path');
        foreach ($requite as $key => $value) {
          if(! isset($file[$value])){
            return 0;
          }
        }
        if(file_exists(FCPATH.$file['path'])){
          return 1;
        }else{
          return 0;
        }
    }

    public function get_path_folder($full_path,$start=0,$end=-1){
        $full_path = trim($full_path,"/");
        $full_path = explode("/", $full_path);
        $full_path = array_slice($full_path, $start,$end);
        return implode("/", $full_path);
    }
    //return file to new location
    public function move_file_to_post_folder($file_name, $file_path){
        $this->load->library('upload_lib');
        $option = $this->handle_get_option_post_folder();
        //Importance check folder before upload
        $this->handle_check_option_folder_is_created($this->dir_path_post,$option);
        $path_image = $this->dir_path_post.'/'.$option['store_value'].'/'.$option['group_value'].'/'.$option['child_value'];
        $file_name = $this->upload_lib->validate_file_in_path($path_image.'/'.IMAGE_BIG_FOLDER, $file_name);
        //$uploadPath = $path_image . '/' . $file_name;
        $uploadPath = $path_image .'/'.IMAGE_BIG_FOLDER . '/' . $file_name;
        //move file to new location
        $stt = rename( FCPATH.$file_path, FCPATH.$uploadPath );
        if($stt){
            try {
                $this->sup_gen_thumb($uploadPath,IMAGE_BIG_SIZE,IMAGE_BIG_FOLDER,$path_image,$file_name);
                $this->sup_gen_thumb($uploadPath,IMAGE_LARGE_SIZE,IMAGE_LARGE_FOLDER,$path_image,$file_name);
                $this->sup_gen_thumb($uploadPath,IMAGE_SMALL_SIZE,IMAGE_SMALL_FOLDER,$path_image,$file_name);
                //Importance check full folder before upload
                $this->handle_check_folder_is_over_load($this->dir_path_post,$option);
                return array( 'name'=>$file_name,'path'=>$uploadPath );
            } catch (Exception $e) {
                return false;
            }
        }else{
            return false;
        }
    }
    public function sup_gen_thumb($source_path,$size,$folder,$path_image,$file_name){
        $stt = false;
        $phpThumb = new phpThumb();
        $phpThumb->setSourceFilename(FCPATH.$source_path);
        $phpThumb->setParameter('w', $size);
        if($phpThumb->GenerateThumbnail()){
            if($phpThumb->RenderToFile(FCPATH.$path_image .'/'.$folder . '/' . $file_name)){
                $stt = true;
            }
        }
        return $stt;
    }
    public function sup_get_media_path_name($media_full_path){
        $media_path = $this->get_path_folder($media_full_path,0,-2);
        $tmp_arr = explode("/", $media_full_path);
        $media_name = end($tmp_arr);
        return array($media_path,$media_name);
    }
}