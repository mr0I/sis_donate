<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


global $wpdb;
$configs = include_once(ROOT_PATH. 'config.php');
include_once(INC_DIR . 'functions.php');


$donateData = $_GET['donate_data'];
$reqData = getRequest($donateData ,$configs['DONATE_KEY']);
$author_id = $reqData['user_id'];
$display_name = $reqData['user_name'];
$pid = $reqData['post_id'];


$usersTable = $wpdb->prefix . 'users';
$author = $wpdb->get_results( "SELECT * FROM $usersTable WHERE ID='$author_id' AND display_name='$display_name' ");
$author_name = (sizeof($author) !== 0) ? get_the_author_meta( 'display_name', $author_id ) : '';

$current_user = new stdClass();
if ( is_user_logged_in() ) {
  $current_user = wp_get_current_user();
  $name = $current_user->display_name;
  $email = $current_user->user_email;
  $phone = get_user_meta($current_user->ID,'billing_phone',true);
}

$same_user_msg = '';
if($current_user->ID === $author_id){
  $merchantsTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;
  $author_posts = $wpdb->get_var( "SELECT COUNT(`id`) FROM $merchantsTable WHERE user_id='$author_id' ");
  $same_user_msg = ($author_posts === '0') ?  '<div class="alert alert-danger same_user_msg mt-3" role="alert">شما هنوز  <a href="'.admin_url().'admin.php?page=payPingDonate_authorsMerchantId" class="alert-link" target="_blank">کد درگاه پرداخت</a> خود را ثبت نکرده اید!</div>' : '';
}
?>


<div class="container-fluid">
    <div style="clear:both;width:100%;height: 100vh;">
        <div id="sisoogDonate_MainForm">
            <div class="sisoogDonate_FormTitle">
                <h4 class="">شما در حال حمایت از <span><?= $author_name; ?></span>  هستید. </h4>
                <div><?= $same_user_msg; ?></div>
            </div>
            <div id="sisoogDonate_Form">
                <div class="col-lg-5 col-md-5 col-sm-12">
                    <form method="post" id="add_donate_frm" name="add_donate_frm">
                        <div class="sisoogDonate_FormItem required">
                            <label class="sisoogDonate_FormLabel">نام شما :</label>
                            <div class="sisoogDonate_ItemInput">
                                <input type="text" id="sisoogDonate_Name_Input" name="name" value="<?= $name ?>" />
                            </div>
                        </div>
                        <div class="sisoogDonate_FormItem">
                            <label class="sisoogDonate_FormLabel">تلفن همراه :</label>
                            <div class="sisoogDonate_ItemInput"><input type="text" name="mobile" value="<?= $phone ?>" /></div>
                        </div>
                        <div class="sisoogDonate_FormItem">
                            <label class="sisoogDonate_FormLabel">ایمیل :</label>
                            <div class="sisoogDonate_ItemInput"><input type="text" name="email" style="direction:ltr;text-align:left;" value="<?= $email ?>" /></div>
                        </div>
                        <div class="sisoogDonate_FormItem">
                            <label class="sisoogDonate_FormLabel">توضیحات :</label>
                            <div class="sisoogDonate_ItemInput"><input type="text" name="desc" value="<?= $description ?>" /></div>
                        </div>
                        <div class="sisoogDonate_FormItem required">
                            <label class="sisoogDonate_FormLabel">مبلغ</label>
                            <div class="sisoogDonate_ItemInput">
                                <select name="amount" id="sisoogDonate_Amount_Select">
                                    <option value="0">---</option>
                                    <option value="10000">10000 تومان</option>
                                    <option value="20000">20000 تومان</option>
                                    <option value="50000">50000 تومان</option>
                                    <option value="100000">100000 تومان</option>
                                    <option value="others">سایر مبالغ</option>
                                </select>
                                <input style="width:60%" type="text" name="sisoogDonate_Amount" id="sisoogDonate_Amount_Input" placeholder="مبلغ دلخواهتان را وارد کنید..."
                                       value="<?= $amount ?>" onkeyup="this.value = this.value.replace(/[^\d]+/g, \'\');" />
                                <span style="margin-right:10px;display: none;"><?= $sisoogDonate_Unit ?></span>
                            </div>
                        </div>

                        <input type="hidden" value="<?= $author_id ?>" name="author_id">
                        <input type="hidden" value="<?= $display_name ?>" name="user_name">
                        <input type="hidden" value="<?= $pid ?>" name="post_id">
                        <input type="hidden" value="<?= $donateData; ?>" name="donate_data">
                        <input type="hidden" value="<?= wp_create_nonce('donate-frm-nonce') ?>" id="donate_frm_nonce">

                        <div class="sisoogDonate_FormItem">
                            <input type="submit" class="sisoogDonate_Submit" name="submit" value="پرداخت" disabled />
                        </div>
                    </form>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-12">
                    <figure>
                        <img src="<?= ASSETSDIR . 'images/donate.jpg'; ?>">
                    </figure>
                </div>
            </div>
        </div>