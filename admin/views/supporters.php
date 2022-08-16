<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


global $wpdb;
$DonateTable = $wpdb->prefix . TABLE_DONATE;
$all_donates = $wpdb->get_results( "SELECT * FROM $DonateTable ORDER BY DonateID DESC ");
$ok_donates = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Status='OK' ORDER BY DonateID DESC ");

// Convert Date To Shamsi
require_once LIBDIR . '/jdate/jdatetime.class.php';
$date = new jDateTime(true, true, 'Asia/Tehran');
$configs = include(ROOT_PATH . 'config.php');
include_once(INC_DIR . 'functions.php');
?>
  <div class="wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div><h2>حامیان مالی</h2>
	<form id="posts-filter" action="<?= GetCallBackURL(); ?>" method="post">
	  <p class="search-box">
		<label class="screen-reader-text" for="post-search-input">جست‌وجوی نویسنده:</label>
		<input type="search" id="post-search-input" name="searchbyname" value="" />
		<button type="submit" id="search-submit" class="btn btn-primary mb-2" style="cursor: pointer;">جست‌وجوی نویسنده</button>
	  </p>
	</form>
  </div>

  <form class="form-inline">
	<input type="hidden" name="donate_select_nonce" id="donate_select_nonce" value="<?php echo wp_create_nonce('donate-select-nonce'); ?>" >
	<div class="form-group mx-sm-3 mb-2" style="display: flex;justify-content: space-between">
	  <div class="form-group-item">
		<ul class="subsubsub">
		  <li class="all"><a href="admin.php?page=sisoogDonate_supporters&pageid=1" class="<?php if($_GET['sort_by']=='') echo 'current'; ?>" aria-current="page">همه <span class="count">(<?= count($all_donates); ?>)</span></a> |</li>
		  <li class="completed"><a href="admin.php?page=sisoogDonate_supporters&sort_by=competed&pageid=1" class="<?php if($_GET['sort_by']=='competed') echo 'current'; ?>">تکمیل شده <span class="count">(<?= count($ok_donates) ?>)</span></a> |</li>
		  <li class="not_completed"><a href="admin.php?page=sisoogDonate_supporters&sort_by=not_competed&pageid=1" class="<?php if($_GET['sort_by']=='not_competed') echo 'current'; ?>">تکمیل نشده <span class="count">(<?= count($all_donates) - count($ok_donates) ?>)</span></a></li>
		</ul>
	  </div>
	</div>
  </form>

  <table class="wp-list-table widefat posts" cellspacing="0">
	<thead>
	<tr>
	  <th scope='col' id='cb' class='manage-column column-cb check-column'  style=""><label class="screen-reader-text" for="cb-select-all-1">گزینش همه</label><input id="cb-select-all-1" type="checkbox" /></th>
	  <th scope='col' id='title' class='manage-column column-title'  style="">
		<span>نام و نام خانوادگی</span><span class="sorting-indicator"></span>
	  </th>
	  <th scope='col' id='author' class='manage-column column-author'  style="">مبلغ (<?= get_option('sisoogDonate_Unit') ?>)</th>
	  <th scope='col' id='categories' class='manage-column column-categories'  style="">موبایل</th>
	  <th scope='col' id='tags' class='manage-column column-tags'  style="">ایمیل</th>
	  <th scope='col' id='comments' class='manage-column column-tags'  style="">توضیحات</th>
	  <th scope='col' id='followup' class='manage-column column-tags'  style="">شماره پیگیری</th>
	  <th scope='col' id='author' class='manage-column column-tags'  style="">نویسنده</th>
	  <th scope='col' id='date' class='manage-column column-date'  style=""><span>تاریخ</span><span class="sorting-indicator"></span></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	  <th scope='col' id='cb' class='manage-column column-cb check-column'  style=""><label class="screen-reader-text" for="cb-select-all-1">گزینش همه</label><input id="cb-select-all-1" type="checkbox" /></th>
	  <th scope='col' id='title' class='manage-column column-title'  style="">
		<span>نام و نام خانوادگی</span><span class="sorting-indicator"></span>
	  </th>
	  <th scope='col' id='author' class='manage-column column-author'  style="">مبلغ (تومان)</th>
	  <th scope='col' id='categories' class='manage-column column-categories'  style="">موبایل</th>
	  <th scope='col' id='tags' class='manage-column column-tags'  style="">ایمیل</th>
	  <th scope='col' id='comments' class='manage-column column-tags'  style="">توضیحات</th>
	  <th scope='col' id='followup' class='manage-column column-tags'  style="">شماره پیگیری</th>
	  <th scope='col' id='author' class='manage-column column-tags'  style="">نویسنده</th>
	  <th scope='col' id='date' class='manage-column column-date'  style=""><span>تاریخ</span><span class="sorting-indicator"></span></th>
	</tr>
	</tfoot>

	<tbody id="the-list">
	<?php
	//////////// Page ////////////
	$LIMIT = '';
	if(isset($_GET['pageid']))
	{
	  $page = htmlspecialchars(strip_tags(trim($_GET['pageid'])), ENT_QUOTES);
	  $lim = $configs['PAGINATE_NUM'];
	  $offset = --$page * ($configs['PAGINATE_NUM']);
	  $LIMIT = " LIMIT $lim OFFSET $offset";
	}

	if(isset($_REQUEST['searchbyname']) && $_REQUEST['searchbyname'] != '')
	{
	  $SearchName = htmlspecialchars(strip_tags(trim($_REQUEST['searchbyname'])), ENT_QUOTES);
	  $result = $wpdb->get_results( "SELECT * FROM `$DonateTable` WHERE `Author` LIKE '%$SearchName%' ORDER BY DonateID DESC ". $LIMIT);
	  $total = $wpdb->get_results( "SELECT * FROM $DonateTable  ");
	}
	elseif (isset($_GET['sort_by'])){
	  $sort = $_GET['sort_by'];
	  switch ($sort){
		case 'competed';
		  $result = $wpdb->get_results( "SELECT * FROM `$DonateTable` WHERE Status='OK' ORDER BY DonateID DESC ". $LIMIT);
		  $total = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Status='OK' ");
		  break;
		case 'not_competed';
		  $result = $wpdb->get_results( "SELECT * FROM `$DonateTable` WHERE Status<>'OK' ORDER BY DonateID DESC ". $LIMIT);
		  $total = $wpdb->get_results( "SELECT * FROM $DonateTable WHERE Status<>'OK' ");
		  break;
		default:
		  $result = $wpdb->get_results( "SELECT * FROM `$DonateTable` ORDER BY DonateID DESC ". $LIMIT);
		  $total = $wpdb->get_results( "SELECT * FROM $DonateTable ");
	  }
	}
	else
	{
	  $result = $wpdb->get_results( "SELECT * FROM `$DonateTable` ORDER BY DonateID DESC ".  $LIMIT);
	  $total = $wpdb->get_results( "SELECT * FROM $DonateTable ");
	}


	foreach($result as $row) :
	  ?>
	  <tr id="post-109" style="<?php if($row->Status == 'OK') echo 'background-color: #cfc'; ?> <?php if($row->Status != 'OK') echo 'background-color: #FBE9E7'; ?>" class="post-109 type-post status-draft format-standard hentry category-news alternate iedit author-self" valign="top">
		<th scope="row" class="check-column">
		  <label class="screen-reader-text" for="cb-select-109">گزینش رکورد</label>
		  <input id="cb-select-109" type="checkbox" name="chkDonates" value="<?= $row->DonateID ?>" />
		  <div class="locked-indicator"></div>
		</th>
		<td class="post-title page-title column-title"><strong><?php echo $row->Name; ?></strong> <small><?php echo $row->Status; ?></small>
		  <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
		</td>
		<td class="author column-author digits"><?php echo $row->AmountTomaan; ?></td>
		<td class="categories column-categories"><?php echo $row->Mobile; ?></td>
		<td class="tags column-tags"><?php echo $row->Email; ?></td>
		<td class="tags column-tags"><?php echo $row->Description; ?></td>
		<td class="tags column-tags"><?= intval($row->Authority); ?></td>
		<td class="author column-author" style="display: flex; justify-content: flex-start; align-items: center;">
		  <?php echo $row->Author; ?>
		  <?php $AuthorEmail = $wpdb->get_results( "SELECT user_email FROM $wpdb->users WHERE display_name='$row->Author' "); ?>
		  <figure>
			<img src="http://www.gravatar.com/avatar/<?= md5($AuthorEmail[0]->user_email); ?>?rating=PG&size=24&size=50&d=identicon"
				 style="width: 30px;height: auto;" alt="تصویر نویسنده">
		  </figure>
		</td>
		<td class="date column-date"><?= $date->date("l j F Y - H:i" , strtotime($row->InputDate)); ?></td>
	  </tr>
	<?php
	endforeach;
	?>
	</tbody>
  </table>

  <div class="tablenav bottom">
	<div class="alignright actions paginate_btns">
	  <?php
	  $totalPay = count($total);

	  $PageNumInt = 1;
	  if($totalPay > 0)
	  {
		$PagesNum = $totalPay / $configs['PAGINATE_NUM'];
		$PageNumInt = intval($PagesNum);
		if($PageNumInt < $PagesNum)
		  $PageNumInt++;
	  }

	  $currentPage = 1;
	  if(isset($_REQUEST['pageid']))
		$currentPage = htmlspecialchars(strip_tags(trim($_REQUEST['pageid'])), ENT_QUOTES);
	  ?>
	  <?php
	  if (!isset($_GET['sort_by']))  $_GET['sort_by'] = '';
	  for($i = 1 ; $i <= $PageNumInt; $i++)
	  {
		if($i == $currentPage)
		  echo '<a href="admin.php?page=sisoogDonate_supporters&pageid='. $i .' &sort_by='.$_GET['sort_by'].' "  class="first-page active">'. $i .'</a>';
		else
		  echo '<a href="admin.php?page=sisoogDonate_supporters&pageid='. $i . ' &sort_by='.$_GET['sort_by'].' "  class="first-page">'. $i .'</a>';
	  }
	  ?>
	</div>
	<br class="clear" />
  </div>

  <div id="ajax-response"></div>
  <br class="clear" />


