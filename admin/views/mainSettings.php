<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


if ( $_POST ) {

  if ( isset($_POST['sisoogDonate_MerchantIDType']) ) {
	update_option( 'sisoogDonate_MerchantIDType', $_POST['sisoogDonate_MerchantIDType'] );
  }

  if ( isset($_POST['sisoogDonate_MerchantID']) ) {
	update_option( 'sisoogDonate_MerchantID', $_POST['sisoogDonate_MerchantID'] );
  }

  if ( isset($_POST['sisoogDonate_IsOK']) ) {
	update_option( 'sisoogDonate_IsOK', $_POST['sisoogDonate_IsOK'] );
  }

  if ( isset($_POST['sisoogDonate_IsError']) ) {
	update_option( 'sisoogDonate_IsError', $_POST['sisoogDonate_IsError'] );
  }

  if ( isset($_POST['sisoogDonate_Unit']) ) {
	update_option( 'sisoogDonate_Unit', $_POST['sisoogDonate_Unit'] );
  }

  if ( isset($_POST['sisoogDonate_LoginUrl']) ) {
	update_option( 'sisoogDonate_LoginUrl', $_POST['sisoogDonate_LoginUrl'] );
  }

  if ( isset($_POST['sisoogDonate_CallbackSlug']) ) {
	update_option( 'sisoogDonate_CallbackSlug', $_POST['sisoogDonate_CallbackSlug'] );
  }

  if ( isset($_POST['sisoogDonate_UseCustomStyle']) ) {
	update_option( 'sisoogDonate_UseCustomStyle', 'true' );

	if ( isset($_POST['sisoogDonate_CustomStyle']) )
	{
	  update_option( 'sisoogDonate_CustomStyle', strip_tags($_POST['sisoogDonate_CustomStyle']) );
	}

  }
  else
  {
	update_option( 'sisoogDonate_UseCustomStyle', 'false' );
  }

  echo '<div class="updated" id="message"><p><strong>تنظیمات ذخیره شد</strong>.</p></div>';
}
?>


<h2 id="add-new-user">تنظیمات افزونه حمایت مالی - سیسوگ</h2>
<h2 id="add-new-user">برای استفاده تنها کافی است کد زیر را درون بخشی از برگه یا نوشته خود قرار دهید  [SisoogDonate]</h2>
<form method="post">
  <table class="form-table">
	<tbody>
	<tr class="user-first-name-wrap">
	  <th><label for="sisoogDonate_MerchantIDType">درگاه پیشفرض</label></th>
	  <td>
		<select name="sisoogDonate_MerchantIDType" id="sisoogDonate_MerchantIDType" style="max-width: 100%;min-width: 400px">
		  <option value="0">---</option>
		  <option value="payping" <?= @(get_option( 'sisoogDonate_MerchantIDType')=='payping') ? "selected" : "" ?>>پی پینگ</option>
		  <option value="zarinpal" <?= @(get_option( 'sisoogDonate_MerchantIDType')=='zarinpal') ? "selected" : "" ?>>زرین پال</option>
		</select>
	  </td>
	</tr>
	<tr class="user-first-name-wrap">
	  <th><label for="sisoogDonate_MerchantID">توکن پیشفرض</label></th>
	  <td>
		<input type="text" class="regular-text" value="<?php echo get_option( 'sisoogDonate_MerchantID'); ?>" id="sisoogDonate_MerchantID" name="sisoogDonate_MerchantID">
	  </td>
	</tr>
	<tr>
	  <th><label for="sisoogDonate_IsOK">متن پرداخت موفق</label></th>
	  <td><input type="text" class="regular-text" value="<?php echo get_option( 'sisoogDonate_IsOK'); ?>" id="sisoogDonate_IsOK" name="sisoogDonate_IsOK"></td>
	</tr>
	<tr>
	  <th><label for="sisoogDonate_IsError">متن خطا در پرداخت</label></th>
	  <td><input type="text" class="regular-text" value="<?php echo get_option( 'sisoogDonate_IsError'); ?>" id="sisoogDonate_IsError" name="sisoogDonate_IsError"></td>
	</tr>

	<tr class="user-display-name-wrap">
	  <th><label for="sisoogDonate_Unit">واحد پول</label></th>
	  <td>
		<?php $sisoogDonate_Unit = get_option( 'sisoogDonate_Unit'); ?>
		<select id="sisoogDonate_Unit" name="sisoogDonate_Unit">
		  <option <?php if($sisoogDonate_Unit == 'تومان' ) echo 'selected="selected"' ?>>تومان</option>
		  <option <?php if($sisoogDonate_Unit == 'ریال' ) echo 'selected="selected"' ?>>ریال</option>
		</select>
	  </td>
	</tr>

	<tr class="user-display-name-wrap">
	  <th>استفاده از استایل سفارشی</th>
	  <td>
		<?php $sisoogDonate_UseCustomStyle = get_option('sisoogDonate_UseCustomStyle') == 'true' ? 'checked="checked"' : ''; ?>
		<input type="checkbox" name="sisoogDonate_UseCustomStyle" id="sisoogDonate_UseCustomStyle" value="true" <?php echo $sisoogDonate_UseCustomStyle ?> /><label for="sisoogDonate_UseCustomStyle">استفاده از استایل سفارشی برای فرم</label><br>
	  </td>
	</tr>

	<tr>
	  <th><label for="sisoogDonate_LoginUrl">لینک صفحه ورود</label></th>
	  <td><input type="text" class="regular-text" value="<?php echo get_option( 'sisoogDonate_LoginUrl'); ?>" id="sisoogDonate_LoginUrl" name="sisoogDonate_LoginUrl"></td>
	</tr>

	<tr>
	  <th><label for="sisoogDonate_CallbackSlug">لینک صفحه کالبک</label></th>
	  <td><input type="text" class="regular-text" value="<?php echo get_option( 'sisoogDonate_CallbackSlug'); ?>" id="sisoogDonate_CallbackSlug" name="sisoogDonate_CallbackSlug"></td>
	</tr>

	<tr class="user-display-name-wrap" id="sisoogDonate_CustomStyleBox" <?php if(get_option('sisoogDonate_UseCustomStyle') != 'true') echo 'style="display:none"'; ?>>
	  <th>استایل سفارشی</th>
	  <td>
		<textarea style="width: 90%;min-height: 400px;direction:ltr;" name="sisoogDonate_CustomStyle" id="sisoogDonate_CustomStyle"><?php echo get_option('sisoogDonate_CustomStyle') ?></textarea><br>
	  </td>
	</tr>

	</tbody>
  </table>
  <p class="submit"><input type="submit" value="به روز رسانی تنظیمات" class="button button-primary" id="submit" name="submit"></p>
</form>

<script>
    jQuery(document).ready(function ($) {
        const sisoogDonate_CustomStyleBox = $('#sisoogDonate_CustomStyleBox');

        $("#sisoogDonate_UseCustomStyle").change(function(){
            if($(this).prop('checked'))
                $(sisoogDonate_CustomStyleBox).slideDown(500);
            else
                $(sisoogDonate_CustomStyleBox).slideUp(500);
        });
    })
</script>

