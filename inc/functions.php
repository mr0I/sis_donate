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

function payPingDonate_GetDonate($id)
{
  global $wpdb;
  $id = strip_tags($wpdb->escape($id));


  if($id == '')
	return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;

  $res = $wpdb->get_results( "SELECT * FROM ".$DonateTable." WHERE `DonateID` = ".$id." LIMIT 1",ARRAY_A);

  if(count($res) == 0)
	return false;
  return $res[0];
}
function payPingDonate_AddDonate($Data, $Format)
{
  global $wpdb;

  if(!is_array($Data))
	return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;

  $res = $wpdb->insert( $DonateTable , $Data, $Format);

  return $wpdb->insert_id;
}
function payPingDonate_ChangeStatus($id,$Status)
{
  global $wpdb;
  $id = strip_tags($wpdb->escape($id));
  $Status = strip_tags($wpdb->escape($Status));

  if($id == '' || $Status == '')
	return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;

  $res = $wpdb->query( "UPDATE ".$DonateTable." SET `Status` = '".$Status."' WHERE `DonateID` = '".$id."'");

  return $res;
}
function payPingDonate_SetAuthority($id,$Authority)
{
  global $wpdb;
  $id = strip_tags($wpdb->escape($id));
  $Authority = strip_tags($wpdb->escape($Authority));

  if($id == '' || $Authority == '')
	return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;

  $res = $wpdb->query( "UPDATE ".$DonateTable." SET `Authority` = '".$Authority."' WHERE `DonateID` = '".$id."'");

  return $res;
}
function payPingDonate_GetResaultStatusString($StatusNumber)
{
  switch($StatusNumber) {
	case 200 :
	  return 'عملیات با موفقیت انجام شد';
	  break ;
	case 400 :
	  return 'مشکلی در ارسال درخواست وجود دارد';
	  break ;
	case 500 :
	  return 'مشکلی در سرور رخ داده است';
	  break;
	case 503 :
	  return 'سرور در حال حاضر قادر به پاسخگویی نمی‌باشد';
	  break;
	case 401 :
	  return 'عدم دسترسی';
	  break;
	case 403 :
	  return 'دسترسی غیر مجاز';
	  break;
	case 404 :
	  return 'آیتم درخواستی مورد نظر موجود نمی‌باشد';
	  break;
  }

  return '';
}


function EZD_GetDonate($Authority)
{
  global $wpdb;
  $Authority = strip_tags($wpdb->escape($Authority));

  if($Authority == '')
	return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;
  $res = $wpdb->get_results( "SELECT * FROM ${DonateTable} WHERE Authority = '${Authority}' LIMIT 1",ARRAY_A);

  if(count($res) == 0) return false;
  return $res[0];
}
function EZD_AddDonate($Data, $Format)
{
  global $wpdb;
  if(!is_array($Data)) return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;

  $res = $wpdb->insert( $DonateTable , $Data, $Format);

  return $res;
}
function EZD_ChangeStatus($Authority,$Status)
{
  global $wpdb;
  $Authority = strip_tags($wpdb->escape($Authority));
  $Status = strip_tags($wpdb->escape($Status));

  if($Authority == '' || $Status == '')
	return false;

  $DonateTable = $wpdb->prefix . TABLE_DONATE;

  $res = $wpdb->query( "UPDATE ${DonateTable} SET `Status` = '${Status}' WHERE `Authority` = '${Authority}'");

  return $res;
}
function EZD_GetResaultStatusString($StatusNumber)
{
  switch($StatusNumber)
  {
	case -1:
	  return 'اطلاعات ارسال شده ناقص است';
	case -2:
	  return 'IP و یا مرچنت کد پذیرنده صحیح نیست';
	case -3:
	  return 'رقم باید بالای صد تومان باشد';
	case -4:
	  return 'سطح تایید پذیرنده پایین تر از سطح نقره ای است';
	case -11:
	  return 'درخواست مورد نظر یافت نشد';
	case -21:
	  return 'هیچ نوع عملیات مالی برای این تراکنش یافت نشد';
	case -22:
	  return 'تراکنش نا موفق می باشد';
	case -33:
	  return 'رقم تراکنش با رقم پرداخت شده مطابقت ندارد';
	case -54:
	  return 'درخواست مورد نظر آرشیو شده';
	case 100:
	  return 'عملیات با موفقیت انجام شد';
	case 101:
	  return 'عملیات این تراکنش با موفقیت انجام شد ولی قبلا عملیات اعتبار سنجی بر روی این تراکنش انجام شده است';
  }

  return '';
}

function GetCallBackURL()
{
  $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

  $ServerName = htmlspecialchars($_SERVER["SERVER_NAME"], ENT_QUOTES, "utf-8");
  $ServerPort = htmlspecialchars($_SERVER["SERVER_PORT"], ENT_QUOTES, "utf-8");
  $ServerRequestUri = htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES, "utf-8");

  if ($_SERVER["SERVER_PORT"] != "80")
  {
	$pageURL .= $ServerName .":". $ServerPort . $_SERVER["REQUEST_URI"];
  }
  else
  {
	$pageURL .= $ServerName . $ServerRequestUri;
  }
  return $pageURL;
}
function sendEmail($name,$tracking_code,$AmountTomaan, $post_title ,$email){
  ob_start();
  include INCDIR . 'email_template.php';
  $html=ob_get_contents();
  ob_end_clean();
  $html=  str_replace('{name}',$name, $html);
  $html=  str_replace('{amount_tomaan}',$AmountTomaan, $html);
  $html=  str_replace('{post_title}',$post_title, $html);
  $html=  str_replace('{tracking_code}',$tracking_code, $html);
  $headers  = 'From: no-reply@domain.com'. "\r\n" .
			  'MIME-Version: 1.0' . "\r\n" .
			  'Content-type: text/html; charset=utf-8' . "\r\n" .
			  'X-Mailer: PHP/' . phpversion();

  return wp_mail( $email, 'حمایت مالی(سیسوگ)', $html, $headers);
}

if (! function_exists('decryptHash')){

  function decryptHash($ciphered, $cryptoKey) {
	try{
	  $data       = explode(":", $ciphered);
	  $iv         = hex2bin($data[0]);
	  $ciphertext = hex2bin($data[1]);
	} catch (Exception $e) {
	  echo 'error:' . $e;
	}

	return json_decode(openssl_decrypt($ciphertext, AES_METHOD, $cryptoKey, OPENSSL_RAW_DATA, $iv));
  }
}

function getOneMerchant($user_id,$merchants_table)
{
  global $wpdb;
  return $wpdb->get_results("SELECT * FROM ${merchants_table} WHERE user_id = '${user_id}' LIMIT 1");
}