<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Starter_Model extends CI_Model
{
  protected $_tb_name = '';
  public function __construct()
  {
      parent::__construct(); 
      $this->load->database();
  }

  public function insert_data($data){ 
   	$this->db->insert($this->_tb_name,$data);
   	return $this->db->insert_id();
 }
 
 public function get_data($select,$where,$limit="",$nStart=0,$order_by=""){
      $result = array();
      $this->db->select($select);
      $this->db->from($this->_tb_name);
      $this->db->where($where);
      if($limit)
        $this->db->limit($limit, $nStart);
      if($order_by)
        $this->db->order_by($order_by);
      $query = $this->db->get();
      if ($query && $query->num_rows() > 0) {
        $result = $query->result_array();
        $query->free_result();
      }
      return $result;
  }
  //tb_join array('table_name'=>'ec_signup as B','condition'=>'cn=0 ,id=10', 'type'=>'left');
  public function get_data_join($select,$where,$tb_join = array() ,$limit="",$nStart=0,$order_by=""){
      $result = array();
      $this->db->select($select);
      $this->db->from($this->_tb_name . " as A");
      foreach ($tb_join as $key => $value) {
        $this->db->join($value['table_name'], $value['condition'], $value['type']);
      }
      $this->db->where($where);
      if($limit)
        $this->db->limit($limit, $nStart);
      if($order_by)
        $this->db->order_by($order_by);
      $query = $this->db->get();
      if ($query && $query->num_rows() > 0) {
        $result = $query->result_array();
        $query->free_result();
      }
      return $result;
  }
  public function update_data($data = array(), $where) {
      if (count($data)) {
        foreach ($data as $key => $value) {
          $this->db->set($key, $value);
        }
      }
      $this->db->where($where);
      return $this->db->update($this->_tb_name);
  }
  public function get_total($where)
  {
    if(is_array($where))
      $this->db->where($where);
    else
      $this->db->where($where, NULL, FALSE);
      return $this->db->count_all_results($this->_tb_name);  
  }
  public function delete_data($where) {
      $this->db->where($where);
      $this->db->delete($this->_tb_name);
      $affected_rows = $this->db->affected_rows();
      if ($affected_rows > 0) {
          return true;
      } else {
          return false;
      }
  }
  //$select = array('select'=>'id,name,price','table_join'=>array()) ;
  //$where = array('where'=>array(),'or_where'=>array(),'where_in'=>array() or string);
  //$filter = array('limit'=>10,'start'=>0,'order_by'=>'id ASC','type'=>'int/rows');
  public function get_dt($select,$where,$filter){
      $result = array();
      if(isset($select['select']) && $select['select']){
        $this->db->select($select['select']);
      }
      if(isset($select['table_join']) && is_array($select['table_join'])){
        $this->db->from($this->_tb_name . " as A");
        foreach ($select['table_join'] as $key => $value) {
          $this->db->join($value['table_name'], $value['condition'], $value['type']);
        }
      }else{
        $this->db->from($this->_tb_name);
      }
      if(isset($where['where']) && $where['where']){
        if(is_array($where['where'])){
          $this->db->where($where['where']);   
        }else{
          $this->db->where($where['where'],NULL,FALSE);   
        }
      }
      if(isset($where['or_where']) && $where['or_where']){
        if(is_array($where['or_where'])){
          $this->db->or_where($where['or_where']);   
        }else{
          $this->db->or_where($where['or_where'],NULL,FALSE);   
        }
      }
      if(isset($where['where_in']) && $where['where_in']){
        $this->db->where_in($where['where_in']['key'],$where['where_in']['value']);
      }
      if(isset($filter['limit']) && $filter['limit']){
        if(isset($filter['start'])){
          $filter['start'] = intval($filter['start']);
        }else{
          $filter['start'] = 0;
        }
        $this->db->limit($filter['limit'], $filter['start']);
      }  
      if(isset($filter['order_by']) && $filter['order_by'])
        $this->db->order_by($filter['order_by']);
      if(isset($filter['type']) && $filter['type']=='int'){
        $result= $this->db->count_all_results();
      }else{
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
          $result = $query->result_array();
          $query->free_result();
        }else{
          $result = 0;
        }
      }
      return $result;
  }
  
  public function get_slug($title,$type='add'){
    $this->load->library('common_lib');
    $common_lib= new common_lib();
    $slug= $common_lib->name_on_bar($title);
    $select="*";
    $where = array('slug'=>$slug);
    $data = $this->user_group_model->get_data($select,$where,1);
    if($type=='add'){
      if(count($data)>0){
        $slug=$slug.'-'.rand(0,100);
        return $this->get_slug($slug);
      }  
    }else{
      if(count($data)>1){
        $slug=$slug.'-'.rand(0,100);
        return $this->get_slug($slug);
      } 
    }
    return $slug;
  }
  protected function check_admin($uid){
    
  }
    
}

