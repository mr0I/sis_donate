<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>

<?php
// Convert Date To Shamsi
require_once LIBDIR . '/jdate/jdatetime.class.php';
$date = new jDateTime(true, true, 'Asia/Tehran');
$configs = include_once(ROOT_PATH . 'config.php');
include_once(INC_DIR . 'functions.php');
?>

<?php
if ( !is_user_logged_in() ) {
  ?>
    <div class="alert alert-warning text-center" role="alert">
        <p class="text-center" style="color: inherit">برای مشاهده این صفحه باید وارد سایت شوید!</p>
        <p class="text-center"><a href="<?= (get_option('sisoogDonate_LoginUrl')) ? get_option('sisoogDonate_LoginUrl') : '#' ?>">ورود به سایت</a></p>
    </div>
  <?php
} else {
  $current_user = wp_get_current_user();
  $Name = $current_user->display_name;

  global $wpdb;
  $LIMIT = '';
  $DonateTable = $wpdb->prefix . TABLE_DONATE;
  $all_donates = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Author='$Name' ORDER BY InputDate DESC ");

  $page_num = isset( $_GET['page_num'] ) ? absint( $_GET['page_num'] ) : 1;
  $limit = $configs['PAGINATE_NUM'] ? $configs['PAGINATE_NUM'] : 10 ;
  $offset = ( $page_num - 1 ) * $limit;

  $user_donates = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Author='$Name' AND Status='OK' ORDER BY InputDate DESC LIMIT $offset,$limit ");
  $total_donates = $wpdb->get_var( "SELECT COUNT(*) FROM $DonateTable WHERE Author='$Name' AND Status='OK' ");
  $page_links = getPageLinks($total_donates,$limit,$page_num);
  ?>

  <?php
  if (count($user_donates) === 0){
	echo '<div class="alert alert-warning w-100" role="alert"><p class="text-center" style="color: inherit">هنوز هیچ پرداختی برای شما ثبت نشده است!</p></div>';
  } else {
	?>
      <div class="authors_donates_container">
          <div class="container">
              <div class="row">
                  <div class="table-responsive">
                      <table class="table" id="authors_list_table">
                          <caption style="caption-side: top">لیست کمک های مالی شما</caption>
                          <thead>
                          <tr>
                              <th>ردیف</th>
                              <th>مبلغ</th>
                              <th>مقاله</th>
                              <th>شماره پیگیری</th>
                              <th>تاریخ</th>
                          </tr>
                          </thead>
                          <tbody>
						  <?php
						  $counter = 1;
						  foreach ($user_donates as $row){
							?>
                              <tr>
                                  <td><?= $counter++; ?></td>
                                  <td class="digits"><?= $row->AmountTomaan . ' ' . get_option('sisoogDonate_Unit'); ?></td>
                                  <td><?= get_the_title($row->PostID) ?></td>
                                  <td><?= substr($row->Authority,strlen($row->Authority)-10); ?></td>
                                  <td><?= $date->date("l j F Y - H:i" , strtotime($row->InputDate)); ?></td>
                              </tr>
							<?php
						  }
						  ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>

          <!--   Pagination   -->
		<?php if ( $page_links ): ?>
            <div class="tablenav paginate"><div class="tablenav-pages" style="margin: 1em 0"><?= $page_links; ?></div></div>
		<?php endif; ?>
          <br class="clear" />
          <!--   Pagination   -->
      </div>
	<?php
  }
}

