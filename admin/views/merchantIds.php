<?php defined('ABSPATH') or die('&lt;h3&gt;Access denied!');

include_once(INC_DIR . 'functions.php');


global $wpdb;
$user_id = get_current_user_id();
$merchantTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;
$merchant = getOneMerchant($user_id,$merchantTable);

if (isset($_POST['add_merchantId_submit']) && $_POST['add_merchantId_submit'] === 'ذخیره') {
  $gateway_name = $_POST['gateway_name'];
  $gateway_id = $_POST['gateway_id'];

  if (sizeof($merchant) === 0){
	$data = array(
		'user_id' => absint($user_id),
		'merchant_id' => sanitize_text_field($gateway_id),
		'payment_gateway' => sanitize_text_field($gateway_name),
		'date' => current_time( 'mysql' )
	);
	$insert = $wpdb->insert( $merchantTable , $data, array( '%d','%s', '%s','%s' ));
	if ($insert) {
      echo '<div class="updated notice"><p>تنظیمات ذخیره شد.</p></div>';
	}
  } else {
	$update = $wpdb->update( $merchantTable, array(
		'merchant_id' => sanitize_text_field($gateway_id),
		'payment_gateway' => sanitize_text_field($gateway_name)
	),
		array( 'user_id' => absint($user_id)),
		array( '%s' , '%s' ),
		array( '%d' )
	);
	if ($update) {
	  echo '<div class="updated notice"><p>تنظیمات آپدیت شد.</p></div>';
	}
  }
}
?>


<form method="post">

  <?php
  global $wpdb;
  $user_id = get_current_user_id();
  $merchantTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;
  $merchant = getOneMerchant($user_id,$merchantTable);
  ?>
    <table class="form-table" role="presentation">
        <tbody>
        <tr>
            <th scope="row">نام درگاه</th>
            <td>
                <select name="gateway_name" style="max-width: 100%;min-width: 400px">
                    <option value="0">---</option>
                    <option value="payping" <?= @($merchant[0]->payment_gateway=='payping' && sizeof($merchant) !== 0) ? "selected" : "" ?>>پی پینگ</option>
                    <option value="zarinpal" <?= @($merchant[0]->payment_gateway=='zarinpal' && sizeof($merchant) !== 0) ? "selected" : "" ?>>زرین پال</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">آیدی درگاه</th>
            <td>
                <input class="ltr left-align" type="text" name="gateway_id" value="<?= @($merchant[0]->merchant_id && sizeof($merchant) !== 0) ? $merchant[0]->merchant_id : '' ?>"
                       style="max-width: 100%;min-width: 400px" required="">
            </td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" id="add_merchantId_nonce" value="<?= wp_create_nonce('add-merchantId-nonce') ?>">
    <p class="submit"><input type="submit" name="add_merchantId_submit" id="add_merchantId_submit" class="button button-primary" value="ذخیره"></p>
</form>