<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



global $wpdb;
$configs = include(ROOT_PATH . 'config.php');
$configs = include(INC_DIR . 'functions.php');


$reqData = getRequest($_GET['donate_data'],$configs['DONATE_KEY']);
$user_id = $reqData['user_id'];
$display_name = $reqData['user_name'];
$pid = $reqData['post_id'];


$name = 'ali';
$mobile = '09323232323';
$email = 'ali@sdsada.ir';
$description = 'desc';
$amount = '13323';
$sisoogDonate_Unit = 'TOMAN';

var_dump($reqData);
?>

<div class="container-fluid">
    <div style="clear:both;width:100%;height: 100vh;">
        <div id="sisoogDonate_MainForm">
            <div class="sisoogDonate_FormTitle">
                <h4 class="">شما در حال حمایت از <span>'.$author_name.'</span>  هستید. </h4>
                <div>'.$same_user_msg.'</div>
            </div>
            <div id="sisoogDonate_Form">
                <div class="col-lg-5 col-md-5 col-sm-12"><form method="post" id="add_donate_frm">
                        <div class="sisoogDonate_FormItem required">
                            <label class="sisoogDonate_FormLabel">نام شما :</label>
                            <div class="sisoogDonate_ItemInput">
                                <input type="text" id="sisoogDonate_Name_Input" name="sisoogDonate_Name" value="<?= $name ?>" />
                            </div>
                        </div>
                        <div class="sisoogDonate_FormItem">
                            <label class="sisoogDonate_FormLabel">تلفن همراه :</label>
                            <div class="sisoogDonate_ItemInput"><input type="text" name="mobile" value="<?= $mobile ?>" /></div>
                        </div>
                        <div class="sisoogDonate_FormItem">
                            <label class="sisoogDonate_FormLabel">ایمیل :</label>
                            <div class="sisoogDonate_ItemInput"><input type="text" name="email" style="direction:ltr;text-align:left;" value="<?= $email ?>" /></div>
                        </div>
                        <div class="sisoogDonate_FormItem">
                            <label class="sisoogDonate_FormLabel">توضیحات :</label>
                            <div class="sisoogDonate_ItemInput"><input type="text" name="sisoogDonate_Description" value="<?= $description ?>" /></div>
                        </div>
                        <div class="sisoogDonate_FormItem required">
                            <label class="sisoogDonate_FormLabel">مبلغ</label>
                            <div class="sisoogDonate_ItemInput">
                                <select name="sisoogDonate_Amount" id="sisoogDonate_Amount_Select">
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

                        <input type="hidden" value="<?= $user_id ?>" name="author_id">
                        <input type="hidden" value="<?= $display_name ?>" name="user_name">
                        <input type="hidden" value="<?= $pid ?>" name="post_id">

                        <div class="sisoogDonate_FormItem">
                            <input type="submit" name="submit" value="پرداخت" class="sisoogDonate_Submit" disabled />
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