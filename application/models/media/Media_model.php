<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/models/Starter_model.php';

class Media_Model extends Starter_Model{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->_tb_name="rz_media";
    }
    
    public function get_media_by_parent($parent_id,$type){
    	$select = array('media_id','image_path','image_name');
    	$where = array('parent_id'=>$parent_id,'type'=>$type);
    	$media_data = $this->get_data($select,$where);
    	return $media_data;
    }
    public function delete_media($media_id){
    	$where = array('media_id'=>$media_id);
    	return $this->delete_data($where);
    }

}
