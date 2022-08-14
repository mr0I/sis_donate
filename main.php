<?php
/*
Plugin Name: افزونه حمایت مالی سیسوگ
Version: 1.0
Description: افزونه حمایت مالی از وبسایت ها -- برای استفاده تنها کافی است کد زیر را درون بخشی از برگه یا نوشته خود قرار دهید  [SisoogDonate]
Plugin URI: https://sisoog.com/
Author: IO
Author URI:
*/
defined('ABSPATH') or die('Access denied!');


// define constants
define('ROOT_DIR', plugin_dir_url(__FILE__));
define('ADMIN_CSS', plugin_dir_url(__FILE__) . 'admin/css/');
define('ADMIN_JS', plugin_dir_url(__FILE__) . 'admin/js/');
define('ADMIN_DIR', plugin_dir_path(__FILE__) . 'admin/');
define('SITE_CSS', plugin_dir_url(__FILE__) . 'static/css/');
define('SITE_JS', plugin_dir_url(__FILE__) . 'static/js/');
define('ASSETSDIR', plugin_dir_url(__FILE__) . 'static/');
define('ADMINVIEWSDIR', plugin_dir_path(__FILE__) . 'admin/views/');
define('LIBDIR', plugin_dir_path(__FILE__) . 'lib/');
define ('TABLE_DONATE'  , 'sisoog_donate');
define ('TABLE_MERCHANTS_IDS'  , 'gateway_merchantsids');
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

// enqueue statics
add_action ('admin_enqueue_scripts', function(){
  wp_enqueue_style('admin-styles', ADMIN_CSS.'admin-styles.css');
  wp_enqueue_script('admin-scripts', ADMIN_JS.'admin-scripts.js' , array('jquery'));
  wp_localize_script( 'admin-scripts', 'SISOOGDONATEADMINAJAX', array(
	  'ajaxurl' => admin_url( 'admin-ajax.php' ),
	  'security' => wp_create_nonce( 'Ny3nIq4Tq8o6' )
  ));
});
add_action( 'wp_enqueue_scripts', function(){
  wp_enqueue_style( 'sisoog_donate_styles', SITE_CSS . 'styles.css');
  wp_enqueue_script('sisoog_donate_scripts', SITE_JS.'scripts.js' , array('jquery'));
});


