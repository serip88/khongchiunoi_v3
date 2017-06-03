<!DOCTYPE html>
<html lang="vi" data-ng-app="app">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tôi giải trí</title>
    <base href="/">
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
    <link rel='stylesheet' id='wsl-widget-css' href="{$config['app_url']}/css/style.css" type='text/css' media='all' />
    <link rel='stylesheet' id='blt-bootstrap-css' href="{$config['app_common_url']}/css/bootstrap.css" type='text/css' media='all' />
    <link rel='stylesheet' id='blt-style-css' href="{$config['app_url']}/themes/first/css/style.css" type='text/css' media='all' />
    <link rel='stylesheet' id='blt-fontawesome-css' href="{$config['app_url']}/css/font-awesome.min.css?ver=2.31" type='text/css' media='all' />
    <link rel='stylesheet' id='acfgfs-enqueue-fonts-css' href='http://fonts.googleapis.com/css?family=Open+Sans%3A400%2Citalic%2C600%2C700%2C800%7COpen+Sans%3A400%2Citalic%2C600%2C700%2C800&#038;subset&#038;ver=4.5.2' type='text/css' media='all' />
    <script type='text/javascript' src="{$config['app_common_url']}/lib/jquery/jquery.js"></script>
    <script type='text/javascript' src="{$config['app_common_url']}/lib/jquery/jquery-migrate.min.js"></script>

    <script src="{$config['app_common_url']}/vendor/angular/angular-1.5.7.js"></script>
    <link rel="stylesheet" href="{$config['app_common_url']}/vendor/angular/angular-strap/libs.min.css">
    <script src="{$config['app_common_url']}/vendor/angular/angular-strap/angular-strap.js" ></script>
    <script src="{$config['app_common_url']}/vendor/angular/angular-strap/angular-strap.tpl.js"></script>
    <script src="{$config['app_common_url']}/vendor/angular/angular-strap/angular-strap.docs.tpl.js" ></script>
    <!-- Cookies -->
    <script src="{$config['app_common_url']}/vendor/angular/angular-cookies/angular-cookies-1.5.7.js"></script>
    <!-- Router -->
    <script src="{$config['app_common_url']}/vendor/angular/angular-ui-router/angular-ui-router.js"></script>
    <script src="{$config['app_common_url']}/vendor/angular/ngstorage/ngStorage.js"></script>
    <!-- Lazyload -->
    <script src="{$config['app_common_url']}/vendor/angular/oclazyload/ocLazyLoad.js"></script>
    <!-- B translate -->
    <script src="{$config['app_common_url']}/vendor/angular/angular-translate/angular-translate.js"></script>
    <script src="{$config['app_common_url']}/vendor/angular/angular-translate/loader-static-files.js"></script>
    <script src="{$config['app_common_url']}/vendor/angular/angular-translate/storage-cookie.js"></script>
    <script src="{$config['app_common_url']}/vendor/angular/angular-translate/storage-local.js"></script>
    <link rel="stylesheet" href="{$config['app_common_url']}/vendor/sweetalert/dist/sweetalert.css">
    <script src="{$config['app_common_url']}/js/directives/sweet-alert.min.js"></script>
    <script src="{$config['app_common_url']}/js/directives/SweetAlert.min.js"></script>
    <!-- B translate -->
    <!-- <script src="{$config['app_common_url']}/vendor/angular/angular-bootstrap/ui-bootstrap-tpls-1.3.3.js"></script> -->

    <script src="{$config['app_common_url']}/js/helper.js"></script>
    <script src="{$config['app_url']}/js/app.js"></script>
    <script src="{$config['app_url']}/js/config.js"></script>
    <script src="{$config['app_url']}/js/config.lazyload.js"></script>
    <script src="{$config['app_url']}/js/config.router.js"></script>
    <script src="{$config['app_url']}/js/main.js"></script>

    <script src="{$config['app_common_url']}/js/services/common-service.js"></script>    

    <meta name="generator" content="Renza.CMS" />
    {literal}
    <style type="text/css">
        .blt-sidebar-sticky {
            display: none
        }
    </style>
    {/literal}
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

<body class="home blog infinite-scroll sidebar-right fixed-header post-layout-normal" ui-view>
   
</body>

</html>