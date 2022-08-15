<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('admin_menu', function (){
  add_menu_page(
	  'تنظیمات افزونه حمایت مالی سیسوگ',
	  'حمایت مالی سیسوگ',
	  'administrator',
	  'sisoogdonate_menuItems',
	  function (){include(ADMINVIEWSDIR . 'mainSettings.php');},
	  'dashicons-money-alt' );

  add_submenu_page('sisoogdonate_menuItems',
	  'نمایش حامیان مالی',
	  'نمایش حامیان مالی',
	  'administrator',
	  'sisoogDonate_supporters',
	  function (){include(ADMINVIEWSDIR . 'supporters.php');});

  add_submenu_page('sisoogdonate_menuItems',
	  'ثبت آیدی درگاه پرداخت',
	  'ثبت آیدی درگاه پرداخت',
	  'edit_posts',
	  'sisoogDonate_merchantId',
	  function (){include(ADMINVIEWSDIR .'merchantIds.php');});
});
