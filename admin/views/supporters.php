<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Convert Date To Shamsi
require_once LIBDIR . '/jdate/jdatetime.class.php';
$date = new jDateTime(true, true, 'Asia/Tehran');
$configs = include_once(ROOT_PATH. 'config.php');
include_once(INC_DIR . 'functions.php');

global $wpdb;
$DonateTable = $wpdb->prefix . TABLE_DONATE;
$all_donates = $wpdb->get_var( "SELECT COUNT(*) FROM $DonateTable");
$ok_donates = $wpdb->get_var( "SELECT COUNT(*) FROM $DonateTable WHERE Status='OK' ");

$page_num = isset( $_GET['page_num'] ) ? absint( $_GET['page_num'] ) : 1;
$limit = $configs['PAGINATE_NUM'] ? $configs['PAGINATE_NUM'] : 10 ;
$offset = ( $page_num - 1 ) * $limit;

$condition = new stdClass();
if (isset($_GET['s']) && $_GET['s'] !== '') {
  $search = sanitizeInput( $_GET['s'] );
  $condition->like = "Author LIKE '%$search%' ";
}
if (isset($_GET['sort_by'])) {
  $sort = $_GET['sort_by'];
  switch ($sort){
	case 'completed';
	  $condition->status = "Status='OK'";
	  break;
	case 'not_completed';
	  $condition->status = "Status<>'OK'";
	  break;
	default:
	  break;
  }
}

$where = '';
$where .= $condition->like;
$where .= $condition->like && $condition->status ? 'AND ' : '';
$where .= $condition->status;

if ($where !== ''){
  $result = $wpdb->get_results( "SELECT * FROM {$DonateTable} WHERE {$where} ORDER BY InputDate DESC LIMIT $offset,$limit " );
  $total = $wpdb->get_var( "SELECT COUNT(*) FROM {$DonateTable} WHERE {$where} " );
} else {
  $result = $wpdb->get_results( "SELECT * FROM {$DonateTable} ORDER BY InputDate DESC LIMIT $offset,$limit " );
  $total = $wpdb->get_var( "SELECT COUNT(*) FROM {$DonateTable} " );
}

$page_links = getPageLinks($total,$limit,$page_num);
?>
<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br /></div><h2>حامیان مالی</h2>
    <form id="search_author_frm" action="<?= GetCallBackURL(); ?>" method="post">
        <p class="search-box">
            <label class="screen-reader-text" for="search_author_input">جست‌وجوی نویسنده:</label>
            <input type="search" id="search_author_input" name="s" value="<?= $_GET['s'] ?>" />
            <button type="submit" id="search_author_submit" class="btn btn-primary mb-2" style="cursor: pointer;">جست‌وجوی نویسنده</button>
        </p>
    </form>
</div>

<form class="form-inline">
    <input type="hidden" name="donate_select_nonce" id="donate_select_nonce" value="<?php echo wp_create_nonce('donate-select-nonce'); ?>" >
    <div class="form-group mx-sm-3 mb-2" style="display: flex;justify-content: space-between">
        <div class="form-group-item">
            <ul class="subsubsub">
                <li class="all"><a href="admin.php?page=sisoogDonate_supporters&page_num=1" class="<?php if($_GET['sort_by']=='') echo 'current'; ?>" aria-current="page">همه <span class="count">(<?= $all_donates; ?>)</span></a> |</li>
                <li class="completed"><a href="admin.php?page=sisoogDonate_supporters&sort_by=completed&page_num=1" class="<?php if($_GET['sort_by']=='completed') echo 'current'; ?>">تکمیل شده <span class="count">(<?= $ok_donates ?>)</span></a> |</li>
                <li class="not_completed"><a href="admin.php?page=sisoogDonate_supporters&sort_by=not_completed&page_num=1" class="<?php if($_GET['sort_by']=='not_completed') echo 'current'; ?>">تکمیل نشده <span class="count">(<?= $all_donates - $ok_donates ?>)</span></a></li>
            </ul>
        </div>
    </div>
</form>

<table class="wp-list-table widefat posts" cellspacing="0">
    <thead>
    <tr>
        <th>#</th>
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
	<?php if (empty($result)): ?>
        <tr><td colspan="10">رکوردی یافت نشد!</td></tr>
	<?php else: ?>
	  <?php $counter=1;foreach($result as $row): ?>
            <tr id="post-109" style="<?php if($row->Status == 'OK') echo 'background-color: #cfc'; ?> <?php if($row->Status != 'OK') echo 'background-color: #FBE9E7'; ?>" class="post-109 type-post status-draft format-standard hentry category-news alternate iedit author-self" valign="top">
                <th><?= $counter++; ?></th>
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
	  <?php endforeach; ?>
	<?php endif; ?>
    </tbody>
</table>

<?php if ( $page_links ): ?>
    <div class="tablenav paginate"><div class="tablenav-pages" style="margin: 1em 0"><?= $page_links; ?></div></div>
<?php endif; ?>


<div id="ajax-response"></div>
<br class="clear" />


