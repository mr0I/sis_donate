<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action( 'init', function () {
  add_shortcode('SisoogDonate', 'ShowDonateFrm');
  add_shortcode('ShowAuthorDonatesList', 'ShowAuthorDonatesList');
  add_shortcode('ShowDonatesList', 'ShowDonatesList');
  add_shortcode('DonateLandingPage', 'DonateLanding');
});

function ShowAuthorDonatesList(){
  ob_start();
  include(plugin_dir_path( __FILE__ ).'../site/views/show_author_donates_list.php');
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
}function DonateLanding(){
  ob_start();
  include(plugin_dir_path( __FILE__ ).'../site/views/donate_landing_page.php');
  return do_shortcode(ob_get_clean());
}