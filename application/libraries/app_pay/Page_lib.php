<?php
/*
  +------------------------------------------------------------------------+
  | Copyright (C) 2016 Toigiaitri.                                        |
  |                                                                        |
  | This program is free software; you can redistribute it and/or          |
  | modify it under the terms of the Toigiaitri  License                      |
  |                                                                        |
  +------------------------------------------------------------------------+
  | o Developer : Rain                                                     |
  | o HomePage  : http://www.toigiaitri.net/                               |
  | o Email     : serip88@gmail.com                                        |
  +------------------------------------------------------------------------+
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Page_lib {
	function __construct() {
        
        $this->CI =& get_instance();
        $this->CI->load->database('default');
        $this->CI->load->model(array(
            'post/post_model',
            ));
        $this->_config =  $this->CI->config;
    }
    
    function get_main_menu(){
		$language='vn';$tail='.html';$head='';
		$title=isset($_GET['slug'])?$_GET['slug']:'';

		$where = array('post_type'=>'menu_top', 'enabled'=>1);
		$dt = $this->CI->post_model->get_data('id_post,options',$where,'',0,'order DESC');
		$strIdPage=$this->menu_filter_id_page($dt);
		$data_category=$this->CI->post_model->get_data('title,id_post,options,slug',"id_post IN($strIdPage) AND enabled=1",'',0,"FIELD(id_post, $strIdPage)");
		$count_cate= count($data_category);
		$main_menu = array();
		for($i=0; $i<$count_cate; $i++){
			$options= json_decode($dt[$i]['options']);
			//$total_child= get_total_table($tableName,"WHERE parent_id=".$data_category[$i]['id_post']." AND enabled=1");
			$data_child=$this->CI->post_model->get_data('title,id_post,options,slug',"parent_id=".$options->id_page." AND post_type='page_default' AND enabled=1",'',0 ,"order DESC");
			$total_child= count($data_child);
			if($data_category[$i]['slug']=='san-pham' && 0){ 
				$parent_id = get_id_slug('san-pham','product');
				
				$data_child= get_post_type_list('product',20,0,'',"WHERE post_type='product' AND level=1 AND parent_id=$parent_id AND enabled=1 ORDER BY `order` DESC LIMIT 20",'','no');
				$total_child= count($data_child);
				
			}
			if($total_child){
				$href= '/'.$data_category[$i]['slug'].'/'.$data_child[0]['slug'].$tail;
				$display = '';
				$arrowdow='<span class="caret"></span>';
				$class_list= 'dropdown';
			}else{
				$data_category[$i]['slug']!=''?$href='/'.$data_category[$i]['slug'].$tail:$href='/';
				$display = 'style="display:none;opacity: 0;"';
				$arrowdow='';
				$class_list= '';
			}

			if($title==$data_category[$i]['slug']){
				$active=IS_ACTIVE;
			}else
				$active='';
			
			$sub_menu= array();
			for($j=0; $j<$total_child; $j++){
				$sub_menu[]= array(
					'ID'			=> $data_child[$j]['id_post'],
					'TITLE'			=> $data_child[$j]['title'],
					'HREF'			=> $head.'/'.$data_category[$i]['slug'].'/'.$data_child[$j]['slug'].$tail,
				);
			}
			$main_menu[]= array(
					'ID'			=> $data_category[$i]['id_post'],
					'TITLE'			=> $data_category[$i]['title'],
					'HREF'			=> $head.$href,
					'DISPLAY'		=> $display,
					'ACTIVE'		=> $active,
					'ARROW_DOWN'	=> $arrowdow,
					'CLASS_LIST'	=> $class_list,
					'SUB'			=> $sub_menu
			);
		}
		return $main_menu;
	}

	function menu_filter_id_page($dt){
	if(count($dt)){
		$strIdPage='';
		foreach ($dt as $element){
			$options= json_decode($element['options']);
			$strIdPage.=$options->id_page.',';
		}
	}else{
		$strIdPage=0;
	}
	return rtrim($strIdPage,',');
}
}