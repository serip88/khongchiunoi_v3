<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//$config['base_url'] = strpos($_SERVER['HTTP_HOST'],"www")===0?'http://'.$_SERVER['HTTP_HOST'].'/':'http://www.'.$_SERVER['HTTP_HOST'].'/';
$config['base_url'] = strpos($_SERVER['HTTP_HOST'],"www")===0?'http://'.$_SERVER['HTTP_HOST'].'/':'http://'.$_SERVER['HTTP_HOST'].'/';
/*$config['site_name'] = 'Ungdungnho';
$config['site_version'] = '1.0';
$config['app_path'] = 'app_change';
$config['app_admin_path'] = 'app_change_ad';
$config['app_name'] = 'bacrau';
$config['app_url'] = $config['base_url'].'app/'.$config['app_path'];
$config['app_url_admin'] = $config['base_url'].'app/app_change_ad';
$config['app_common_url'] = $config['base_url'].'app/app_common';*/

$config['site_name'] = 'Pay';
$config['site_version'] = '1.0';
$config['app_path'] = 'app_pay';
$config['app_admin_path'] = 'app_pay_ad';
$config['app_name'] = 'pay';
$config['app_url'] = $config['base_url'].'app/'.$config['app_path'];
$config['app_url_admin'] = $config['base_url'].'app/app_pay_ad';
$config['app_common_url'] = $config['base_url'].'app/app_common';