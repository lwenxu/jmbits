<?php
require_once("include/bittorrent.php");
dbconn();

//$langid = 0 + $_GET['sitelanguage'];
//if ($langid)
//{
//	$lang_folder = validlang($langid);
//	if(get_langfolder_cookie() != $lang_folder)
//	{
//		set_langfolder_cookie($lang_folder);
//		header("Location: " . $_SERVER['PHP_SELF']);
//	}
//}
require_once(get_langfile_path("", false, $CURLANGDIR));

failedloginscheck ();
cur_user_check () ;
//stdhead($lang_login['head_login']);
login_head();


//$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";

//$langs = langlist("site_lang");

//foreach ($langs as $row)
//{
//	if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = "selected=\"selected\""; else $se = "";
//	$s .= "<option value=\"". $row["id"] ."\" ". $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
//}
//$s .= "\n</select>";
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>" xmlns="http://www.w3.org/1999/html">
<?php
//print("<div align=\"right\">".$lang_login['text_select_lang']. $s . "</div>");
?>
</form>
<?php

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
//	if (!$_GET["nowarn"]) {
//		print("<h1>" . $lang_login['h1_not_logged_in']. "</h1>\n");
//		print("<p><b>" . $lang_login['p_error']. "</b> " . $lang_login['p_after_logged_in']. "</p>\n");
//	}
}
?>

	<div class="logo">
		<a href="index.html">
			<img src="./styles/BambooGreen/logo-big.png" alt=""> </a>
	</div>
	<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" method="post" action="takelogin.php">
		<h3 class="form-title font-green">登 录</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span> Enter any username and password. </span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<input style="width: 100%" class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off"
			       placeholder="Username" name="username"/></div>
		<div class="form-group" style="margin-top: 10px">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<input style="width: 100%" class="form-control form-control-solid placeholder-no-fix " type="password" autocomplete="off"
			       placeholder="Password" name="password"/></div>
		<div class="form-actions">
			<button type="submit" class="btn green uppercase">登录</button>
			<label class="rememberme check mt-checkbox mt-checkbox-outline">
				<input type="checkbox" name="remember" value="1"/>记住密码
				<span></span>
			</label>
			<a href="recover.php" id="forget-password" class="forget-password">忘记密码?</a>
		</div>

		<div class="create-account">
			<p>
				<a href="signup.php" id="register-btn" class="uppercase">创建新账户</a>
			</p>
		</div>
	</form>








<!--<form method="post" action="takelogin.php">-->
<!--<p>--><?php //echo "<span style='font-size: 17px;color: tomato'>".$lang_login['p_warning']."</span><span style='color: #00a2d4;font-size: 16px'>".$maxloginattempts."</span>";?><!-- --><?php //echo "<sapn style='font-size: 17px'>".$lang_login['p_fail_ban']."</sapn>"?><!--</p>-->
<!--<p>--><?php //echo "<span style='font-size: 16px'>".$lang_login['p_you_have']?><!-- <span style="color: #00c500">--><?php //echo remaining ();?><!--</span> --><?php //echo $lang_login['p_remaining_tries']?><!--</span></p>-->

<!--login	tables  here-->



<!--<table border="0" cellpadding="5">-->
<!--<tr><td class="rowhead">--><?php //echo $lang_login['rowhead_username']?><!--</td><td class="rowfollow" align="left"><input type="text" name="username" style="width: 180px; border: 1px solid gray" /></td></tr>-->
<!--<tr><td class="rowhead">--><?php //echo $lang_login['rowhead_password']?><!--</td><td class="rowfollow" align="left"><input type="password" name="password" style="width: 180px; border: 1px solid gray"/></td></tr>-->
<?php
//show_image_code ();
//if ($securelogin == "yes")
//	$sec = "checked=\"checked\" disabled=\"disabled\"";
//elseif ($securelogin == "no")
//	$sec = "disabled=\"disabled\"";
//elseif ($securelogin == "op")
//	$sec = "";
//
//if ($securetracker == "yes")
//	$sectra = "checked=\"checked\" disabled=\"disabled\"";
//elseif ($securetracker == "no")
//	$sectra = "disabled=\"disabled\"";
//elseif ($securetracker == "op")
//	$sectra = "";
//?>
<!--<tr><td class="toolbox" colspan="2" align="left">--><?php //echo $lang_login['text_advanced_options']?><!--</td></tr>-->
<!--<tr><td class="rowhead">--><?php //echo $lang_login['text_auto_logout']?><!--</td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="logout" value="yes" />--><?php //echo $lang_login['checkbox_auto_logout']?><!--</td></tr>-->
<!--<tr><td class="rowhead">--><?php //echo $lang_login['text_restrict_ip']?><!--</td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="securelogin" value="yes" />--><?php //echo $lang_login['checkbox_restrict_ip']?><!--</td></tr>-->
<!--<tr><td class="rowhead">--><?php //echo $lang_login['text_ssl']?><!--</td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="ssl" value="yes" --><?php //echo $sec?><!-- />--><?php //echo $lang_login['checkbox_ssl']?><!--<br /><input class="checkbox" type="checkbox" name="trackerssl" value="yes" --><?php //echo $sectra?><!-- />--><?php //echo $lang_login['checkbox_ssl_tracker']?><!--</td></tr>-->
<!--<tr><td class="toolbox" colspan="2" align="right"><input type="submit" value="--><?php //echo $lang_login['button_login']?><!--" class="btn" /> <input type="reset" value="--><?php //echo $lang_login['button_reset']?><!--" class="btn" /></td></tr>-->
<!--</table>-->
<?php
//
//if (isset($returnto))
//	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");
//
//?>
<!--</form>-->
<!--<p>--><?php //echo $lang_login['p_no_account_signup']?><!--</p>-->
<?php
//if ($smtptype != 'none'){
//?>
<!--<p>--><?php //echo $lang_login['p_forget_pass_recover']?><!--</p>-->
<!--<p>--><?php //echo $lang_login['p_resend_confirm']?><!--</p>-->
<?php
//}
//if ($showhelpbox_main != 'no'){?>
<!--<table width="700" class="main" border="0" cellspacing="0" cellpadding="0"><tr><td class="embedded">-->
<!--<h2>--><?php //echo $lang_login['text_helpbox'] ?><!--<font class="small"> - --><?php //echo $lang_login['text_helpbox_note'] ?><!--<font id= "waittime" color="red"></font></h2>-->
<?php
//print("<table width='100%' border='1' cellspacing='0' cellpadding='1'><tr><td class=\"text\">\n");
//print("<iframe src='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php?type=helpbox' width='650' height='180' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
//print("<form action='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php' id='helpbox' method='get' target='sbox' name='shbox'>\n");
//print($lang_login['text_message']."<input type='text' id=\"hbtext\" name='shbox_text' autocomplete='off' style='width: 500px; border: 1px solid gray' ><input type='submit' id='hbsubmit' class='btn' name='shout' value=\"".$lang_login['sumbit_shout']."\" /><input type='reset' class='btn' value=".$lang_login['submit_clear']." /> <input type='hidden' name='sent' value='yes'><input type='hidden' name='type' value='helpbox' />\n");
//print("<div id=sbword style=\"display: none\">".$lang_login['sumbit_shout']."</div>");
//print(smile_row("shbox","shbox_text"));
//print("</td></tr></table></form></td></tr></table>");
//}
stdfoot();
