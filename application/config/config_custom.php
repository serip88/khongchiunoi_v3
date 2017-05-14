<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['base_url'] = strpos($_SERVER['HTTP_HOST'],"www")===0?'http://'.$_SERVER['HTTP_HOST'].'/':'http://www.'.$_SERVER['HTTP_HOST'].'/';
$config['site_name'] = 'Ungdungnho';
$config['site_version'] = '1.0';