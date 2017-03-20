<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
require_once(get_langfile_path("",true));
loggedinorreturn();
parked();
if ($enableoffer == 'no')
permissiondenied();
function bark($msg) {
	global $lang_offers;
	stdhead($lang_offers['head_offer_error']);
	stdmsg($lang_offers['std_error'], $msg);
	stdfoot();
	exit;
}

if ($_GET["category"]){
	$categ = isset($_GET['category']) ? (int)$_GET['category'] : 0;
	if(!is_valid_id($categ))
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
}

if ($_GET["id"]){
	$id = 0 + htmlspecialchars($_GET["id"]);
	if (preg_match('/^[0-9]+$/', !$id))
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
}




//==== add offer
if ($_GET["add_offer"]){
	if (get_user_class() < $addoffer_class)
		permissiondenied();
	$add_offer = 0 + $_GET["add_offer"];
	if($add_offer != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	stdhead($lang_offers['head_offer']);

	echo "
		<style>
				#compose *{
					    font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;
						font-size: 14px;
						line-height: 1.42857143;
						color: #333;
				}
		</style>
	";




	echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-open font-green'></i>
                                <span class='caption-subject font-green sbold uppercase'>添加候选 <span class='small font-red   '>星号部分为必填</span></span>
                            </h2>
                        </div>
                    </div>
                    <div class='portlet-body form'>";

	$sele = "<select name=type class='form-control'><option value=0>" . $lang_offers['select_type_select'] . "</option>";
	$cats = genrelist($browsecatmode);
	foreach ($cats as $row)
		$sele .= "<option value=" . $row["id"] . ">" . htmlspecialchars($row["name"]) . "</option>\n";
	$sele .= "</select>";
	print("<form class='form-horizontal form-bordered'  enctype=multipart/form-data id='compose' action='?new_offer=1' name='compose' method='post'>");
	echo "<div class='form-body'>";
	echo "<div class='form-group'>
            <label class='control-label col-md-3'>类型 <font style='color=red'> *</font></label>
            <div class=\"col-md-9\">
            	$sele
            </div>
          </div>
";
	echo "<div class='form-group'>
            <label class='control-label col-md-3'>标题 <font style='color=red'> *</font></label>
            <div class=\"col-md-9\">
            	<input class='form-control' type='text' name='name'>
            </div>
          </div>
";
	echo "<div class='form-group' >
            <label class='control-label col-md-3'>海报或图片  </label>
            <div class=\"col-md-9\" >
            <input class='form-control' type=text name=picture data-toggle='tooltip' id='piclink' style='color: white' title='图片的链接,不要添加代码标签'>
			</div>
         </div>
";
	echo "<div class='form-group' >
            <label class='control-label col-md-3'>简介  <font style='color=red'> *</font></label>
            <div class=\"col-md-9\" >";
            textbbcode("compose", "body", $body, false);
echo	"		</div>
         </div>
";


	print("<div class=toolbox colspan=2 align=center>
		<input style='margin-left:25%' class='med btn btn-success' id=qr type=submit class=btn value=" . $lang_offers['submit_add_offer'] . " >
	</div>");
	echo "</div>";
	print("</form>");


	echo "</div>
                </div>
            </div>
          </div>
            ";
	stdfoot();
	die;
}
//=== end add offer




//=== take new offer
if ($_GET["new_offer"]){
	if (get_user_class() < $addoffer_class)
		permissiondenied();
	$new_offer = 0 + $_GET["new_offer"];
	if($new_offer != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$userid = 0 + $CURUSER["id"];
	if (preg_match("/^[0-9]+$/", !$userid))
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$name = $_POST["name"];
	if ($name == "")
	bark($lang_offers['std_must_enter_name']);

	$cat = (0 + $_POST["type"]);
	if (!is_valid_id($cat))
	bark($lang_offers['std_must_select_category']);

	$descrmain = unesc($_POST["body"]);
	if (!$descrmain)
	bark($lang_offers['std_must_enter_description']);

	if (!empty($_POST['picture'])){
		$picture = unesc($_POST["picture"]);
		if(!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $picture))
		stderr($lang_offers['std_error'], $lang_offers['std_wrong_image_format']);
		$pic = "[img]".$picture."[/img]\n";
	}

	$descr = $pic;
	$descr .= $descrmain;

	$res = sql_query("SELECT name FROM offers WHERE name =".sqlesc($_POST[name])) or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_assoc($res);
	if (!$arr['name']){
		//===add karma //=== uncomment if you use the mod
		//sql_query("UPDATE users SET seedbonus = seedbonus+10.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
		//===end

		$ret = sql_query("INSERT INTO offers (userid, name, descr, category, added) VALUES (" .
		implode(",", array_map("sqlesc", array($CURUSER["id"], $name, $descr, 0 + $_POST["type"]))) .
		", '" . date("Y-m-d H:i:s") . "')");
		if (!$ret) {
			if (mysql_errno() == 1062)
			bark("!!!");
			bark("mysql puked: ".mysql_error());
		}
		$id = mysql_insert_id();

		write_log("offer $name was added by ".$CURUSER[username],'normal');

		header("Refresh: 0; url=offers.php?id=$id&off_details=1");

		stdhead($lang_offers['head_success']);
	}
	else{
		stderr ($lang_offers['std_error'], $lang_offers['std_offer_exists']."<a class=altlink href=offers.php>".$lang_offers['text_view_all_offers']."</a>",false);
	}
	stdfoot();
	die;
}
//==end take new offer

//=== offer details
if ($_GET["off_details"]){

	$off_details = 0 + $_GET["off_details"];
	if($off_details != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$id = 0+$_GET["id"];
	if(!$id)
		die();
//		stderr("Error", "I smell a rat!");
	
	$res = sql_query("SELECT * FROM offers WHERE id = $id") or sqlerr(__FILE__,__LINE__);
	$num = mysql_fetch_array($res);

	$s = $num["name"];

	stdhead($lang_offers['head_offer_detail_for']." \"".$s."\"");

	$offertime = gettime($num['added'], true, false);
	if ($CURUSER['timetype'] != 'timealive')
		$offertime = $lang_offers['text_at'] . $offertime;
	else $offertime = $lang_offers['text_blank'] . $offertime;

	if ($num["allowed"] == "pending")
		$status = "<font class='label label-warning'>" . $lang_offers['text_pending'] . "</font>";
	elseif ($num["allowed"] == "allowed")
		$status = "<font class='label label-success'>" . $lang_offers['text_allowed'] . "</font>";
	else
		$status = "<font class='label label-danger'>" . $lang_offers['text_denied'] . "</font>";

	$zres = sql_query("SELECT COUNT(*) from offervotes where vote='yeah' and offerid=$id");
	$arr = mysql_fetch_row($zres);
	$za = $arr[0];
	$pres = sql_query("SELECT COUNT(*) from offervotes where vote='against' and offerid=$id");
	$arr2 = mysql_fetch_row($pres);
	$protiv = $arr2[0];

	echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-list-alt font-green-dark'></i>
                                <span class='caption-subject font-green-dark sbold uppercase'>候选详情页 </span>
                            </h2>
                        </div>
                    </div>
                    <div class='portlet-body form'>";
	print("<div class='form-horizontal form-bordered'>");
	echo "<div class='form-body'>";
	echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>提交信息 </label>
                    <div class=col-md-9>
                        <div class='input-group'><img class='img img-circle' height='60px' width='60px' src=";
							echo get_user_avatar_url($num['userid']);
							echo "> ";
							echo get_username($num['userid']) . $offertime;
 echo "                 </div>
                    </div>
         </div>
";
	echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>状态 </label>
                    <div class=col-md-9>
                        <div class='input-group'>
                        	$status
               			 </div>
                    </div>
         </div>
";
	if (get_user_class() >= $offermanage_class && $num["allowed"] == "pending"){
			echo "
		        <div class='form-group' >
		                    <label class='control-label col-md-3'>一票决定权 </label>
		                    <div class=col-md-9>
		                        <div class='input-group'>".
										"
													<form method=\"post\" action=\"?allow_offer=1\" class='pull-left' style='padding:5px'>
														<input type=\"hidden\" value=\"" . $id . "\" name=\"offerid\" />" .
													"<input class=\"btn btn-success\" type=\"submit\" value=\"" . $lang_offers['submit_allow'] . "\" />
													</form>
												
													<form method=\"post\" action=\"?id=" . $id . "&amp;finish_offer=1\" class='pull-left'>" .
													"<input type=\"hidden\" value=\"" . $id . "\" name=\"finish\" />
													<input class=\"btn btn-danger\" type=\"submit\" value=\"" . $lang_offers['submit_let_votes_decide'] . "\" />
													"
		                         ."</div>
		                    </div>
		         </div>
		";
	}

	//if pending
	if ($num["allowed"] == "pending") {

	echo "
		        <div class='form-group' >
		                    <label class='control-label col-md-3'>投票 </label>
		                    <div class=col-md-9>
		                        <div class='input-group'>".
									"<a href=\"?id=" . $id . "&amp;vote=yeah\"><font class='label label-success'>" . $lang_offers['text_for'] . "</font></a>" . (get_user_class() >= $againstoffer_class ? " <a href=\"?id=" . $id . "&amp;vote=against\">" . "<font class='label label-danger'>" . $lang_offers['text_against'] . "</font></a>": '')
                                ."                       
							  </div>
						</div>
				</div>";

		echo "
		        <div class='form-group' >
		                    <label class='control-label col-md-3'>状态 </label>
		                    <div class=col-md-9>
		                        <div class='input-group'>支持： <span class='label label-success'>".
									$za .'</span> 反对：<span class=\'label label-danger\'>'. $protiv.
		                         "</span></div>
		                    </div>
		         </div>
		";
	}
	//===upload torrent message
	if ($num["allowed"] == "allowed" && $CURUSER["id"] != $num["userid"]){
		echo "
		        <div class='form-group' >
		                    <label class='control-label col-md-3'>$lang_offers[row_offer_allowed] </label>
		                    <div class=col-md-9>
		                        <div class='input-group bg-green-dark bg-font-green-dark'>
		                        $lang_offers[text_voter_receives_pm_note]
								</div>
		                    </div>
		         </div>
		";
	}
	if ($num["allowed"] == "allowed" && $CURUSER["id"] == $num["userid"]) {
		echo "
		        <div class='form-group' >
		                    <label class='control-label col-md-3'>$lang_offers[row_offer_allowed] </label>
		                    <div class=col-md-9>
		                        <div class='input-group bg-green-dark bg-font-green-dark'>
		                        $lang_offers[text_urge_upload_offer_note]
								</div>
		                    </div>
		         </div>
		";
	}

	if ($CURUSER[id] == $num[userid] || get_user_class() >= $offermanage_class) {
		$edit = "<a href=\"?id=" . $id . "&amp;edit_offer=1\" class='btn btn-circle green-dark'>编辑候选</a>&nbsp;&nbsp;";
		$delete = "<a href=\"?id=" . $id . "&amp;del_offer=1&amp;sure=0\" class='btn btn-circle red-mint'>删除候选</a>&nbsp;&nbsp;";
	}
	$report = "<a href=\"report.php?reportofferid=" . $id . "\" class='btn btn-circle yellow-crusta'>举报候选</a>";
	echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>修改 </label>
                    <div class=col-md-9>
                        <div class='input-group'>
                        	$edit$delete$report
               			 </div>
                    </div>
         </div>
";
	if ($num["descr"]) {
		$off_bb = format_comment($num["descr"]);
		echo "
	        <div class='form-group' >
	                    <label class='control-label col-md-3'>简介 </label>
	                    <div class=col-md-9>
	                        <div class='input-group well well-lg'>
	                        	$off_bb
	                         </div>
	                    </div>
	         </div>
	";
	}

	stdfoot();
	die;
}
//=== end offer details
//=== allow offer by staff
if ($_GET["allow_offer"]) {

	if (get_user_class() < $offermanage_class)
	stderr($lang_offers['std_access_denied'], $lang_offers['std_mans_job']);

	$allow_offer = 0 + $_GET["allow_offer"];
	if($allow_offer != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	//=== to allow the offer  credit to S4NE for this next bit :)
	//if ($_POST["offerid"]){
	$offid = 0 + $_POST["offerid"];
	if(!is_valid_id($offid))
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$res = sql_query("SELECT users.username, offers.userid, offers.name FROM offers inner join users on offers.userid = users.id where offers.id = $offid") or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_assoc($res);
	if ($offeruptimeout_main){
		$timeouthour = floor($offeruptimeout_main/3600);
		$timeoutnote = $lang_offers_target[get_user_lang($arr["userid"])]['msg_you_must_upload_in'].$timeouthour.$lang_offers_target[get_user_lang($arr["userid"])]['msg_hours_otherwise'];
	}
	else $timeoutnote = "";
	$msg = "$CURUSER[username]".$lang_offers_target[get_user_lang($arr["userid"])]['msg_has_allowed']."[b][url=". get_protocol_prefix() . $BASEURL ."/offers.php?id=$offid&off_details=1]" . $arr[name] . "[/url][/b]. ".$lang_offers_target[get_user_lang($arr["userid"])]['msg_find_offer_option'].$timeoutnote;

	$subject = $lang_offers_target[get_user_lang($arr["userid"])]['msg_your_offer_allowed'];
	$allowedtime = date("Y-m-d H:i:s");
	sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $arr[userid], '" . $allowedtime . "', " . sqlesc($msg) . ", ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);
	sql_query ("UPDATE offers SET allowed = 'allowed', allowedtime = '".$allowedtime."' WHERE id = $offid") or sqlerr(__FILE__,__LINE__);

	write_log("$CURUSER[username] allowed offer $arr[name]",'normal');
	header("Refresh: 0; url=" . get_protocol_prefix() . "$BASEURL/offers.php?id=$offid&off_details=1");
}
//=== end allow the offer

//=== allow offer by vote
if ($_GET["finish_offer"]) {

	if (get_user_class() < $offermanage_class)
	stderr($lang_offers['std_access_denied'], $lang_offers['std_have_no_permission']);

	$finish_offer = 0 + $_GET["finish_offer"];
	if($finish_offer != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$offid = 0 + $_POST["finish"];
	if(!is_valid_id($offid))
		stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$res = sql_query("SELECT users.username, offers.userid, offers.name FROM offers inner join users on offers.userid = users.id where offers.id = $offid") or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_assoc($res);

	$voteresyes = sql_query("SELECT COUNT(*) from offervotes where vote='yeah' and offerid=$offid");
	$arryes = mysql_fetch_row($voteresyes);
	$yes = $arryes[0];
	$voteresno = sql_query("SELECT COUNT(*) from offervotes where vote='against' and offerid=$offid");
	$arrno = mysql_fetch_row($voteresno);
	$no = $arrno[0];

	if($yes == '0' && $no == '0')
	stderr($lang_offers['std_sorry'], $lang_offers['std_no_votes_yet']."<a  href=offers.php?id=$offid&off_details=1>".$lang_offers['std_back_to_offer_detail']."</a>",false);
	$finishvotetime = date("Y-m-d H:i:s");
	if (($yes - $no)>=$minoffervotes){
		if ($offeruptimeout_main){
			$timeouthour = floor($offeruptimeout_main/3600);
			$timeoutnote = $lang_offers_target[get_user_lang($arr["userid"])]['msg_you_must_upload_in'].$timeouthour.$lang_offers_target[get_user_lang($arr["userid"])]['msg_hours_otherwise'];
		}
		else $timeoutnote = "";
		$msg = $lang_offers_target[get_user_lang($arr["userid"])]['msg_offer_voted_on']."[b][url=" . get_protocol_prefix() . $BASEURL."/offers.php?id=$offid&off_details=1]" . $arr[name] . "[/url][/b].". $lang_offers_target[get_user_lang($arr["userid"])]['msg_find_offer_option'].$timeoutnote;
		sql_query ("UPDATE offers SET allowed = 'allowed',allowedtime ='".$finishvotetime."' WHERE id = $offid") or sqlerr(__FILE__,__LINE__);
	}
	else if(($no - $yes)>=$minoffervotes){
		$msg = $lang_offers_target[get_user_lang($arr["userid"])]['msg_offer_voted_off']."[b][url=". get_protocol_prefix() . $BASEURL."/offers.php?id=$offid&off_details=1]" . $arr[name] . "[/url][/b].".$lang_offers_target[get_user_lang($arr["userid"])]['msg_offer_deleted'] ;
		sql_query ("UPDATE offers SET allowed = 'denied' WHERE id = $offid") or sqlerr(__FILE__,__LINE__);
	}
			//===use this line if you DO HAVE subject in your PM system
	$subject = $lang_offers_target[get_user_lang($arr[userid])]['msg_your_offer'].$arr[name].$lang_offers_target[get_user_lang($arr[userid])]['msg_voted_on'];
	sql_query("INSERT INTO messages (sender, subject, receiver, added, msg) VALUES(0, ".sqlesc($subject).", $arr[userid], '" . $finishvotetime . "', " . sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
	//===use this line if you DO NOT subject in your PM system
	//sql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES(0, $arr[userid], '" . date("Y-m-d H:i:s") . "', " . sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
	write_log("$CURUSER[username] closed poll $arr[name]",'normal');

	header("Refresh: 0; url=" . get_protocol_prefix() . "$BASEURL/offers.php?id=$offid&off_details=1");
	die;
}
//===end allow offer by vote

//=== edit offer

if ($_GET["edit_offer"]) {

	$edit_offer = 0 + $_GET["edit_offer"];
	if($edit_offer != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$id = 0 + $_GET["id"];

	$res = sql_query("SELECT * FROM offers WHERE id = $id") or sqlerr(__FILE__, __LINE__);
	$num = mysql_fetch_array($res);

	$timezone = $num["added"];

	$s = $num["name"];
	$id2 = $num["category"];

	if ($CURUSER["id"] != $num["userid"] && get_user_class() < $offermanage_class)
	stderr($lang_offers['std_error'], $lang_offers['std_cannot_edit_others_offer']);

	$body = htmlspecialchars(unesc($num["descr"]));
	$s2 = "<select name=\"category\">\n";

	$cats = genrelist($browsecatmode);

	foreach ($cats as $row)
	$s2 .= "<option value=\"" . $row["id"] . "\" ".($row['id'] == $id2 ? " selected=\"selected\"" : "").">" . htmlspecialchars($row["name"]) . "</option>\n";
	$s2 .= "</select>\n";

	stdhead($lang_offers['head_edit_offer'].": $s");
	$title = htmlspecialchars(trim($s));
	
	print("<form id=\"compose\" method=\"post\" name=\"compose\" action=\"?id=".$id."&amp;take_off_edit=1\">".
	"<table width=\"940\" cellspacing=\"0\" cellpadding=\"3\"><tr><td class=\"colhead\" align=\"center\" colspan=\"2\">".$lang_offers['text_edit_offer']."</td></tr>");
	tr($lang_offers['row_type']."<font color=\"red\">*</font>", $s2, 1);
	tr($lang_offers['row_title']."<font color=\"red\">*</font>", "<input type=\"text\" style=\"width: 650px\" name=\"name\" value=\"".$title."\" />", 1);
	tr($lang_offers['row_post_or_photo'], "<input type=\"text\" name=\"picture\" style=\"width: 650px\" value='' /><br />".$lang_offers['text_link_to_picture'], 1);
	print("<tr><td class=\"rowhead\" align=\"right\" valign=\"top\"><b>".$lang_offers['row_description']."<font color=\"red\">*</font></b></td><td class=\"rowfollow\" align=\"left\">");
	textbbcode("compose","body",$body,false);
	print("</td></tr>");
	print("<tr><td class=\"toolbox\" style=\"vertical-align: middle; padding-top: 10px; padding-bottom: 10px;\" align=\"center\" colspan=\"2\"><input id=\"qr\" type=\"submit\" value=\"".$lang_offers['submit_edit_offer']."\" class=\"btn\" /></td></tr></table></form><br />\n");
	stdfoot();
	die;
}
//=== end edit offer

//==== take offer edit
if ($_GET["take_off_edit"]){

	$take_off_edit = 0 + $_GET["take_off_edit"];
	if($take_off_edit != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$id = 0 + $_GET["id"];

	$res = sql_query("SELECT userid FROM offers WHERE id = $id") or sqlerr(__FILE__, __LINE__);
	$num = mysql_fetch_array($res);

	if ($CURUSER[id] != $num[userid] && get_user_class() < $offermanage_class)
	stderr($lang_offers['std_error'], $lang_offers['std_access_denied']);

	$name = $_POST["name"];

	if (!empty($_POST['picture'])){
		$picture = unesc($_POST["picture"]);
		if(!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $picture))
		stderr($lang_offers['std_error'], $lang_offers['std_wrong_image_format']);
		$pic = "[img]".$picture."[/img]\n";
	}
	$descr = "$pic";
	$descr .= unesc($_POST["body"]);
	if (!$name)
	bark($lang_offers['std_must_enter_name']);
	if (!$descr)
	bark($lang_offers['std_must_enter_description']);
	$cat = (0 + $_POST["category"]);
	if (!is_valid_id($cat))
	bark($lang_offers['std_must_select_category']);

	$name = sqlesc($name);
	$descr = sqlesc($descr);
	$cat = sqlesc($cat);

	sql_query("UPDATE offers SET category=$cat, name=$name, descr=$descr where id=".sqlesc($id));

	//header("Refresh: 0; url=offers.php?id=$id&off_details=1");
}
//======end take offer edit

//=== offer votes list
if ($_GET["offer_vote"]){

	$offer_vote = 0 + $_GET["offer_vote"];
	if($offer_vote != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$offerid = 0 + htmlspecialchars($_GET[id]);

	$res2 = sql_query("SELECT COUNT(*) FROM offervotes WHERE offerid = ".sqlesc($offerid)) or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$offername = get_single_value("offers","name","WHERE id=".sqlesc($offerid));
	stdhead($lang_offers['head_offer_voters']." - \"".$offername."\"");

	print("<h1 align=center>".$lang_offers['text_vote_results_for']." <a  href=offers.php?id=$offerid&off_details=1><b>".htmlspecialchars($offername)."</b></a></h1>");

	$perpage = 25;
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?id=".$offerid."&offer_vote=1&");
	$res = sql_query("SELECT * FROM offervotes WHERE offerid=".sqlesc($offerid)." ".$limit) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
	print("<p align=center><b>".$lang_offers['std_no_votes_yet']."</b></p>\n");
	else
	{
		echo $pagertop;
		print("<table border=1 cellspacing=0 cellpadding=5><tr><td class=colhead>".$lang_offers['col_user']."</td><td class=colhead align=left>".$lang_offers['col_vote']."</td>\n");

		while ($arr = mysql_fetch_assoc($res))
		{
			if ($arr[vote] == 'yeah')
				$vote = "<b><font color=green>".$lang_offers['text_for']."</font></b>";
			elseif ($arr[vote] == 'against')
				$vote = "<b><font color=red>".$lang_offers['text_against']."</font></b>";
			else $vote = "unknown";

			print("<tr><td class=rowfollow>" . get_username($arr['userid']) . "</td><td class=rowfollow align=left >".$vote."</td></tr>\n");
		}
		print("</table>\n");
		echo $pagerbottom;
	}

	stdfoot();
	die;
}
//=== end offer votes list

//=== offer votes
if ($_GET["vote"]){
	$offerid = 0 + htmlspecialchars($_GET["id"]);
	$vote = htmlspecialchars($_GET["vote"]);
	if ($vote == 'against' && get_user_class() < $againstoffer_class)
		stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
	if ($vote =='yeah' || $vote =='against')
	{
		$userid = 0+$CURUSER["id"];
		$res = sql_query("SELECT * FROM offervotes WHERE offerid=".sqlesc($offerid)." AND userid=".sqlesc($userid)) or sqlerr(__FILE__,__LINE__);
		$arr = mysql_fetch_assoc($res);
		$voted = $arr;
		$offer_userid = get_single_value("offers", "userid", "WHERE id=".sqlesc($offerid));
		if ($offer_userid == $CURUSER['id'])
		{
			stderr($lang_offers['std_error'], $lang_offers['std_cannot_vote_youself']);
		}
		elseif ($voted)
		{
			stderr($lang_offers['std_already_voted'],$lang_offers['std_already_voted_note']."<a  href=offers.php?id=$offerid&off_details=1>".$lang_offers['std_back_to_offer_detail'] ,false);
		}
		else
		{
			sql_query("UPDATE offers SET $vote = $vote + 1 WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);

			$res = sql_query("SELECT users.username, offers.userid, offers.name FROM offers LEFT JOIN users ON offers.userid = users.id WHERE offers.id = ".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
			$arr = mysql_fetch_assoc($res);

			$rs = sql_query("SELECT yeah, against, allowed FROM offers WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
			$ya_arr = mysql_fetch_assoc($rs);
			$yeah = $ya_arr["yeah"];
			$against = $ya_arr["against"];
			$finishtime = date("Y-m-d H:i:s");
			//allowed and send offer voted on message
			if(($yeah-$against)>=$minoffervotes && $ya_arr['allowed'] != "allowed")
			{
				if ($offeruptimeout_main){
					$timeouthour = floor($offeruptimeout_main/3600);
					$timeoutnote = $lang_offers_target[get_user_lang($arr["userid"])]['msg_you_must_upload_in'].$timeouthour.$lang_offers_target[get_user_lang($arr["userid"])]['msg_hours_otherwise'];
				}
				else $timeoutnote = "";
				sql_query("UPDATE offers SET allowed='allowed', allowedtime=".sqlesc($finishtime)." WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
				$msg = $lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_voted_on']."[b][url=". get_protocol_prefix() . $BASEURL."/offers.php?id=$offerid&off_details=1]" . $arr[name] . "[/url][/b].". $lang_offers_target[get_user_lang($arr['userid'])]['msg_find_offer_option'].$timeoutnote;
				$subject = $lang_offers_target[get_user_lang($arr['userid'])]['msg_your_offer_allowed'];
				sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $arr[userid], " . sqlesc(date("Y-m-d H:i:s")) . ", " . sqlesc($msg) . ", ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);
				write_log("System allowed offer $arr[name]",'normal');
			}
			//denied and send offer voted off message
			if(($against-$yeah)>=$minoffervotes && $ya_arr['allowed'] != "denied")
			{
				sql_query("UPDATE offers SET allowed='denied' WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
				$msg = $lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_voted_off']."[b][url=" . get_protocol_prefix() . $BASEURL."/offers.php?id=$offid&off_details=1]" . $arr[name] . "[/url][/b].".$lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_deleted'] ;
				$subject = $lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_deleted'];
				sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $arr[userid], " . sqlesc(date("Y-m-d H:i:s")) . ", " . sqlesc($msg) . ", ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);
				write_log("System denied offer $arr[name]",'normal');
			}


			sql_query("INSERT INTO offervotes (offerid, userid, vote) VALUES($offerid, $userid, ".sqlesc($vote).")") or sqlerr(__FILE__,__LINE__);
			KPS("+",$offervote_bonus,$CURUSER["id"]);
			stdhead($lang_offers['head_vote_for_offer']);
			print("<h1 align=center>".$lang_offers['std_vote_accepted']."</h1>");
			print($lang_offers['std_vote_accepted_note']."<a  href=offers.php?id=$offerid&off_details=1>".$lang_offers['std_back_to_offer_detail']);
			stdfoot();
			die;
		}
	}
	else
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
}
//=== end offer votes

//=== delete offer
if ($_GET["del_offer"]){

	$del_offer = 0 + $_GET["del_offer"];
	if($del_offer != '1')
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$offer = 0 + $_GET["id"];

	$userid = 0 + $CURUSER["id"];
	if (!is_valid_id($userid))
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);

	$res = sql_query("SELECT * FROM offers WHERE id = $offer") or sqlerr(__FILE__, __LINE__);
	$num = mysql_fetch_array($res);

	$name = $num["name"];

	if ($userid != $num["userid"] && get_user_class() < $offermanage_class)
	stderr($lang_offers['std_error'], $lang_offers['std_cannot_delete_others_offer']);

	if ($_GET["sure"])
	{
		$sure = $_GET["sure"];
		if($sure == '0' || $sure == '1')
		$sure = 0 + $_GET["sure"];
		else
		stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
	}


	if ($sure == 0)
	stderr($lang_offers['std_delete_offer'], $lang_offers['std_delete_offer_note']."<br /><form method=post action=offers.php?id=$offer&del_offer=1&sure=1>".$lang_offers['text_reason_is']."<input type=text style=\"width: 200px\" name=reason><input type=submit value=\"".$lang_offers['submit_confirm']."\"></form>",false);
	elseif ($sure == 1)
	{
		$reason = $_POST["reason"];
		sql_query("DELETE FROM offers WHERE id=$offer");
		sql_query("DELETE FROM offervotes WHERE offerid=$offer");
		sql_query("DELETE FROM comments WHERE offer=$offer");

		//===add karma	//=== use this if you use the karma mod
		//sql_query("UPDATE users SET seedbonus = seedbonus-10.0 WHERE id = $num[userid]") or sqlerr(__FILE__, __LINE__);
		//===end

		if ($CURUSER["id"] != $num["userid"])
		{
			$added = sqlesc(date("Y-m-d H:i:s"));
			$subject = sqlesc($lang_offers_target[get_user_lang($num["userid"])]['msg_offer_deleted']);
			$msg = sqlesc($lang_offers_target[get_user_lang($num["userid"])]['msg_your_offer'].$num[name].$lang_offers_target[get_user_lang($num["userid"])]['msg_was_deleted_by']. "[url=userdetails.php?id=".$CURUSER['id']."]".$CURUSER['username']."[/url]".$lang_offers_target[get_user_lang($num["userid"])]['msg_blank'].($reason != "" ? $lang_offers_target[get_user_lang($num["userid"])]['msg_reason_is'].$reason : ""));
			sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0, $num[userid], $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
		}
		write_log("Offer: $offer ($num[name]) was deleted by $CURUSER[username]".($reason != "" ? " (".$reason.")" : ""),'normal');
		header("Refresh: 0; url=offers.php");
		die;
	}
	else
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
}
//== end  delete offer

//=== prolly not needed, but what the hell... basically stopping the page getting screwed up
if ($_GET["sort"])
{
	$sort = $_GET["sort"];
	if($sort == 'cat' || $sort == 'name' || $sort == 'added' || $sort == 'comments' || $sort == 'yeah' || $sort == 'against' || $sort == 'v_res')
	$sort = $_GET["sort"];
	else
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
}
//=== end of prolly not needed, but what the hell :P

$categ = 0 + $_GET["category"];

if ($_GET["offerorid"]){
	$offerorid = 0 + htmlspecialchars($_GET["offerorid"]);
	if (preg_match("/^[0-9]+$/", !$offerorid))
	stderr($lang_offers['std_error'], $lang_offers['std_smell_rat']);
}

$search = ($_GET["search"]);

if ($search) {
	$search = " AND offers.name like ".sqlesc("%$search%");
} else {
	$search = "";
}


$cat_order_type = "desc";
$name_order_type = "desc";
$added_order_type = "desc";
$comments_order_type = "desc";
$v_res_order_type = "desc";

/*
if ($cat_order_type == "") { $sort = " ORDER BY added " . $added_order_type; $cat_order_type = "asc"; } // for torrent name
if ($name_order_type == "") { $sort = " ORDER BY added " . $added_order_type; $name_order_type = "desc"; }
if ($added_order_type == "") { $sort = " ORDER BY added " . $added_order_type; $added_order_type = "desc"; }
if ($comments_order_type == "") { $sort = " ORDER BY added " . $added_order_type; $comments_order_type = "desc"; }
if ($v_res_order_type == "") { $sort = " ORDER BY added " . $added_order_type; $v_res_order_type = "desc"; }
*/

if ($sort == "cat")
{
	if ($_GET['type'] == "desc")
		$cat_order_type = "asc";
	$sort = " ORDER BY category ". $cat_order_type;
}
else if ($sort == "name")
{
	if ($_GET['type'] == "desc")
		$name_order_type = "asc";
	$sort = " ORDER BY name ". $name_order_type;
}
else if ($sort == "added")
{
	if ($_GET['type'] == "desc")
		$added_order_type = "asc";
	$sort = " ORDER BY added " . $added_order_type;
}
else if ($sort == "comments")
{
	if ($_GET['type'] == "desc")
		$comments_order_type = "asc";
	$sort = " ORDER BY comments " . $comments_order_type;
}
else if ($sort == "v_res")
{
	if ($_GET['type'] == "desc")
		$v_res_order_type = "asc";
	$sort = " ORDER BY (yeah - against) " . $v_res_order_type;
}




if ($offerorid <> NULL)
{
	if (($categ <> NULL) && ($categ <> 0))
	$categ = "WHERE offers.category = " . $categ . " AND offers.userid = " . $offerorid;
	else
	$categ = "WHERE offers.userid = " . $offerorid;
}

else if ($categ == 0)
$categ = '';
else
$categ = "WHERE offers.category = " . $categ;

$res = sql_query("SELECT count(offers.id) FROM offers inner join categories on offers.category = categories.id inner join users on offers.userid = users.id  $categ $search") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 25;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&" );

//stderr("", $sort);
if($sort == "")
$sort =  "ORDER BY added desc ";

$res = sql_query("SELECT offers.id, offers.userid, offers.name, offers.added, offers.allowedtime, offers.comments, offers.yeah, offers.against, offers.category as cat_id, offers.allowed, categories.image, categories.name as cat FROM offers inner join categories on offers.category = categories.id $categ $search $sort $limit") or sqlerr(__FILE__,__LINE__);
$num = mysql_num_rows($res);

stdhead($lang_offers['head_offers']);
begin_frame('', true,10,"100%","center");
if ($offervotetimeout_main) {
	$rule_three = "<li>" . $lang_offers['text_rule_three_one'] . "<b>" . ($offervotetimeout_main / 3600) . "</b>" . $lang_offers['text_rule_three_two'] . "</li>";
} else {
	$rule_three = "";
}
if ($offeruptimeout_main) {
	$rule_four = "<li>" . $lang_offers['text_rule_four_one'] . "<b>" . ($offeruptimeout_main / 3600) . "</b>" . $lang_offers['text_rule_four_two'] . "</li>";
} else {
	$rule_four = "";
}

$rules="<div align=\"left\">
<ol style='margin-left: 22%;font-size: 15px'>
<li >" . $lang_offers['text_rule_one_one'] . get_user_class_name($upload_class, false, true, true) . $lang_offers['text_rule_one_two'] . get_user_class_name($addoffer_class, false, true, true) . $lang_offers['text_rule_one_three'] . "</li>
<li>" . $lang_offers['text_rule_two_one'] . "<b>" . $minoffervotes . "</b>" . $lang_offers['text_rule_two_two'] . "</li>"
.$rule_three.$rule_four."</ol></div>";
success_msg('<span class=\'icon-bullhorn\'></span>候选区',$rules);

echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-search font-green-jungle'></i>
                                <span class='caption-subject font-green-jungle sbold uppercase'>搜索 </span>
                            </h2>
                        </div>
                    </div>
                    <div class='portlet-body form'>";
print("<form class='form-horizontal form-bordered'  method=get action=?>");
echo "<div class='form-body'>";
echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>搜索 </label>
                    <div class=col-md-9>
                        <div class='input-group'>
                            <span class='input-group-addon'><span class='glyphicon glyphicon-search'></span></span>
                            <input class='form-control' id='specialboxg' type='text'  name='search'>
                        </div>
                    </div>
         </div>
";
echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>分类 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
						$cats = genrelist($browsecatmode);
						$catdropdown = "";
						foreach ($cats as $cat) {
							$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
							$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
						}
						$s = "<select name='category' class='form-control input-large' name=\"lang_id\"><option value = 0>" . $lang_offers['select_show_all'] . "</option>". $catdropdown . "</select>";
						echo $s;
echo "                 </div>
                    </div>
         </div>
";
echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>检索 </label>
                    <div class=col-md-9>
                        <div class='input-group'>
                        <input type=submit class='btn btn-success' value=\"" . $lang_offers['submit_search'] . "\">
                        </div>
                    </div>
         </div>
";
echo "</form>";
if (get_user_class() >= $addoffer_class)
print("<div align=\"center\" style=\"margin: 7px;\"><a style='color: white' class='btn btn-success' href=\"?add_offer=1\"><span class='icon-upload-alt'></span>".
"<b>".$lang_offers['text_add_offer']."</b></a></div>");
end_frame();



$last_offer = strtotime($CURUSER['last_offer']);
if (!$num)
	stdmsg($lang_offers['text_nothing_found'],$lang_offers['text_nothing_found']);
else
{
	echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-tasks font-green-steel'></i>
                                <span class='caption-subject font-green-steel sbold uppercase'>候选列表 </span>
                            </h2>
                        </div>
                    </div>
                    <div class='portlet-body'>";


	$catid = $_GET[category];
	print("<table class=\"table table-hover\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">");
	print("<tr><td class= style=\"padding: 0px\"><a href=\"?category=" . $catid . "&amp;sort=cat&amp;type=" . $cat_order_type . "\">" . $lang_offers['col_type'] . "</a></td>" .
		"<td class= width=\"100%\"><a href=\"?category=" . $catid . "&amp;sort=name&amp;type=" . $name_order_type . "\">" . $lang_offers['col_title'] . "</a></td>" .
		"<td colspan=\"3\" class=><a href=\"?category=" . $catid . "&amp;sort=v_res&amp;type=" . $v_res_order_type . "\">" . $lang_offers['col_vote_results'] . "</a></td>" .
		"<td class=></td>" .
		"<td class=><a href=\"?category=" . $catid . "&amp;sort=added&amp;type=" . $added_order_type . "\"><span class=\"icon-time icos-download\"  title=\"" . $lang_offers['title_time_added'] . "\" ></span></a></td>");
	if ($offervotetimeout_main > 0 && $offeruptimeout_main > 0)
		print("<td class=>" . $lang_offers['col_timeout'] . "</td>");
	print("<td class=>" . $lang_offers['col_offered_by'] . "</td>" .
		(get_user_class() >= $offermanage_class ? "<td class=>" . $lang_offers['col_act'] . "</td>" : "") . "</tr>\n");
	for ($i = 0; $i < $num; ++$i) {
		$arr = mysql_fetch_assoc($res);


		$addedby = get_username($arr['userid']);
		$comms = $arr['comments'];


		//==== if you want allow deny for offers use this next bit
		if ($arr["allowed"] == 'allowed')
			$allowed = "&nbsp;<font class='label label-success' color=\"white\">" . $lang_offers['text_allowed'] . "</font>";
		elseif ($arr["allowed"] == 'denied')
			$allowed = "&nbsp;<font class='label label-danger' color=\"red\">" . $lang_offers['text_denied'] . "</font>";
		else
			$allowed = "&nbsp;<font class='label label-default' color=\"orange\">" . $lang_offers['text_pending'] . "</font>";
		//===end

		if ($arr["yeah"] == 0)
			$zvote = $arr[yeah];
		else
			$zvote = "<b><a href=\"?id=" . $arr[id] . "&amp;offer_vote=1\">" . $arr[yeah] . "</a></b>";
		if ($arr["against"] == 0)
			$pvote = "$arr[against]";
		else
			$pvote = "<b><a href=\"?id=" . $arr[id] . "&amp;offer_vote=1\">" . $arr[against] . "</a></b>";

		if ($arr["yeah"] == 0 && $arr["against"] == 0) {
			$v_res = "0";
		} else {

			$v_res = "<b><a href=\"?id=" . $arr[id] . "&amp;offer_vote=1\" title=\"" . $lang_offers['title_show_vote_details'] . "\"><font color=\"green\">" . $arr[yeah] . "</font> - <font color=\"red\">" . $arr[against] . "</font> = " . ($arr[yeah] - $arr[against]) . "</a></b>";
		}
		$addtime = gettime($arr['added'], false, true);
		$dispname = $arr[name];
		$count_dispname = mb_strlen($arr[name], "UTF-8");
		$max_length_of_offer_name = 70;
		if ($count_dispname > $max_length_of_offer_name)
			$dispname = mb_substr($dispname, 0, $max_length_of_offer_name - 2, "UTF-8") . "..";
		echo "<tr>";
		$img=get_offer_thumb($arr['id']);
		echo "
	<td class=\"rowfollow\">";
		echo GetCategoriesPic($id);
		echo "<a href=\"?category=" . $arr['cat_id'] . "\"><img class='img img-thumbnail' heigt='60px' width='60px' src=" . $img. "></a>";
		echo "</div>
    </div>
</td>
	";

//<td class=\"rowfollow\" style=\"padding: 5px \">
//<a href=\"?category=".$arr['cat_id']."\">".return_category_image($arr['cat_id'], "")."</a>
//</td>
		print("
<td style='text-align: left'><a class='embedded-head' href=\"?id=" . $arr[id] . "&amp;off_details=1\" title=\"" . htmlspecialchars($arr[name]) . "\">" . htmlspecialchars($dispname) . "</a>" . ($CURUSER['appendnew'] != 'no' && strtotime($arr["added"]) >= $last_offer ? " <font class='label label-info'>" . $lang_offers['text_new'] . "</font>" : "") . $allowed . "</td>
<td class=\"rowfollow nowrap\" style='' align=\"left\">" . $v_res . "</td>
<td class=\"rowfollow nowrap\" " . (get_user_class() < $againstoffer_class ? " colspan=\"2\" " : "") . " ><a href=\"?id=" . $arr[id] . "&amp;vote=yeah\" title=\"" . $lang_offers['title_i_want_this'] . "\"><font color=\"green\" class='label label-success'>" . $lang_offers['text_yep'] . "</font></a></td>" . (get_user_class() >= $againstoffer_class ? "<td class=\"rowfollow nowrap\" align=\"left\"><a href=\"?id=" . $arr[id] . "&amp;vote=against\" title=\"" . $lang_offers['title_do_not_want_it'] . "\"><font class='label label-danger' color=\"white\">" . $lang_offers['text_nah'] . "</font></a></td>" : ""));

		print("<td class=\"rowfollow\">" . $comment . "</td><td class=\"rowfollow nowrap\">" . $addtime . "</td>");
		if ($offervotetimeout_main > 0 && $offeruptimeout_main > 0) {
			if ($arr["allowed"] == 'allowed') {
				$futuretime = strtotime($arr['allowedtime']) + $offeruptimeout_main;
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, true, true, false, true);
			} elseif ($arr["allowed"] == 'pending') {
				$futuretime = strtotime($arr['added']) + $offervotetimeout_main;
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, true, true, false, true);
			}
			if (!$timeout)
				$timeout = "N/A";
			print("<td class=\"rowfollow nowrap\">" . $timeout . "</td>");
		}
		print("<td class=\"rowfollow\">" . $addedby . "</td>" . (get_user_class() >= $offermanage_class ? "<td class=\"rowfollow\"><a href=\"?id=" . $arr[id] . "&amp;del_offer=1\"><span class=\"icon-ban-circle\" src=\"pic/trans.gif\" alt=\"D\" title=\"" . $lang_offers['title_delete'] . "\" ></span></a><br /><a href=\"?id=" . $arr[id] . "&amp;edit_offer=1\"><span class=\"icon-pencil\" src=\"pic/trans.gif\" alt=\"E\" title=\"" . $lang_offers['title_edit'] . "\" ></span></a></td>" : "") . "</tr>");
	}
	print("</table>");



	echo "                                         </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
        ";




	echo $pagerbottom;
if(!isset($CURUSER) || $CURUSER['showlastcom'] == 'yes')
create_tooltip_container($lastcom_tooltip, 400);
}
panel_end();
end_main_frame();
$USERUPDATESET[] = "last_offer = ".sqlesc(date("Y-m-d H:i:s"));
stdfoot();
?>
