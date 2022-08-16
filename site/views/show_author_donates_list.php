<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>

<?php
// Convert Date To Shamsi
require_once LIBDIR . '/jdate/jdatetime.class.php';
$date = new jDateTime(true, true, 'Asia/Tehran');
$configs = include_once(ROOT_PATH . 'config.php');
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
  $all_donates = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Author='$Name' ORDER BY DonateID DESC ");


  if(isset($_REQUEST['page_num']))
  {
	$page = htmlspecialchars(strip_tags(trim($_REQUEST['page_num'])), ENT_QUOTES);
	$lim = $configs['PAGINATE_NUM'];
	$offset = --$page * ($configs['PAGINATE_NUM']);
	$LIMIT = " LIMIT $lim OFFSET $offset";
  }

  $user_donates = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Author='$Name' AND Status='OK' ORDER BY DonateID DESC $LIMIT ");
  $total_donates = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Author='$Name' AND Status='OK' ");
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
                                  <td><?= intval($row->Authority); ?></td>
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
          <div class="actions paginate_btns">
			<?php
			$total = count($total_donates);

			$PageNumInt = 1;
			if($total > 0)
			{
			  $PagesNum = $total / $configs['PAGINATE_NUM'];
			  $PageNumInt = intval($PagesNum);
			  if($PageNumInt < $PagesNum)
				$PageNumInt++;
			}

			$currentPage = 1;
			if(isset($_GET['page_num'])){
			  $currentPage = htmlspecialchars(strip_tags(trim($_GET['page_num'])), ENT_QUOTES);
			}
			?>
			<?php
			if (!isset($_GET['sort_by']))  $_GET['sort_by'] = 'all';
			for($i = 1 ; $i <= $PageNumInt; $i++)
			{
			  if($i == $currentPage)
				echo '<a href="?page=authorslist&page_num='. $i .' &sort_by='.$_GET['sort_by'].' "  class="first-page active">'. $i .'</a>';
			  else
				echo '<a href="?page=authorslist&page_num='. $i .' &sort_by='.$_GET['sort_by'].' "  class="first-page">'. $i .'</a>';
			}
			?>
          </div>
          <br class="clear" />
          <!--   Pagination   -->
      </div>
	<?php
  }
}

