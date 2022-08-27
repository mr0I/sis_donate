<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


global $wpdb;
$configs = include_once(ROOT_PATH. 'config.php');
include_once(INC_DIR . 'functions.php');


$donateData = $_GET['donate_data'];
$reqData = getRequest($donateData ,$configs['DONATE_KEY']);
$author_id = $reqData['user_id'];
$display_name = $reqData['user_name'];
$pid = $reqData['post_id'];
$postUrl = get_permalink($pid);

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
?>


<div class="container-fluid">
    <div>
        <div id="sisoogDonate_MainForm">
            <div class="sisoogDonate_FormTitle">
			  <?php if (!$author_name):
				echo '<div class="alert alert-danger text-center" role="alert">
                  <h6 class="alert-heading">خطای دسترسی!</h6>
                </div>';
				exit();
				?>
			  <?php else: ?>
                  <h4>شما در حال حمایت از <span><?= $author_name; ?></span>  هستید. </h4>
			  <?php endif; ?>
            </div>
            <div class="m-auto" id="sisoogDonate_Form">
                <div class="col-12">
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
                                    <option value="other_prices">سایر مبالغ</option>
                                </select>
                                <input style="width:60%" type="text" name="input_amount" id="sisoogDonate_Amount_Input"
                                       placeholder="مبلغ دلخواهتان را وارد کنید..." value="<?= $amount ?>"
                                       onkeyup="this.value = this.value.replace(/[^\d]+/g, '');" />
                                <span style="margin-right:10px;display: none;"><?= $sisoogDonate_Unit ?></span>
                            </div>
                        </div>

                        <input type="hidden" value="<?= $author_id ?>" name="author_id">
                        <input type="hidden" value="<?= $display_name ?>" name="user_name">
                        <input type="hidden" value="<?= $pid ?>" name="post_id">
                        <input type="hidden" value="<?= $postUrl ?>" name="post_url">
                        <input type="hidden" value="<?= $donateData; ?>" name="donate_data">
                        <input type="hidden" value="<?= wp_create_nonce('donate-frm-nonce') ?>" id="donate_frm_nonce">

                        <div class="sisoogDonate_FormItem">
                            <button class="sisoogDonate_Submit" name="submit" disabled>
                                پرداخت
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>