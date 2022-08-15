<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function sisoog_donate_activate_function()
{
  global $wpdb;
  $DonateTable = $wpdb->prefix . TABLE_DONATE;
  $MercahnIdsTable = $wpdb->prefix . TABLE_MERCHANTS_IDS;

  $mainTable = "CREATE TABLE IF NOT EXISTS `$DonateTable` (
					  `DonateID` int(11) NOT NULL AUTO_INCREMENT,
					  `Authority` varchar(50) NOT NULL,
					  `Name` varchar(50) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
					  `AmountTomaan` int(11) NOT NULL,
					  `Mobile` varchar(11) ,
					  `Email` varchar(50),
					  `InputDate` varchar(20),
					  `Description` varchar(100) CHARACTER SET utf8 COLLATE utf8_persian_ci,
					  `Status` varchar(5),
					  `Author` varchar(55),
					  `PostID` varchar(20),
					  `payment_gateway` varchar(20),
					  `author_id` int(11),
					  PRIMARY KEY (`DonateID`),
					  KEY `DonateID` (`DonateID`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

  $merchantidsTable = "CREATE TABLE `$MercahnIdsTable` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `merchant_id` varchar(255) COLLATE utf8_persian_ci NOT NULL,
					  `user_id` int(11) NOT NULL,
					  `payment_gateway` varchar(25) COLLATE utf8_persian_ci NOT NULL,
					  `date` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci";

  dbDelta($mainTable);
  dbDelta($merchantidsTable);
  // other options
  add_option("sisoogDonate_TotalAmount", 0, '', 'yes');
  add_option("sisoogDonate_TotalPayment", 0, '', 'yes');
  add_option("sisoogDonate_IsOK", 'با تشکر پرداخت شما به درستی انجام شد.', '', 'yes');
  add_option("sisoogDonate_IsError", 'متاسفانه پرداخت انجام نشد.', '', 'yes');

  $style = '#sisoogDonate_MainForm {
  width: 400px;
  height: auto;
  margin: 0 auto;
  direction: rtl;$
}

#sisoogDonate_Form {
  width: 96%;
  height: auto;
  float: right;
  padding: 10px 2%;
}

#sisoogDonate_Message,#sisoogDonate_Error {
  width: 90%;
  margin-top: 10px;
  margin-right: 2%;
  float: right;
  padding: 5px 2%;
  border-right: 2px solid #006704;
  background-color: #e7ffc5;
  color: #00581f;
}

#sisoogDonate_Error {
  border-right: 2px solid #790000;
  background-color: #ffc9c5;
  color: #580a00;
}

.sisoogDonate_FormItem {
  width: 90%;
  margin-top: 10px;
  margin-right: 2%;
  float: right;
  padding: 5px 2%;
}

.sisoogDonate_FormLabel {
  width: 35%;
  float: right;
  padding: 3px 0;
}

.sisoogDonate_ItemInput {
  width: 64%;
  float: left;
}

.sisoogDonate_ItemInput input {
  width: 90%;
  float: right;
  border-radius: 3px;
  box-shadow: 0 0 2px #00c4ff;
  border: 0px solid #c0fff0;
  font-family: inherit;
  font-size: inherit;
  padding: 3px 5px;
}

.sisoogDonate_ItemInput input:focus {
  box-shadow: 0 0 4px #0099d1;
}

.sisoogDonate_ItemInput input.error {
  box-shadow: 0 0 4px #ef0d1e;
}

input.sisoogDonate_Submit {
  background: none repeat scroll 0 0 #2ea2cc;
  border-color: #0074a2;
  box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15);
  color: #fff;
  text-decoration: none;
  border-radius: 3px;
  border-style: solid;
  border-width: 1px;
  box-sizing: border-box;
  cursor: pointer;
  display: inline-block;
  font-size: 13px;
  line-height: 26px;
  margin: 0;
  padding: 0 10px 1px;
  margin: 10px auto;
  width: 50%;
  font: inherit;
  float: right;
  margin-right: 24%;
}';
  add_option("sisoogDonate_CustomStyle", $style, '', 'yes');
  add_option("sisoogDonate_UseCustomStyle", 'false', '', 'yes');
}

function sisoog_donate_deactivate_function()
{
  flush_rewrite_rules();
}