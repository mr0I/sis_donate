<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function getRequest($donate_data,$key)
{
  $decHash = decryptHash($donate_data,$key);
  $request = array(
	  'user_id' => $decHash->user_id,
	  'user_name' => $decHash->user_name,
	  'post_id' => $decHash->post_id,
	  'ts' => $decHash->ts,
  );

  return $request;
}