<?php
require_once("include/bittorrent.php");
dbconn();

$langid = 0 + $_GET['sitelanguage'];
if ($langid)
{
	$lang_folder = validlang($langid);
	if(get_langfolder_cookie() != $lang_folder)
	{
		set_langfolder_cookie($lang_folder);
		header("Location: " . $_SERVER['REQUEST_URI']);
	}
}
require_once(get_langfile_path("", false, $CURLANGDIR));
cur_user_check ();
$type = $_GET['type'];
if ($type == 'invite')
{
	registration_check();
	failedloginscheck ("Invite signup");
	$code = $_GET["invitenumber"];

	$nuIP = getip();
	$dom = @gethostbyaddr($nuIP);
	if ($dom == $nuIP || @gethostbyname($dom) != $nuIP)
	$dom = "";
	else
	{
	$dom = strtoupper($dom);
	preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm);
	$dom = $tldm[2];
	}

	$sq = sprintf("SELECT inviter FROM invites WHERE hash ='%s'",mysql_real_escape_string($code));
	$res = sql_query($sq) or sqlerr(__FILE__, __LINE__);
	$inv = mysql_fetch_assoc($res);
	$inviter = htmlspecialchars($inv["inviter"]);
	if (!$inv)
		stderr($lang_signup['std_error'], $lang_signup['std_uninvited'], 0);
	stdhead($lang_signup['head_invite_signup']);
}
else {
	registration_check("normal");
	failedloginscheck ("Signup");
//	stdhead($lang_signup['head_signup']);
	login_head();
}
//$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";

//$langs = langlist("site_lang");

foreach ($langs as $row)
{
	if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = " selected"; else $se = "";
	$s .= "<option value=". $row["id"] . $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
}
$s .= "\n</select>";
?>
<form method="get" action=<?php echo $_SERVER['PHP_SELF'] ?>>
<?php
if ($type == 'invite')
print("<input type=hidden name=type value='invite'><input type=hidden name=invitenumber value='".$code."'>");
//print("<div align=right valign=top>".$lang_signup['text_select_lang']. $s . "</div>");
?>
</form>
<p>
	<div class="logo">
		<a href="index.html">
			<img src="./styles/BambooGreen/logo-big.png" alt="" height="90px"> </a>
	</div>
	<div class="content">
		<form class="register-form" method="post" action="takesignup.php" novalidate="novalidate" style="display: block;">
			<h3 class="font-green">注 册</h3>
			<p class="hint"> 在下面输入账号信息: </p>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Username</label>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="用户名"
				       name="wantusername"></div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Password</label>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password"
				       placeholder="密码" name="wantpassword"></div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off"
				       placeholder="再次输入密码" name="passagain"></div>



			<p class="hint"> 在下面输入个人信息: </p>
<!--			mail-->
			<div class="form-group">
				<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
				<label class="control-label visible-ie8 visible-ie9">Email</label>
<!--				<label class="control-label visible-ie8 visible-ie9">(格式example@stumail.nwu.edu.cn)</label>-->
				<input class="form-control placeholder-no-fix" type="text" placeholder="Email  目前只支持西大邮箱注册" name="email"></div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Country</label>
<?php

	$countries = "<option value=\"8\">---- " . $lang_signup['select_none_selected'] . " ----</option>";
$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
	$countries .= "<option value=$ct_a[id]" . ($ct_a['id'] == 8 ? " selected" : "") . ">$ct_a[name]</option>n";
?>

				<select name="country" class="form-control">
					<?php echo "<option name=country>n$countries</option>";?>
				</select>
			</div>

			<div class="form-group margin-top-20 margin-bottom-20">
				<label class="mt-checkbox mt-checkbox-outline" style="margin-left: 20%">
					<input type="radio" name="gender" value="Male"> 男
					<span></span>
				</label>
				<label class="mt-checkbox mt-checkbox-outline" style="margin-left: 30%">
					<input type="radio" name="gender" value="Female"> 女
					<span></span>
				</label>
				<div id="register_tnc_error"></div>
			</div>
			<p class="hint"> 请同意以下协议: </p>
			<div class="form-group margin-top-20 margin-bottom-20">
			<label class="mt-checkbox mt-checkbox-outline" style="margin-left: 20%">
				<input type="checkbox" name="rulesverify" value="yes"> 我已阅读并同意遵守站点
				<span></span>
			</label>
			<label class="mt-checkbox mt-checkbox-outline" style="margin-left: 20%">
					<input type="checkbox" name="faqverify" value="yes"> 我会在提问前先查看常见问题
					<span></span>
			</label>
			<label class="mt-checkbox mt-checkbox-outline" style="margin-left: 20%">
					<input type="checkbox" name="ageverify" value="yes"> 我已满13周岁。
					<span></span>
			</label>
			</div>
			<input type=hidden name=hash value=<?php echo $code ?>>
				<div class=toolbox colspan="2" align="center"><font
						color=red><?php echo $lang_signup['text_all_fields_required'] ?></font></div>
			<div class="form-actions">
				<button type="button" id="register-back-btn" class="btn green btn-outline"><a href="login.php">登录</a></button>
				<input type="submit" id="register-submit-btn" class="btn btn-success uppercase pull-right" value="注册">
				</input>
			</div>
		</form>
	</div>



<!--<form method="post" action="takesignup.php">-->
<?php //if ($type == 'invite') print("<input type=\"hidden\" name=\"inviter\" value=\"".$inviter."\"><input type=hidden name=type value='invite'");?>
<!--<table  cellspacing="0" cellpadding="10">-->
<?php
//print("<tr><td class=text align=center colspan=2>".$lang_signup['text_cookies_note']."</td></tr>");
//?>
<!--<tr><td class=rowhead>--><?php //echo $lang_signup['row_desired_username'] ?><!--</td><td class=rowfollow align=left><input type="text" style="width: 200px" name="wantusername" /><br />-->
<!--<font class=small>--><?php //echo $lang_signup['text_allowed_characters'] ?><!--</font></td></tr>-->
<!--<tr><td class=rowhead>--><?php //echo $lang_signup['row_pick_a_password'] ?><!--</td><td class=rowfollow align=left><input type="password" style="width: 200px" name="wantpassword" /><br />-->
<!--	<font class=small>--><?php //echo $lang_signup['text_minimum_six_characters'] ?><!--</font></td></tr>-->
<!--<tr><td class=rowhead>--><?php //echo $lang_signup['row_enter_password_again'] ?><!--</td><td class=rowfollow align=left><input type="password" style="width: 200px" name="passagain" /></td></tr>-->
<?php
//show_image_code ();
//?>
<!--<tr><td class=rowhead>--><?php //echo $lang_signup['row_email_address'] ?><!--</td><td class=rowfollow align=left><input type="text" style="width: 200px" name="email" />-->
<!--<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font class=small>--><?php //echo ($restrictemaildomain == 'yes' ? $lang_signup['text_email_note'].allowedemails() : "") ?><!--</td></tr>-->
<!--</font></td></tr></table>-->
<!--</td></tr>-->
<?php //$countries = "<option value=\"8\">---- ".$lang_signup['select_none_selected']." ----</option>n";
//$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name") or die;
//while ($ct_a = mysql_fetch_array($ct_r))
//$countries .= "<option value=$ct_a[id]" . ($ct_a['id'] == 8 ? " selected" : "") . ">$ct_a[name]</option>n";
//tr($lang_signup['row_country'], "<select name=country>n$countries</select>", 1);
////School select
//if ($showschool == 'yes'){
//$schools = "<option value=35>---- ".$lang_signup['select_none_selected']." ----</option>n";
//$sc_r = sql_query("SELECT id,name FROM schools ORDER BY name") or die;
//while ($sc_a = mysql_fetch_array($sc_r))
//$schools .= "<option value=$sc_a[id]" . ($sc_a['id'] == 35 ? " selected" : "") . ">$sc_a[name]</option>n";
//tr($lang_signup['row_school'], "<select name=school>$schools</select>", 1);
//}
//?>
<!--<tr><td class=rowhead>--><?php //echo $lang_signup['row_gender'] ?><!--</td><td class=rowfollow align=left>-->
<!--<input type=radio name=gender value=Male>--><?php //echo $lang_signup['radio_male'] ?><!--<input type=radio name=gender value=Female>--><?php //echo $lang_signup['radio_female'] ?><!--</td></tr>-->
<!--<tr><td class=rowhead>--><?php //echo $lang_signup['row_verification'] ?><!--</td><td class=rowfollow align=left><input type=checkbox name=rulesverify value=yes>--><?php //echo $lang_signup['checkbox_read_rules'] ?><!--<br />-->
<!--<input type=checkbox name=faqverify value=yes>--><?php //echo $lang_signup['checkbox_read_faq'] ?><!-- <br />-->
<!--<input type=checkbox name=ageverify value=yes>--><?php //echo $lang_signup['checkbox_age'] ?><!--</td></tr>-->
<!---->
<!---->
<!--<input type=hidden name=hash value=--><?php //echo $code?><!-->-->
<!--<tr><td class=toolbox colspan="2" align="center"><font color=red><b>--><?php //echo $lang_signup['text_all_fields_required'] ?><!--</b><p></font><input type=submit value=--><?php //echo $lang_signup['submit_sign_up'] ?><!-- style='height: 25px'></td></tr>-->
<!--</table>-->
<!--</form>-->
<?php
gethelptips();
stdfoot();
