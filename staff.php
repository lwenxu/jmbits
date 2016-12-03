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
//--------------------- FIRST LINE SUPPORT SECTION ---------------------------//
unset($ppl);
$res = sql_query("SELECT * FROM users WHERE users.support='yes' AND users.status='confirmed' ORDER BY users.username") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
	$countryrow = get_country_row($arr['country']);
	$ppl .= "<tr  style='margin-left:5%'><td class=embedded-add align=center>". get_username($arr['id']) ."</td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<td class=embedded-add align=center ><img width=24 height=15 src=\"pic/flag/".$countryrow[flagpic]."\" title=\"".$countryrow['name']."\" style=\"padding-bottom:1px;\"></td>
 <td class=embedded-add align=center> ".(strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg)."</td>".
 "<td class=embedded-add align=center><a href=sendmessage.php?receiver=".$arr['id']." title=\"".$lang_staff['title_send_pm']."\">".$sendpmimg."</a></td>".
 "<td class=embedded-add align=center>".$arr['supportlang']."</td>".
 "<td class=embedded-add align=center>".$arr['supportfor']."</td></tr>\n";
}

begin_frame($lang_staff['text_firstline_support']."<font class=small> - [<a class=altlink href=contactstaff.php><b>".$lang_staff['text_apply_for_it']."</b></a>]</font>");
?>
<?php echo $lang_staff['text_firstline_support_note'] ?>
<br /><br />
<table width=100% cellspacing=0 align=center class="table table-striped">
	<tr>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_username'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_country'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_online_or_offline'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_contact'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_language'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_support_for'] ?></b></td>
	</tr>
	<tr>
		<td class=embedded-add align=center colspan=6>
			<hr color="#4040c0">
		</td>
	</tr>
	<?php echo $ppl?>
</table>
<?php
end_frame();

//--------------------- FIRST LINE SUPPORT SECTION ---------------------------//

//--------------------- film critics section ---------------------------//
unset($ppl);
$res = sql_query("SELECT * FROM users WHERE users.picker='yes' AND users.status='confirmed' ORDER BY users.username") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
	$countryrow = get_country_row($arr['country']);
	$ppl .= "<tr height=15><td class=embedded-add align=center>". get_username($arr['id']) ."</td class=embedded-add align=center> <td class=embedded-add align=center style='text-align:center'><span style='text-align:center'><img  width=24 height=15 src=\"pic/flag/".$countryrow['flagpic']."\" title=\"".$countryrow['name']."\" style=\"padding-bottom:1px;\"></span></td>
 <td class=embedded-add align=center> ".(strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg)."</td>".
 "<td class=embedded-add align=center><a href=sendmessage.php?receiver=".$arr['id']." title=\"".$lang_staff['title_send_pm']."\">".$sendpmimg."</a></td>".
 "<td class=embedded-add align=center>".$arr['pickfor']."</td></tr>\n";
}

begin_frame($lang_staff['text_movie_critics']."<font class=small> - [<a class=altlink href=contactstaff.php><b>".$lang_staff['text_apply_for_it']."</b></a>]</font>");
?>
<?php echo $lang_staff['text_movie_critics_note'] ?>
<br /><br />
<table width=100% cellspacing=0 align=center class="table table-striped">
	<tr>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_username'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_country'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_online_or_offline'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_contact'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_responsible_for'] ?></b></td>
	</tr>
	<tr>
		<td class=embedded-add align=center colspan=5>
			<hr color="#4040c0">
		</td>
	</tr>
	<?php echo $ppl?>
</table>
<?php
end_frame();

//--------------------- film critics section ---------------------------//

//--------------------- forum moderators section ---------------------------//
unset($ppl);
$res = sql_query("SELECT forummods.userid AS userid, users.last_access, users.country FROM forummods LEFT JOIN users ON forummods.userid = users.id GROUP BY userid ORDER BY forummods.forumid, forummods.userid") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
	$countryrow = get_country_row($arr['country']);
	$forums = "";
	$forumres = sql_query("SELECT forums.id, forums.name FROM forums LEFT JOIN forummods ON forums.id = forummods.forumid WHERE forummods.userid = ".sqlesc($arr[userid]));
	while ($forumrow = mysql_fetch_array($forumres)){
		$forums .= "<a href=forums.php?action=viewforum&forumid=".$forumrow['id'].">".$forumrow['name']."</a>, ";
	}
	$forums = rtrim(trim($forums),",");
	$ppl .= "<tr height=15><td class=embedded-add align=center>". get_username($arr['userid']) ."</td><td class=embedded-add align=center ><img width=24 height=15 src=\"pic/flag/".$countryrow['flagpic']."\" title=\"".$countryrow['name']."\" style=\"padding-bottom:1px;\"></td>
 <td class=embedded-add align=center> ".(strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg)."</td>".
 "<td class=embedded-add align=center><a href=sendmessage.php?receiver=".$arr['userid']." title=\"".$lang_staff['title_send_pm']."\">".$sendpmimg."</a></td>".
 "<td class=embedded-add align=center>".$forums."</td></tr>\n";
}

begin_frame($lang_staff['text_forum_moderators']."<font class=small> - [<a class=altlink href=contactstaff.php><b>".$lang_staff['text_apply_for_it']."</b></a>]</font>");
?>
<?php echo $lang_staff['text_forum_moderators_note'] ?>
<br /><br />
<table width=100% cellspacing=0 align=center class="table table-striped">
	<tr>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_username'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_country'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_online_or_offline'] ?></b></td>
		<td class=embedded-add align=center align=center><b><?php echo $lang_staff['text_contact'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_forums'] ?></b></td>
	</tr>
	<tr>
		<td class=embedded-add align=center colspan=5>
			<hr color="#4040c0">
		</td>
	</tr>
	<?php echo $ppl?>
</table>
<?php
end_frame();

//--------------------- film critics section ---------------------------//

//--------------------- general staff section ---------------------------//
unset($ppl);
$res = sql_query("SELECT * FROM users WHERE class > ".UC_VIP." AND status='confirmed' ORDER BY class DESC, username") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
	if($curr_class != $arr['class'])
	{
		$curr_class = $arr['class'];
		if ($ppl != "")
			$ppl .= "<tr height=15><td class=embedded-add align=center colspan=5 align=right>&nbsp;</td></tr>";
		$ppl .= "<tr height=15><td class=embedded-add align=center colspan=5 align=right>" . get_user_class_name($arr["class"],false,true,true) . "</td></tr>";
		$ppl .= "<tr>" .
		"<td class=embedded-add align=center><b>" . $lang_staff['text_username'] . "</b></td>".
		"<td class=embedded-add align=center align=center><b>" . $lang_staff['text_country'] . "</b></td>".
		"<td class=embedded-add align=center align=center><b>" . $lang_staff['text_online_or_offline'] . "</b></td>".
		"<td class=embedded-add align=center align=center><b>" . $lang_staff['text_contact'] . "</b></td>".
		"<td class=embedded-add align=center><b>" . $lang_staff['text_duties'] . "</b></td>".
		"</tr>";
		$ppl .= "<tr height=15><td class=embedded-add align=center colspan=5><hr color=\"#4040c0\"></td></tr>";
	}
	$countryrow = get_country_row($arr['country']);
	$ppl .= "<tr><td class=embedded-add align=center>". get_username($arr['id']) ."</td><td class=embedded-add align=center ><img width=24 height=15 src=\"pic/flag/".$countryrow['flagpic']."\" title=\"".$countryrow['name']."\" style=\"padding-bottom:1px;\"></td>
 <td class=embedded-add align=center> ".(strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg)."</td>".
 "<td class=embedded-add align=center><a href=sendmessage.php?receiver=".$arr['id']." title=\"".$lang_staff['title_send_pm']."\">".$sendpmimg."</a></td>".
 "<td class=embedded-add align=center>".$arr['stafffor']."</td></tr>\n";
}

begin_frame($lang_staff['text_general_staff']."<font class=small> - [<a class=altlink href=contactstaff.php><b>".$lang_staff['text_apply_for_it']."</b></a>]</font>");
?>
<?php echo $lang_staff['text_general_staff_note'] ?>
<br /><br />
<table width=100% cellspacing=0 align=center class="table table-striped">
	<?php echo $ppl?>
</table>
<?php
end_frame();

//--------------------- general staff section ---------------------------//


//--------------------- VIP section ---------------------------//

unset($ppl);
$res = sql_query("SELECT * FROM users WHERE class=".UC_VIP." AND status='confirmed' ORDER BY username") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
	$countryrow = get_country_row($arr['country']);
	$ppl .= "<tr><td class=embedded-add align=center>". get_username($arr['id']) ."</td><td class=embedded-add align=center><img width=24 height=15 src=\"pic/flag/".$countryrow['flagpic']."\" title=\"".$countryrow['name']."\" style=\"padding-bottom:1px;\"></td>
 <td class=embedded-add align=center> ".(strtotime($arr['last_access']) > $dt ? $onlineimg : $offlineimg)."</td>".
 "<td class=embedded-add align=center><a href=sendmessage.php?receiver=".$arr['id']." title=\"".$lang_staff['title_send_pm']."\">".$sendpmimg."</a></td>".
 "<td class=embedded-add align=center>".$arr['stafffor']."</td></tr>\n";
}

begin_frame($lang_staff['text_vip']);
?>
<?php echo $lang_staff['text_vip_note'] ?>
<br /><br />
<table width=100% cellspacing=0 align=center class="table table-striped">
	<tr>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_username'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_country'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_online_or_offline'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_contact'] ?></b></td>
		<td class=embedded-add align=center><b><?php echo $lang_staff['text_reason'] ?></b></td>
	</tr>
	<tr>
		<td class=embedded-add align=center colspan=5>
			<hr color="#4040c0">
		</td>
	</tr>
	<?php echo $ppl?>
</table>
<?php
end_frame();

//--------------------- VIP section ---------------------------//
end_main_frame();
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
stdfoot();
