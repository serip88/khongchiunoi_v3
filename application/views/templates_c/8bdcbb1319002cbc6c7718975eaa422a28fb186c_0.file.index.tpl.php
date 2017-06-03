<?php
/* Smarty version 3.1.29, created on 2017-06-03 06:54:08
  from "C:\xampp\htdocs\toigiaitri_v3\application\views\app_change\index.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_593240f0bec9f9_88116480',
  'file_dependency' => 
  array (
    '8bdcbb1319002cbc6c7718975eaa422a28fb186c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\toigiaitri_v3\\application\\views\\app_change\\index.tpl',
      1 => 1496465644,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_593240f0bec9f9_88116480 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="vi" data-ng-app="app">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tôi giải trí</title>
    <!-- <base href="/"> -->
    <style type="text/css">
        img.wp-smiley,
        img.emoji {
            display: inline !important;
            border: none !important;
            box-shadow: none !important;
            height: 1em !important;
            width: 1em !important;
            margin: 0 .07em !important;
            vertical-align: -0.1em !important;
            background: none !important;
            padding: 0 !important;
        }
    </style>
    <link rel='stylesheet' id='wsl-widget-css' href="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/css/style.css" type='text/css' media='all' />
    <link rel='stylesheet' id='blt-bootstrap-css' href="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/css/bootstrap.css" type='text/css' media='all' />
    <link rel='stylesheet' id='blt-style-css' href="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/css/theme/style.css" type='text/css' media='all' />
    <link rel='stylesheet' id='blt-fontawesome-css' href="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/css/font-awesome.min.css?ver=2.31" type='text/css' media='all' />
    <link rel='stylesheet' id='acfgfs-enqueue-fonts-css' href='http://fonts.googleapis.com/css?family=Open+Sans%3A400%2Citalic%2C600%2C700%2C800%7COpen+Sans%3A400%2Citalic%2C600%2C700%2C800&#038;subset&#038;ver=4.5.2' type='text/css' media='all' />
    <?php echo '<script'; ?>
 type='text/javascript' src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/lib/jquery/jquery.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type='text/javascript' src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/lib/jquery/jquery-migrate.min.js"><?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-1.5.7.js"><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-strap/libs.min.css">
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-strap/angular-strap.js" ><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-strap/angular-strap.tpl.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-strap/angular-strap.docs.tpl.js" ><?php echo '</script'; ?>
>
    <!-- Cookies -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-cookies/angular-cookies-1.5.7.js"><?php echo '</script'; ?>
>
    <!-- Router -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-ui-router/angular-ui-router.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/ngstorage/ngStorage.js"><?php echo '</script'; ?>
>
    <!-- Lazyload -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/oclazyload/ocLazyLoad.js"><?php echo '</script'; ?>
>
    <!-- B translate -->
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-translate/angular-translate.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-translate/loader-static-files.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-translate/storage-cookie.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-translate/storage-local.js"><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/sweetalert/dist/sweetalert.css">
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/js/directives/sweet-alert.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/js/directives/SweetAlert.min.js"><?php echo '</script'; ?>
>
    <!-- B translate -->
    <!-- <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/vendor/angular/angular-bootstrap/ui-bootstrap-tpls-1.3.3.js"><?php echo '</script'; ?>
> -->

    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/js/helper.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/js/app.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/js/config.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/js/config.lazyload.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/js/config.router.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_url'];?>
/js/main.js"><?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['config']->value['app_common_url'];?>
/js/services/common-service.js"><?php echo '</script'; ?>
>    

    <meta name="generator" content="Renza.CMS" />
    
    <style type="text/css">
        .blt-sidebar-sticky {
            display: none
        }
    </style>
    
    <style type="text/css">
        body {
            /* image */
            background-color: #f4f4f4;

            font-family: Open Sans;
        }
        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: Open Sans;
        }
        .wpcf7-submit,
        .btn-theme {
            background: #4cc2bf;
        }
        .wpcf7-submit,
        .btn-theme:hover {
            background: #38aeab;
        }
        .blt_mailchimp .input-group .btn {
            border-left-color: #38aeab;
        }
        a{
            color: #4cc2bf;
        }
        .blu-post-tags a:hover,
        a.label-blt:hover {
            border-color: #38aeab;
            color: #38aeab;
        }
        a:focus,
        a:hover,
        a.label:focus,
        a.label:hover,
        .primary-menu .nav a:hover,
        .widget_calendar table tbody td a:hover {
            color: #38aeab;
        }
        ::-moz-selection {
            background-color: #4cc2bf;
            color: #FFF;
        }
        ::selection {
            background-color: #4cc2bf;
            color: #FFF;
        }
        .pagination .page-numbers.current {
            background-color: #4cc2bf;
            border-color: #38aeab;
        }
        .blu-post-pagination>a:hover,
        .blu-post-pagination>span {
            background-color: #4cc2bf;
            border-color: #4cc2bf;
        }
        .select2-container-active .select2-choice,
        .select2-container-active .select2-choices,
        .select2-drop-active,
        .select2-search {
            border-color: #38aeab;
        }

        .select2-results .select2-highlighted {
            background-color: #38aeab;
        }

        @media (min-width: 1100px) {
            #site-content {
                width: 1050px;
            }
            #site-content-column {
                width: 690px;
            }
            .spot_below_menu,
            .container {
                width: 1050px;
            }
        }

        .post-status {
            background-color: #4cc2bf;
        }

        .new-post-list>.list-group>.blt-add-new-image:hover {
            background-color: #4cc2bf;
        }
    </style>
</head>

<body class="home blog infinite-scroll sidebar-right fixed-header post-layout-normal">
   
</body>

</html><?php }
}
