<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



function addDonateFrm_callback()
{
  $configs = include_once(ROOT_PATH. 'config.php');
  include_once(INC_DIR . 'functions.php');

  check_ajax_referer( 'Ny3nIq4Tq8o6', 'security' );
  if ( ! wp_verify_nonce($_POST['nonce'], 'donate-frm-nonce')) {
	$data=array( 'success' => false ,'res' => 'Authenticate Error' );
	echo json_encode($data);
	exit();
  }

  // inits
  $error = '';
  $sisoogDonate_Unit = get_option( 'sisoogDonate_Unit');

  $Name =           filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
  $Description =    filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_SPECIAL_CHARS);
  $Mobile =         filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_SPECIAL_CHARS);
  $Email =          filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
  $Amount =         filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_SPECIAL_CHARS);
  $AuthorId =       filter_input(INPUT_POST, 'author_id', FILTER_SANITIZE_SPECIAL_CHARS);
  $userName =       filter_input(INPUT_POST, 'user_name' , FILTER_SANITIZE_SPECIAL_CHARS);
  $postId =         filter_input(INPUT_POST, 'post_id' , FILTER_SANITIZE_SPECIAL_CHARS);
  $donateData =         filter_input(INPUT_POST, 'donate_data' , FILTER_SANITIZE_SPECIAL_CHARS);
  $SendDescription = $Name . ' | ' . $Mobile . ' | ' . $Email . ' | ' . $Description ;


  if(is_numeric($Amount) != false) {
	if($sisoogDonate_Unit === 'ریال') $SendAmount =  $Amount / 10;
	else $SendAmount =  $Amount;
  } else {
	$error .= 'مبلغ به درستی وارد نشده است' . "<br>\r\n";
  }


  global $wpdb;
  $reqData = getRequest($donateData,$configs['DONATE_KEY']);
  $author_id = $reqData['user_id'];
  $ts = $reqData['ts'];
  $currentTime = time();

  if ($currentTime - $ts > 600) {
	$data=array( 'success' => false ,'error' => 'مدت زمان مجاز برای این فرایند به اتمام رسیده است!','status' => '400' );
	echo json_encode($data);
	exit();
  }


  $merchantsTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;
  $merchants = $wpdb->get_results("SELECT * FROM ${merchantsTable} WHERE user_id = '${author_id}' LIMIT 1");
  $MerchantID = '';
  if (sizeof($merchants) !== 0) {
	$MerchantID =  $merchants[0]->merchant_id ;
	$gateway_name = $merchants[0]->payment_gateway;
  }
  if ($MerchantID === '') {
	$MerchantID = get_option('sisoogDonate_MerchantID');
	$gateway_name = get_option( 'sisoogDonate_MerchantIDType');
  }

  $usersTable = $wpdb->prefix . 'users';
  $postsTable = $wpdb->prefix . 'posts';
  $check1 = $wpdb->get_results("SELECT * FROM ${usersTable} WHERE ID = '${AuthorId}' AND display_name='${userName}' LIMIT 1");
  $check2 = $wpdb->get_results("SELECT * FROM ${postsTable} WHERE post_author = '${AuthorId}' AND ID='${postId}' LIMIT 1");
  if(count($check1) === 0 || count($check2) === 0 ) $error .= 'خطا در اعتبارسنجی!' . "<br>\r\n";

  if($error == '') {
	$CallbackURL = GetCallBackURL();
	$CallbackURL = substr($CallbackURL , 0, strpos($CallbackURL,'wp-admin') - 1) . '/donate_landing';

	// get payment gateway
	switch ($gateway_name){
	  case 'payping':
		$code = payPingDonate_AddDonate(array(
			'Name'          => $Name,
			'AmountTomaan'  => $SendAmount,
			'Mobile'        => $Mobile,
			'Email'         => $Email,
			'InputDate'     => current_time( 'mysql' ),
			'Description'   => $Description,
			'Author'        => $userName,
			'PostID'        => $postId,
			'Status'        => 'SEND',
			'payment_gateway' => 'payping',
			'author_id' => $author_id,
		),array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s' ));

		$data = array('payerName'=>$Name, 'Amount' => $SendAmount,'payerIdentity'=> $Mobile , 'returnUrl' => $CallbackURL, 'Description' => $SendDescription , 'clientRefId' => $code  );
		try {
		  $curl = curl_init();
		  curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.payping.ir/v1/pay",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => json_encode($data),
				  CURLOPT_HTTPHEADER => array(
					  "accept: application/json",
					  "authorization: Bearer " .$MerchantID,
					  "cache-control: no-cache",
					  "content-type: application/json"),
			  )
		  );
		  $response = curl_exec($curl);
		  $header = curl_getinfo($curl);
		  $err = curl_error($curl);
		  curl_close($curl);
		  if ($err) {
			$data=array( 'success' => false ,'error' => $err );
			echo json_encode($data);
			exit();
		  } else {
			if ($header['http_code'] == 200) {
			  $response = json_decode($response, true);
			  if (isset($response["code"]) and $response["code"] != '') {
				$redirectUrl = sprintf('https://api.payping.ir/v1/pay/gotoipg/%s', $response["code"]) ;

				$data=array( 'success' => true ,'redirect_url' => $redirectUrl );
				echo json_encode($data);
				exit();
			  } else {
				$error .= ' تراکنش ناموفق بود- شرح خطا : عدم وجود کد ارجاع '. "<br>\r\n";

				$data=array( 'success' => false ,'error' => $error );
				echo json_encode($data);
				exit();
			  }
			} elseif ($header['http_code'] == 400) {
			  $error .= ' تراکنش ناموفق بود- شرح خطا : ' . implode('. ',array_values (json_decode($response,true))). "<br>\r\n" ;

			  $data=array( 'success' => false ,'error' => $error );
			  echo json_encode($data);
			  exit();
			} else {
			  $error .= ' تراکنش ناموفق بود- شرح خطا : ' . payPingDonate_GetResaultStatusString($header['http_code']) . '(' . $header['http_code'] . ')'. "<br>\r\n";

			  $data=array( 'success' => false ,'error' => $error );
			  echo json_encode($data);
			  exit();
			}
		  }
		} catch (Exception $e){
		  $error .= ' تراکنش ناموفق بود- شرح خطا سمت برنامه شما : ' . $e->getMessage(). "<br>\r\n";

		  $data=array( 'success' => false ,'error' => $error );
		  echo json_encode($data);
		  exit();
		}
		break;
	  case 'zarinpal':
		require_once( LIBDIR . '/nusoap.php' );


		if ($configs['IS_DEV']) $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
		else $client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');

		$client->soap_defencoding = 'UTF-8';
		$result = $client->call('PaymentRequest', array(
				array(
					'MerchantID' 	=> $MerchantID,
					'Amount' 		=> $SendAmount,
					'Description' 	=> $SendDescription,
					'Email' 		=> $Email,
					'Mobile' 		=> $Mobile,
					'CallbackURL' 	=> $CallbackURL
				)
			)
		);

		//Redirect to URL You can do it also by creating a form
		if($result['Status'] == 100)
		{
		  // WriteToDB
		  EZD_AddDonate(array(
			  'Authority'     => $result['Authority'],
			  'Name'          => $Name,
			  'AmountTomaan'  => $SendAmount,
			  'Mobile'        => $Mobile,
			  'Email'         => $Email,
			  'InputDate'     => current_time( 'mysql' ),
			  'Description'   => $Description,
			  'Author'   => $userName,
			  'PostID'        => $postId,
			  'Status'        => 'SEND',
			  'payment_gateway' => 'zarinpal',
			  'author_id' => $author_id,
		  ),array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ));

		  if ($configs['IS_DEV']) $redirectUrl = 'https://sandbox.zarinpal.com/pg/StartPay/'.$result['Authority'];
		  else $redirectUrl = 'https://www.zarinpal.com/pg/StartPay/'.$result['Authority'];

		  $data=array( 'success' => true ,'redirect_url' => $redirectUrl );
		  echo json_encode($data);
		  exit();
		}
		else
		{
		  $error .= EZD_GetResaultStatusString($result['Status']) . "<br>\r\n";

		  $data=array( 'success' => false ,'error' => $error );
		  echo json_encode($data);
		  exit();
		}
		break;
	}
  } else {
	$data=array( 'success' => false ,'error' => $error );
	echo json_encode($data);
	exit();
  }



}
add_action( 'wp_ajax_addDonateFrm', 'addDonateFrm_callback' );
add_action( 'wp_ajax_nopriv_addDonateFrm', 'addDonateFrm_callback' );
