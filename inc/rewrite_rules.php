<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// donate rewrite rule
add_action('init', function (){
  add_rewrite_rule(
	  'donate/([^/]+)/?$',
	  'index.php?pagename=donate&donate_data=$matches[1]',
	  'top'
  );
});
add_action('template_redirect', function(){
  return true;
});
add_filter( 'query_vars', function ($vars){
  $vars[] = 'donate_data';
  return $vars;
});