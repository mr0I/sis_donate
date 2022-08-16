<?php defined('ABSPATH') or die('&lt;h3&gt;Access denied!');

global $wpdb;
$user_id = get_current_user_id();
$merchantsTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;
$merchants = $wpdb->get_results("SELECT * FROM ${merchantsTable} WHERE user_id = '${user_id}' LIMIT 1");
//var_dump($merchants);

if ( $_POST ) {
  if (isset($_POST['add_merchantId_submit']) && $_POST['add_merchantId_submit'] === 'ذخیره') {
	  $gateway_name = $_POST['gateway_name'];
	  $gateway_id = $_POST['gateway_id'];

	  //$user_id = get_current_user_id();
	  //$merchants = $wpdb->get_results("SELECT * FROM ${merchantsTable} WHERE user_id = '${user_id}' LIMIT 1");
	  if (sizeof($merchants) === 0){
		$data = array(
			'user_id' => absint($user_id),
			'merchant_id' => sanitize_text_field($gateway_id),
			'payment_gateway' => sanitize_text_field($gateway_name),
			'date' => current_time( 'mysql' )
		);
		$insert = $wpdb->insert( $merchantsTable , $data, array( '%d','%s', '%s','%s' ));
		if ($insert) {
		 //$_SESSION['add_merchantId'] = 'success';
		}
	  } else {
		$update = $wpdb->update( $merchantsTable, array(
			'merchant_id' => sanitize_text_field($gateway_id),
			'payment_gateway' => sanitize_text_field($gateway_name)
		),
			array( 'user_id' => absint($user_id)),
			array( '%s' , '%s' ),
			array( '%d' )
		);
		if ($update) {
		  //$_SESSION['add_merchantId'] = 'success';
		}
	  }
  }

  echo '<div class="updated" id="message"><p><strong>تنظیمات ذخیره شد</strong>.</p></div>';
}
?>


<form method="post" id="add_merchantId_frm" name="add_merchantId_frm">

<!--  --><?php //if ($_SESSION['add_merchantId']) : unset($_SESSION['add_merchantId']); ?>
<!--	<div class="updated notice"><p>تنظیمات ذخیره شد.</p></div>-->
<!--  --><?php //endif; ?>

  <table class="form-table" role="presentation">
	<tbody>
	<tr>
	  <th scope="row">نام درگاه</th>
	  <td>
		<select name="gateway_name" style="max-width: 100%;min-width: 400px">
		  <option value="0">---</option>
		  <option value="payping" <?= @($merchants[0]->payment_gateway=='payping' && sizeof($merchants) !== 0) ? "selected" : "" ?>>پی پینگ</option>
		  <option value="zarinpal" <?= @($merchants[0]->payment_gateway=='zarinpal' && sizeof($merchants) !== 0) ? "selected" : "" ?>>زرین پال</option>
		</select>
	  </td>
	</tr>
	<tr>
	  <th scope="row">آیدی درگاه</th>
	  <td>
		<input class="ltr left-align" type="text" name="gateway_id" value="<?= @($merchants[0]->merchant_id && sizeof($merchants) !== 0) ? $merchants[0]->merchant_id : '' ?>"
			   style="max-width: 100%;min-width: 400px" required="">
	  </td>
	</tr>
	</tbody>
  </table>
  <input type="hidden" id="add_merchantId_nonce" value="<?= wp_create_nonce('add-merchantId-nonce') ?>">
  <p class="submit"><input type="submit" name="add_merchantId_submit" id="add_merchantId_submit" class="button button-primary" value="ذخیره"></p>
</form>