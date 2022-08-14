<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action( 'init', function () {
  add_shortcode('sisoogDonate', 'ShowDonateFrm');
  add_shortcode('ShowAuthorsList', 'ShowAuthorsList');
  add_shortcode('ShowDonatesList', 'ShowDonatesList');
});


function ShowAuthorsList(){
ob_start();
include(plugin_dir_path( __FILE__ ).'../site/views/show_authors_list.php');
return do_shortcode(ob_get_clean());
}
function ShowDonatesList(){
ob_start();
include(plugin_dir_path( __FILE__ ).'../site/views/show_donates_list.php');
return do_shortcode(ob_get_clean());
}
function ShowDonateFrm(){
ob_start();
include(plugin_dir_path( __FILE__ ).'../site/views/show_donate_form.php');
return do_shortcode(ob_get_clean());
}