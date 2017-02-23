<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn(true);
stdhead($lang_staff['head_staff']);

$Cache->new_page('staff_page', 900, true);
if (!$Cache->get_page()){
$Cache->add_whole_row();
begin_main_frame();
$secs = 900;
$dt = TIMENOW - $secs;
$onlineimg = "<span class=\"icon-circle\"  style='color:#5cb85c' title=\"".$lang_staff['title_online']."\"></span>";
$offlineimg = "<span class=\" icon-circle-blank\" style='color:tomato' title=\"".$lang_staff['title_offline']."\" ></span>";
$sendpmimg = "<sapn class=\" icon-envelope-alt\" style='color:#5cb85c' alt=\"pm\" ></sapn>";

	echo "
	<div class='row' style='padding-top: 10px'>
        <div class='container'>
            <div class='col-lg-6 col-md-6 col-sm-6 col-xs-6'>";
	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-earphone font-red-sunglo\"></i>
                                            <span class=\"caption-subject font-red-sunglo bold uppercase\">一线客服</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>". $lang_staff['text_firstline_support_note']."
                                    <div class=\"portlet-body\">";
	//--------------------- FIRST LINE SUPPORT SECTION ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE users.support='yes' AND users.status='confirmed' ORDER BY users.username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		$countryrow = get_country_row($arr['country']);
		$avatar=$arr['avatar']!=''? $arr['avatar']:'pic/default_avatar.png';
		$ppl .= "
<tr  style='margin-left:5%'>
<td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar."> ". get_username($arr['id']) . "</td>
<td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
"<td class=embedded-add align=left>" . $arr['supportfor'] . "</td></tr>\n";
	}
	?>

    <table class="table">
        <thead>
            <tr>
                <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
                <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
                <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
                <th class=embedded-add align="left"><b><?php echo $lang_staff['text_support_for'] ?></b></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- FIRST LINE SUPPORT SECTION ---------------------------//

    echo"                                </div>
                            </div>";
	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-asterisk font-green-meadow\"></i>
                                            <span class=\"caption-subject font-green-meadow bold uppercase\">资源版主</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>". $lang_staff['text_forum_moderators_note']."
                                    <div class=\"portlet-body\">";
//--------------------- forum moderators section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT forummods.userid AS userid, users.last_access, users.country FROM forummods LEFT JOIN users ON forummods.userid = users.id GROUP BY userid ORDER BY forummods.forumid, forummods.userid") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		$countryrow = get_country_row($arr['country']);
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$forums = "";
		$forumres = sql_query("SELECT forums.id, forums.name FROM forums LEFT JOIN forummods ON forums.id = forummods.forumid WHERE forummods.userid = " . sqlesc($arr[userid]));
		while ($forumrow = mysql_fetch_array($forumres)) {
			$forums .= "<a href=forums.php?action=viewforum&forumid=" . $forumrow['id'] . ">" . $forumrow['name'] . "</a>, ";
		}
		$forums = rtrim(trim($forums), ",");
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $forums . "</td></tr>\n";
	}

	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_forums'] ?></b></th>
        </tr>
        </thead>
        <tbody>
	    <?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- forum moderators section ---------------------------//



    echo"                                </div>
                            </div>";



	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-tag font-purple-studio\"></i>
                                            <span class=\"caption-subject font-purple-studio bold uppercase\">VIP</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>". $lang_staff['text_vip_note']."
                                    <div class=\"portlet-body\">";
//--------------------- VIP section ---------------------------//

	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE class=" . UC_VIP . " AND status='confirmed' ORDER BY username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		$countryrow = get_country_row($arr['country']);
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['stafffor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_reason'] ?></b></th>
        </tr>
        </thead>
        <tbody>
		<?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- VIP section ---------------------------//

    echo"                                </div>
                            </div>";



	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\" glyphicon glyphicon-console font-green-turquoise\"></i>
                                            <span class=\"caption-subject font-green-turquoise bold uppercase\">维护开发人员</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>". $lang_staff['text_coder_note']."
                                    <div class=\"portlet-body\">";
//--------------------- coder section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE class = " . UC_SYSOP . " AND status='confirmed' ORDER BY class DESC, username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		if ($curr_class != $arr['class']) {
			$curr_class = $arr['class'];
		}
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$countryrow = get_country_row($arr['country']);
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['stafffor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_duties'] ?></b></th>
        </tr>
        </thead>
        <tbody>
		<?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- coder section ---------------------------//

    echo"                                </div>
                            </div>";
	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-open font-green-seagreen\"></i>
                                            <span class=\"caption-subject font-green-seagreen bold uppercase\">发布员</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>" . $lang_staff['text_uploader_note'] . "
                                    <div class=\"portlet-body\">";
//--------------------- uploader section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE class = " . UC_UPLOADER . " AND status='confirmed' ORDER BY class DESC, username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		if ($curr_class != $arr['class']) {
			$curr_class = $arr['class'];
		}
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$countryrow = get_country_row($arr['country']);
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['stafffor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_duties'] ?></b></th>
        </tr>
        </thead>
        <tbody>
		<?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- uploader section ---------------------------//
	echo "                                </div>
                            </div>";









            echo "</div>";//-------------------col-1-6 end-----------------------//
    echo "<div class='col-lg-6 col-md-6 col-sm-6 col-xs-6'>";  //-----------col-2-6 start----------------//
	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-comment font-yellow-lemon\"></i>
                                            <span class=\"caption-subject font-yellow-lemon bold uppercase\">批评家</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>" . $lang_staff['text_movie_critics_note'] . "
                                    <div class=\"portlet-body\">";
//--------------------- film critics section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE users.picker='yes' AND users.status='confirmed' ORDER BY users.username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		$countryrow = get_country_row($arr['country']);
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['supportfor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_support_for'] ?></b></th>
        </tr>
        </thead>
        <tbody>
		<?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- film critics section ---------------------------//

	echo "                                </div>
                            </div>";

	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-user font-blue-sharp\"></i>
                                            <span class=\"caption-subject font-blue-sharp bold uppercase\">管理员</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>" . $lang_staff['text_general_staff_note'] . "
                                    <div class=\"portlet-body\">";
//--------------------- admin section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE class = " . UC_ADMINISTRATOR . " AND status='confirmed' ORDER BY class DESC, username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		if ($curr_class != $arr['class']) {
			$curr_class = $arr['class'];
		}
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$countryrow = get_country_row($arr['country']);
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['stafffor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_duties'] ?></b></th>
        </tr>
        </thead>
        <tbody>
	    <?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- admin section ---------------------------//
	echo "                                </div>
                            </div>";
	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-th-large font-yellow\"></i>
                                            <span class=\"caption-subject font-yellow bold uppercase\">站长</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>" . $lang_staff['text_admin_staff_note'] . "
                                    <div class=\"portlet-body\">";
//--------------------- admin section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE class = " . UC_STAFFLEADER . " AND status='confirmed' ORDER BY class DESC, username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		if ($curr_class != $arr['class']) {
			$curr_class = $arr['class'];
		}
		$avatar=$arr['avatar']? $arr['avatar']:'pic/default_avatar.png';
		$countryrow = get_country_row($arr['country']);
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> ". get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['stafffor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_duties'] ?></b></th>
        </tr>
        </thead>
        <tbody>
	    <?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- admin section ---------------------------//
	echo "                                </div>
                            </div>";


	echo "
	                        <div class=\"portlet light bordered\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-tags font-purple-sharp\"></i>
                                            <span class=\"caption-subject font-purple-sharp bold uppercase\">退休的管理员</span>
                                        </h3>
                                        </div>
                                        <div class=\"actions\" style='margin-top: 14px'>
                                            <a class=\"btn btn-circle btn-success\" href=\"contactstaff.php\" style='color: white'>
                                                申请加入
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                        </div>
                                    </div>" . $lang_staff['text_retiree_note'] . "
                                    <div class=\"portlet-body\">";
//--------------------- admin section ---------------------------//
	unset($ppl);
	$res = sql_query("SELECT * FROM users WHERE class = " . UC_RETIREE . " AND status='confirmed' ORDER BY class DESC, username") or sqlerr();
	while ($arr = mysql_fetch_assoc($res)) {
		if ($curr_class != $arr['class']) {
			$curr_class = $arr['class'];
		}
		$avatar = $arr['avatar'] ? $arr['avatar'] : 'pic/default_avatar.png';
		$countryrow = get_country_row($arr['country']);
		$ppl .= "
            <tr  style='margin-left:5%'>
            <td class=embedded-add align=left><img class='img img-circle' height='30px' width='30px ' src=" . $avatar . "> " . get_username($arr['id']) . "</td>
            <td class=embedded-add align=left> " . (strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg) . "</td>" .
			"<td class=embedded-add align=left><a href=sendmessage.php?receiver=" . $arr['id'] . " title=\"" . $lang_staff['title_send_pm'] . "\">" . $sendpmimg . "</a></td>" .
			"<td class=embedded-add align=left>" . $arr['stafffor'] . "</td></tr>\n";
	}
	?>
    <table class="table">
        <thead>
        <tr>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_username'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_online_or_offline'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_contact'] ?></b></th>
            <th class=embedded-add align="left"><b><?php echo $lang_staff['text_duties'] ?></b></th>
        </tr>
        </thead>
        <tbody>
	    <?php echo $ppl ?>
        </tbody>
    </table>
	<?php
	end_frame();

//--------------------- admin section ---------------------------//
	echo "                                </div>
                            </div>";

    echo "</div>";//-------------------col-2-6 end-----------------------//
	echo "</div>";//-------------------container end-----------------------//
echo    "</div>";    //-------------------row  end-----------------//


end_main_frame();
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
stdfoot();
