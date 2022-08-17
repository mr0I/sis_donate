<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


$configs = include_once(ROOT_PATH . 'config.php');
include_once(INC_DIR . 'functions.php');

global $wpdb;
$donatesTable = $wpdb->prefix . TABLE_DONATE;
$merchantsTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;

if (! isset($_GET['Authority']) && ! isset($_GET['clientrefid'])) {
  wp_die('خطای دسترسی!');
  return;
}
if (isset($_GET['Authority'])) {
  $gateway_name = 'zarinpal';
  $authority = $_GET['Authority'];
  $donate = $wpdb->get_results("SELECT * FROM ${donatesTable} WHERE Authority = '${authority}' LIMIT 1");
  $author_id = $donate[0]->author_id;
  $merchant = $wpdb->get_results("SELECT * FROM ${merchantsTable} WHERE user_id = '${author_id}' LIMIT 1");
  $MerchantID = $merchant[0]->merchant_id;
  $postUrl = get_permalink($donate[0]->PostID);
} elseif (isset($_GET['clientrefid'])) {
  $gateway_name = 'payping';
  $clientRefID = $_GET['clientrefid'];
  $donate = $wpdb->get_results("SELECT * FROM ${donatesTable} WHERE DonateID = '${clientRefID}' LIMIT 1");
  $author_id = $donate[0]->author_id;
  $merchant = $wpdb->get_results("SELECT * FROM ${merchantsTable} WHERE user_id = '${author_id}' LIMIT 1");
  $MerchantID = $merchant[0]->merchant_id;
  $postUrl = get_permalink($donate[0]->PostID);
}


switch ($gateway_name) {
  case 'payping':
	if(isset($_GET['clientrefid']))
	{
	  $id = $_GET['clientrefid'] ;
	  $refid = $_GET['refid'] ;

	  $Record = payPingDonate_GetDonate($id);
	  if( $Record  === false)
	  {
		$error .= 'چنین تراکنشی در سایت ثبت نشده است' . "<br>\r\n";
	  }
	  else
	  {
		$data = array('refId' => $refid, 'amount' => $Record['AmountTomaan']);
		try {
		  $curl = curl_init();
		  curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.payping.ir/v1/pay/verify",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($data),
			  CURLOPT_HTTPHEADER => array(
				  "accept: application/json",
				  "authorization: Bearer ".$MerchantID,
				  "cache-control: no-cache",
				  "content-type: application/json",
			  ),
		  ));
		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  $header = curl_getinfo($curl);
		  curl_close($curl);
		  if ($err) {
			payPingDonate_ChangeStatus($id, 'ERROR');
			$error .= get_option( 'sisoogDonate_IsError') . "<br>\r\n";
			$error .= 'خطا در ارتباط به پی‌پینگ : شرح خطا '.$err. "<br>\r\n";
			payPingDonate_SetAuthority($id, $refid);
		  } else {
			if ($header['http_code'] == 200) {
			  $response = json_decode($response, true);
			  if (isset($_GET["refid"]) and $_GET["refid"] != '') {
				payPingDonate_ChangeStatus($id, 'OK');
				payPingDonate_SetAuthority($id, $refid);
				$message .= get_option( 'sisoogDonate_IsOK') . "<br>\r\n";
				$message .= 'کد پیگیری تراکنش:'. $refid . "<br>\r\n";
				$payPingDonate_TotalAmount = get_option("sisoogDonate_TotalAmount");
				update_option("sisoogDonate_TotalAmount" , $payPingDonate_TotalAmount + $Record['AmountTomaan']);

				// Send email to author
				sendEmailToAuthor('payping',$id,$refid);
			  } else {
				payPingDonate_ChangeStatus($id, 'ERROR');
				$error .= get_option( 'sisoogDonate_IsError') . "<br>\r\n";
				payPingDonate_SetAuthority($id, $refid);
				$error .= 'متافسانه سامانه قادر به دریافت کد پیگیری نمی باشد! نتیجه درخواست : ' . payPingDonate_GetResaultStatusString($header['http_code']) . '(' . $header['http_code'] . ')' . "<br>\r\n";
			  }
			} elseif ($header['http_code'] == 400) {
			  payPingDonate_ChangeStatus($id, 'ERROR');
			  $error .= get_option( 'sisoogDonate_IsError') . "<br>\r\n";
			  payPingDonate_SetAuthority($id, $refid);
			  $error .= 'تراکنش ناموفق بود- شرح خطا : ' .  implode('. ',array_values (json_decode($response,true))) . "<br>\r\n";
			}  else {
			  payPingDonate_ChangeStatus($id, 'ERROR');
			  $error .= get_option( 'sisoogDonate_IsError') . "<br>\r\n";
			  payPingDonate_SetAuthority($id, $refid);
			  $error .= ' تراکنش ناموفق بود- شرح خطا : ' . payPingDonate_GetResaultStatusString($header['http_code']) . '(' . $header['http_code'] . ')'. "<br>\r\n";
			}
		  }
		} catch (Exception $e){
		  payPingDonate_ChangeStatus($id, 'ERROR');
		  $error .= get_option( 'sisoogDonate_IsError') . "<br>\r\n";
		  payPingDonate_SetAuthority($id, $refid);
		  $error .= ' تراکنش ناموفق بود- شرح خطا سمت برنامه شما : ' . $e->getMessage(). "<br>\r\n";
		}
	  }
	}
	break;
  case 'zarinpal':
	require_once( LIBDIR . '/nusoap.php' );

	$Authority = filter_input(INPUT_GET, 'Authority', FILTER_SANITIZE_SPECIAL_CHARS);
	if($_GET['Status'] == 'OK'){
	  $Record = EZD_GetDonate($Authority);
	  if( $Record  === false)
	  {
		$error .= 'چنین تراکنشی در سایت ثبت نشده است' . "<br>\r\n";
	  }
	  else
	  {
		if ($configs['IS_DEV']) $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
		else $client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');

		$client->soap_defencoding = 'UTF-8';
		$result = $client->call('PaymentVerification', array(
				array(
					'MerchantID'	 => $MerchantID,
					'Authority' 	 => $Record['Authority'],
					'Amount'	 	 => $Record['AmountTomaan']
				)
			)
		);

		if($result['Status'] == 100)
		{
		  EZD_ChangeStatus($Authority, 'OK');
		  $message .= get_option( 'EZD_IsOk') . "<br>\r\n";
		  $message .= 'کد پیگیری تراکنش:'. $result['RefID'] . "<br>\r\n";

		  // send email to author
		  sendEmailToAuthor('zarinpal',$Authority,$result['RefID']);
		} else {
		  $homeUrl = home_url();
		  echo "<script>document.location = '${homeUrl}'</script>";
		}
	  }
	}
	else
	{
	  $error .= 'تراکنش توسط کاربر بازگشت خورد';
	  EZD_ChangeStatus($Authority, 'CANCEL');
	}
	break;
}
?>


<?php
if ($message) {
  echo '<div class="alert alert-success text-center" role="alert">
      <h4 class="alert-heading">پرداخت با موفقیت انجام شد :)</h4>
      <small style="color: inherit;text-align:center;">'.$message.'</small>
    </div>';
} elseif ($error) {
  echo '<div class="alert alert-danger text-center" role="alert">
      <h4 class="alert-heading">خطا در انجام عملیات!</h4>
      <small style="color: inherit;text-align:center;">'.$error.'</small>
      <hr>
      <p> <a href="'.$postUrl.'"><small>بازگشت</small></a></p>
    </div>';
} else {
  echo '<div class="alert alert-warning text-center" role="alert">
      <h4 class="alert-heading">خطا در انجام عملیات!</h4>
      <small style="color: inherit;text-align:center;">یک خطای نامشخص رخ داد. مجددا تلاش نمایید</small>
        </div>';
}
?>
