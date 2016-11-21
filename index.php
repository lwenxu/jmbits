<?php
require "include/bittorrent.php";
dbconn(true);
require_once(get_langfile_path());
loggedinorreturn(true);
if ($showextinfo['imdb'] == 'yes')
	require_once ("imdb/imdb.class.php");
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($showpolls_main == "yes")
	{
		$choice = $_POST["choice"];
		if ($CURUSER && $choice != "" && $choice < 256 && $choice == floor($choice))
		{
			$res = sql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
			$arr = mysql_fetch_assoc($res) or die($lang_index['std_no_poll']);
			$pollid = $arr["id"];

			$hasvoted = get_row_count("pollanswers","WHERE pollid=".sqlesc($pollid)." && userid=".sqlesc($CURUSER["id"]));
			if ($hasvoted)
				stderr($lang_index['std_error'],$lang_index['std_duplicate_votes_denied']);
			sql_query("INSERT INTO pollanswers VALUES(0, ".sqlesc($pollid).", ".sqlesc($CURUSER["id"]).", ".sqlesc($choice).")") or sqlerr(__FILE__, __LINE__);
			$Cache->delete_value('current_poll_content');
			$Cache->delete_value('current_poll_result', true);
			if (mysql_affected_rows() != 1)
			stderr($lang_index['std_error'], $lang_index['std_vote_not_counted']);
			//add karma
			KPS("+",$pollvote_bonus,$userid);

			header("Location: " . get_protocol_prefix() . "$BASEURL/");
			die;
		}
		else
		stderr($lang_index['std_error'], $lang_index['std_option_unselected']);
	}
}



// ------------------------------------------  index strat ---------------------------------------------------------------
stdhead($lang_index['head_home']);
begin_main_frame();
echo "
<style>
a {
    color: #337ab7;
    text-decoration: none;
    background-color: transparent;
}
</style>
";

// ------------- start:  fun box ------------------//
//if ($showfunbox_main == "yes" && (!isset($CURUSER) || $CURUSER['showfb'] == "yes")){
//	// Get the newest fun stuff
//	if (!$row = $Cache->get_value('current_fun_content')){
//		$result = sql_query("SELECT fun.*, IF(ADDTIME(added, '1 0:0:0') < NOW(),true,false) AS neednew FROM fun WHERE status != 'banned' AND status != 'dull' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__,__LINE__);
//		$row = mysql_fetch_array($result);
//		$Cache->cache_value('current_fun_content', $row, 1043);
//	}
//	if (!$row) //There is no funbox item
//	{
//		print("<h4>".$lang_index['text_funbox'].(get_user_class() >= $newfunitem_class ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"fun.php?action=new\"><b>".$lang_index['text_new_fun']."</b></a>]</font>" : "")."</h4>");
//	}
//	else
//	{
//		$totalvote = $Cache->get_value('current_fun_vote_count');
//		if ($totalvote == ""){
//			$totalvote = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id']));
//			$Cache->cache_value('current_fun_vote_count', $totalvote, 756);
//		}
//		$funvote = $Cache->get_value('current_fun_vote_funny_count');
//		if ($funvote == ""){
//			$funvote = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id'])." AND vote='fun'");
//			$Cache->cache_value('current_fun_vote_funny_count', $funvote, 756);
//		}
////check whether current user has voted
//		$funvoted = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id'])." AND userid=".sqlesc($CURUSER[id]));
//		print ("<h4><span class='icon-trophy'></span>".$lang_index['text_funbox']);
//		if ($CURUSER)
//		{
//			print("<font class=\"small\">".(get_user_class() >= $log_class ? " &nbsp;&nbsp;&nbsp;<a class=\"altlink\" href=\"log.php?action=funbox\"><b><span class='icon-quote-left'></span>".$lang_index['text_more_fun']."</b></a>": "").($row['neednew'] && get_user_class() >= $newfunitem_class ? " &nbsp;<a class=altlink href=\"fun.php?action=new\"><b>".$lang_index['text_new_fun']."</b></a>" : "" ).( ($CURUSER['id'] == $row['userid'] || get_user_class() >= $funmanage_class) ? " &nbsp;<a class=\"altlink\" href=\"fun.php?action=edit&amp;id=".$row['id']."&amp;returnto=index.php\"><b><span class='icon-edit'></span>".$lang_index['text_edit']."</b></a>" : "").(get_user_class() >= $funmanage_class ? " &nbsp;<a class=\"altlink\" href=\"fun.php?action=delete&amp;id=".$row['id']."&amp;returnto=index.php\"><b ><sapn class='icon-trash'></sapn>".$lang_index['text_delete']."</b></a>&nbsp;&nbsp;<a class=\"altlink\" href=\"fun.php?action=ban&amp;id=".$row['id']."&amp;returnto=index.php\"><b><sapn class='icon-ban-circle'></sapn>".$lang_index['text_ban']."</b></a>" : "")."</font>") ;
//		}
//		print("</h2>");
//
//		print("<table width=\"100%\"><tr><td class=\"text\">");
//		print("<iframe src=\"fun.php?action=view\" width='100%' height='300' frameborder='0' name='funbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
//
//		if ($CURUSER)
//		{
//			$funonclick = " onclick=\"funvote(".$row['id'].",'fun'".")\"";
//			$dullonclick = " onclick=\"funvote(".$row['id'].",'dull'".")\"";
//			print("<span id=\"funvote\"><b>".$funvote."</b>".$lang_index['text_out_of'].$totalvote.$lang_index['text_people_found_it'].($funvoted ? "" : "<font class=\"striking\">".$lang_index['text_your_opinion']."</font>&nbsp;&nbsp;<input type=\"button\" class='btn' name='fun' id='fun' ".$funonclick." value=\"".$lang_index['submit_fun']."\" />&nbsp;<input type=\"button\" class='btn' name='dull' id='dull' ".$dullonclick." value=\"".$lang_index['submit_dull']."\" />")."</span><span id=\"voteaccept\" style=\"display: none;\">".$lang_index['text_vote_accepted']."</span>");
//		}
//		print("</td></tr></table>");
//	}
//}
// ------------- end: fun box ------------------//



// ------------- start: hot and classic movies ------------------//
//
//if ($showextinfo['imdb'] == 'yes' && ($showmovies['hot'] == "yes" || $showmovies['classic'] == "yes"))
//{
//	$type = array('hot', 'classic');
//	foreach($type as $type_each)
//	{
//		if($showmovies[$type_each] == 'yes' && (!isset($CURUSER) || $CURUSER['show' . $type_each] == 'yes'))
//		{
//			$Cache->new_page($type_each.'_resources', 900, true);
//			if (!$Cache->get_page())
//			{
//				$Cache->add_whole_row();
//
//				$imdbcfg = new imdb_config();
//				$res = sql_query("SELECT * FROM torrents WHERE picktype = " . sqlesc($type_each) . " AND seeders > 0 AND url != '' ORDER BY id DESC LIMIT 30") or sqlerr(__FILE__, __LINE__);
//				if (mysql_num_rows($res) > 0)
//				{
//					$movies_list = "";
//					$count = 0;
//					$allImdb = array();
//					while($array = mysql_fetch_array($res))
//					{
//						$pro_torrent = get_torrent_promotion_append($array[sp_state],'word');
//						if ($imdb_id = parse_imdb_id($array["url"]))
//						{
//							if (array_search($imdb_id, $allImdb) !== false) { //a torrent with the same IMDb url already exists
//								continue;
//							}
//							$allImdb[]=$imdb_id;
//							$photo_url = $imdbcfg->photodir . $imdb_id. $imdbcfg->imageext;
//
//							if (file_exists($photo_url))
//								$thumbnail = "<img width=\"101\" height=\"140\" src=\"".$photo_url."\" border=\"0\" alt=\"poster\" />";
//							else continue;
//						}
//						else continue;
//						$thumbnail = "<a href=\"details.php?id=" . $array['id'] . "&amp;hit=1\" onmouseover=\"domTT_activate(this, event, 'content', '" . htmlspecialchars("<font class=\'big\'><b>" . (addslashes($array['name'] . $pro_torrent)) . "</b></font><br /><font class=\'medium\'>".(addslashes($array['small_descr'])) ."</font>"). "', 'trail', true, 'delay', 0,'lifetime',5000,'styleClass','niceTitle','maxWidth', 600);\">" . $thumbnail . "</a>";
//						$movies_list .= $thumbnail;
//						$count++;
//						if ($count >= 9)
//							break;
//					}
//?>
<!--<h2>--><?php //echo $lang_index['text_' . $type_each . 'movies'] ?><!--</h2>-->
<!--<table width="100%"  cellspacing="0" cellpadding="5"><tr><td class="text nowrap" align="center">-->
<?php //echo $movies_list ?><!--</td></tr></table>-->
<?php
//				}
//				$Cache->end_whole_row();
//				$Cache->cache_page();
//			}
//			echo $Cache->next_row();
//		}
//	}
//}
//
// ------------- end: hot and classic movies ------------------//

main_content_start();
//  first  block
block_start();
col_start(5);
eve_block_start();
// ------------- start: new-boxes ------------------//
panel_head_start();
print("<h3 class=\"panel-title\"><span class=' glyphicon glyphicon-bell '></span>&nbsp;".$lang_index['text_recent_news'].(get_user_class() >= $newsmanage_class ? "&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"altlink\" href=\"news.php\"><span class='icon-edit'></span>".$lang_index['text_news_page']."</a></font>" : "")."</h3>");
panel_head_end();
$Cache->new_page('recent_news', 86400, true);
if (!$Cache->get_page()){
	$res = sql_query("SELECT * FROM news ORDER BY added DESC LIMIT ".(int)$maxnewsnum_main) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0)
	{
		$Cache->add_whole_row();
		echo  "<div class=\"panel-body scroll\" style=\"overflow: auto; width: auto;height: 400px\">";
		print("<table class=\"table table-striped\">");
		$Cache->end_whole_row();
		$news_flag = 0;
		while($array = mysql_fetch_array($res))
		{
			$Cache->add_row();
			$Cache->add_part();
			if ($news_flag < 1) {
				print("<h4><a href=\"javascript: klappe_news('a".$array['id']."')\"><span class='icon-tags' style='color:#2DCB70'></span>&nbsp;"."<b>". $array['title'] . "</b>(".date("Y.m.d",strtotime($array['added'])).")</a></h4>");
				print("<div class='kas' id=\"ka".$array['id']."\" style=\"display: block;\"> ".format_comment($array["body"],0)." </div> ");
				$news_flag = $news_flag + 1;
			}
			else
			{
				print("<h4><a href=\"javascript: klappe_news('a".$array['id']."')\"><br /><span class='icon-tags' style='color:#2DCB70'></span>&nbsp;"."<b>". $array['title'] . "</b>(".date("Y.m.d",strtotime($array['added'])).")</a></h4>");
				print("<div class='alttext' id=\"ka".$array['id']."\" style=\"display: none;\"> ".format_comment($array["body"],0)." </div> ");
			}
			$Cache->end_part();
			$Cache->add_part();
			print("  &nbsp;<span class='icon-edit' style='color:#2DCB70'></span> <a class=\"faqlink\" href=\"news.php?action=edit&amp;newsid=" . $array['id'] . "\">".$lang_index['text_e']."</a>");
			print("  &nbsp;<span class='icon-trash' style='color: tomato'></span> <a class=\"faqlink\" href=\"news.php?action=delete&amp;newsid=" . $array['id'] . "\">".$lang_index['text_d']."</a>");
			$Cache->end_part();
			$Cache->end_row();
		}
		$Cache->break_loop();
		$Cache->add_whole_row();
//	echo "
//	<div class=\"slimScrollBar\" style=\"background: rgb(0, 0, 0); width: 8px; position: absolute; top: 17px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px; height: 249.231px;\"></div>
//	<div class=\"slimScrollRail\" style=\"width: 8px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;\"></div>
//	";
		print("</div></td></tr></table>\n");
		echo "</div>";
		//
		$Cache->end_whole_row();
	}
	$Cache->cache_page();
}
echo $Cache->next_row();
while($Cache->next_row()){
	echo $Cache->next_part();
	if (get_user_class() >= $newsmanage_class)
		echo $Cache->next_part();
}
echo $Cache->next_row();
eve_block_end();
// ------------- end : new box  ------------------//

// ------------- start: latest torrents ------------------//
eve_block_start();
if ($showlastxtorrents_main == "yes") {
	$result = sql_query("SELECT * FROM torrents where visible='yes' ORDER BY added DESC LIMIT 5") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($result) != 0) {
		panel_head_start();
		print ("<h3 class='panel-title'><span class='icon-cloud-upload' ></span>&nbsp;" . $lang_index['text_last_five_torrent'] . "</h3>");
		panel_head_end();
		print ("<table  class='table table-striped' width=\"100%\"  cellspacing=\"0\" cellpadding=\"5\">
<tr><td class=\"colhead\" >" . $lang_index['col_name'] . "</td><td class=\"colhead\" align=\"center\">" . $lang_index['col_seeder'] . "</td><td class=\"colhead\" align=\"center\">" . $lang_index['col_leecher'] . "</td></tr>");

		while ($row = mysql_fetch_assoc($result)) {
			print ("<tr><a href=\"details.php?id=" . $row['id'] . "&amp;hit=1\"><td><a class='sans' href=\"details.php?id=" . $row['id'] . "&amp;hit=1\">" . htmlspecialchars($row['name']) . "</td></a><td align=\"center\" class='sans'>" . $row['seeders'] . "</td><td align=\"center\" class='sans'>" . $row['leechers'] . "</td></tr>");
		}
		print ("</table>");
	}
}
eve_block_end();
// ------------- end: latest torrents ------------------//

// ------------- start: links ------------------//-->
eve_block_start();
panel_head_start();
print("<h3 class='panel-title' ><span class='icon-link'></span>&nbsp;" . $lang_index['text_links']);
if (get_user_class() >= $applylink_class)
	print("&nbsp;<a class=\"altlink\"  href=\"linksmanage.php?action=apply\">" . $lang_index['text_apply_for_link'] . "</a>");
if (get_user_class() >= $linkmanage_class) {
	print("  &nbsp;<a class=\"altlink\" href=\"linksmanage.php\">" . $lang_index['text_manage_links'] . "</a>");
}
print("</h3>");
panel_head_end();

$Cache->new_page('links', 86400, false);
if (!$Cache->get_page()) {
	$Cache->add_whole_row();
	$res = sql_query("SELECT * FROM links ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0) {
		$links = "";
		while ($array = mysql_fetch_array($res)) {
			$links .= "&nbsp;&nbsp;&nbsp;<a class='altlink' href=\"" . $array['url'] . "\" title=\"" . $array['title'] . "\" target=\"_blank\">" . $array['name'] . "</a>&nbsp;&nbsp;&nbsp;";
		}
		print("<table class='table table-striped' width=\"100%\"><tr><td class=\"text\" >" . trim($links) . "</td></tr></table>");
	}
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
col_end();
eve_block_end();
// ------------- end: links ------------------//

// ------------- start: shut box box ------------------//
col_start(7);
eve_block_start();
if ($showshoutbox_main == "yes") {
	panel_head_start();
	?>
	<h3 class="panel-title"><sapn class="icon-comments"></sapn><?php echo $lang_index['text_shoutbox'] ?></h3>
	<?php
	panel_head_end();
	print("<table style='width: 100%;height: 805px;'><tr><td class=\"text\" style='width: 100px'>\n");
	print("<iframe src='shoutbox.php?type=shoutbox' width='100%' height='520' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
	print("<form action='shoutbox.php' method='get' target='sbox' name='shbox'>\n");
	print("<label for='shbox_text'>" . $lang_index['text_message'] . "</label>
	<div class=\"vtop td-fat pd5\">
	<textarea class=\"input fullwidth inputor\" name='shbox_text' id='shbox_text' rows=\"2\" placeholder=\"请输入聊天内容\" style=\"height: 4em; background-color: rgb(255, 255, 255);width:100%\"></textarea>
	</div>
	<input style='margin:7px' type='submit' id='hbsubmit' class=\"btn btn-success\" name='shout' value=\"" . $lang_index['sumbit_shout'] . "\" />");
	if ($CURUSER['hidehb'] != 'yes' && $showhelpbox_main == 'yes')
		print("<input type='submit' class='btn' name='toguest' value=\"" . $lang_index['sumbit_to_guest'] . "\" />");
	print("<input type='reset' class=\"btn btn-danger\" value=\"" . $lang_index['submit_clear'] . "\" /> <input type='hidden' name='sent' value='yes' /><input type='hidden' name='type' value='shoutbox' /><br />\n");
	print(smile_row("shbox", "shbox_text"));
	print("</form></td></tr></table>");
}

// ------------- end: shut box ------------------//
col_end();
block_end();


// ------------- start: stats ------------------//
//echo "<div class='row'>";

if ($showstats_main == "yes") {
	?>
	<h4>
		<sapn class=" icon-dashboard"></sapn><?php echo $lang_index['text_tracker_statistics'] ?></h4>
	<table width="100%" class="table table-bordered" id="statustable">
		<tr>
			<!--<table width="60%" class="table table-bordered"  cellspacing="0" cellpadding="10">-->
			<?php
			$Cache->new_page('stats_users', 3000, true);
			if (!$Cache->get_page()){
			$Cache->add_whole_row();
			$registered = number_format(get_row_count("users"));
			$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
			$totalonlinetoday = number_format(get_row_count("users", "WHERE last_access >= " . sqlesc(date("Y-m-d H:i:s", (TIMENOW - 86400)))));
			$totalonlineweek = number_format(get_row_count("users", "WHERE last_access >= " . sqlesc(date("Y-m-d H:i:s", (TIMENOW - 604800)))));
			$VIP = number_format(get_row_count("users", "WHERE class=" . UC_VIP));
			$donated = number_format(get_row_count("users", "WHERE donor = 'yes'"));
			$warned = number_format(get_row_count("users", "WHERE warned='yes'"));
			$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
			$registered_male = number_format(get_row_count("users", "WHERE gender='Male'"));
			$registered_female = number_format(get_row_count("users", "WHERE gender='Female'"));
			?>
		<tr>
			<?php
			twotd($lang_index['row_users_active_today'], $totalonlinetoday);
			twotd($lang_index['row_users_active_this_week'], $totalonlineweek);
			?>
		</tr>
		<tr>
			<?php
			twotd($lang_index['row_registered_users'], $registered . " / " . number_format($maxusers));
			twotd($lang_index['row_unconfirmed_users'], $unverified);
			?>
		</tr>
		<tr>
			<?php
			twotd(get_user_class_name(UC_VIP, false, false, true), $VIP);
			twotd($lang_index['row_donors'], $donated);
			?>
		</tr>
		<tr>
			<?php
			twotd($lang_index['row_warned_users'], $warned);
			twotd($lang_index['row_banned_users'], $disabled);
			?>
		</tr>
		<tr>
			<?php
			twotd($lang_index['row_male_users'], $registered_male);
			twotd($lang_index['row_female_users'], $registered_female);
			?>
		</tr>
		<?php
		$Cache->end_whole_row();
		$Cache->cache_page();
		}
		echo $Cache->next_row();
		?>
		<tr>
			<td colspan="4" class="rowhead">&nbsp;</td>
		</tr>
		<?php
		$Cache->new_page('stats_torrents', 1800, true);
		if (!$Cache->get_page()) {
			$Cache->add_whole_row();
			$torrents = number_format(get_row_count("torrents"));
			$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));
			$seeders = get_row_count("peers", "WHERE seeder='yes'");
			$leechers = get_row_count("peers", "WHERE seeder='no'");
			if ($leechers == 0)
				$ratio = 0;
			else
				$ratio = round($seeders / $leechers * 100);
			$activewebusernow = get_row_count("users", "WHERE last_access >= " . sqlesc(date("Y-m-d H:i:s", (TIMENOW - 900))));
			$activewebusernow = number_format($activewebusernow);
			$activetrackerusernow = number_format(get_single_value("peers", "COUNT(DISTINCT(userid))"));
			$peers = number_format($seeders + $leechers);
			$seeders = number_format($seeders);
			$leechers = number_format($leechers);
			$totaltorrentssize = mksize(get_row_sum("torrents", "size"));
			$totaluploaded = get_row_sum("users", "uploaded");
			$totaldownloaded = get_row_sum("users", "downloaded");
			$totaldata = $totaldownloaded + $totaluploaded;
			?>
			<tr>
				<?php
				twotd($lang_index['row_torrents'], $torrents);
				twotd($lang_index['row_dead_torrents'], $dead);
				?>
			</tr>
			<tr>
				<?php
				twotd($lang_index['row_seeders'], $seeders);
				twotd($lang_index['row_leechers'], $leechers);
				?>
			</tr>
			<tr>
				<?php
				twotd($lang_index['row_peers'], $peers);
				twotd($lang_index['row_seeder_leecher_ratio'], $ratio . "%");
				?>
			</tr>
			<tr>
				<?php
				twotd($lang_index['row_active_browsing_users'], $activewebusernow);
				twotd($lang_index['row_tracker_active_users'], $activetrackerusernow);
				?>
			</tr>
			<tr>
				<?php
				twotd($lang_index['row_total_size_of_torrents'], $totaltorrentssize);
				twotd($lang_index['row_total_uploaded'], mksize($totaluploaded));
				?>
			</tr>
			<tr>
				<?php
				twotd($lang_index['row_total_downloaded'], mksize($totaldownloaded));
				twotd($lang_index['row_total_data'], mksize($totaldata));
				?>
			</tr>
			<?php
			$Cache->end_whole_row();
			$Cache->cache_page();
		}
		echo $Cache->next_row();
		?>
		<tr>
			<td colspan="4" class="rowhead">&nbsp;</td>
		</tr>
		<?php
		$Cache->new_page('stats_classes', 4535, true);
		if (!$Cache->get_page()) {
			$Cache->add_whole_row();
			$peasants = number_format(get_row_count("users", "WHERE class=" . UC_PEASANT));
			$users = number_format(get_row_count("users", "WHERE class=" . UC_USER));
			$powerusers = number_format(get_row_count("users", "WHERE class=" . UC_POWER_USER));
			$eliteusers = number_format(get_row_count("users", "WHERE class=" . UC_ELITE_USER));
			$crazyusers = number_format(get_row_count("users", "WHERE class=" . UC_CRAZY_USER));
			$insaneusers = number_format(get_row_count("users", "WHERE class=" . UC_INSANE_USER));
			$veteranusers = number_format(get_row_count("users", "WHERE class=" . UC_VETERAN_USER));
			$extremeusers = number_format(get_row_count("users", "WHERE class=" . UC_EXTREME_USER));
			$ultimateusers = number_format(get_row_count("users", "WHERE class=" . UC_ULTIMATE_USER));
			$nexusmasters = number_format(get_row_count("users", "WHERE class=" . UC_NEXUS_MASTER));
			?>
			<tr>
				<?php
				twotd(get_user_class_name(UC_PEASANT, false, false, true), $peasants);
				twotd(get_user_class_name(UC_USER, false, false, true), $users);
				?>
			</tr>
			<tr>
				<?php
				twotd(get_user_class_name(UC_POWER_USER, false, false, true), $powerusers);
				twotd(get_user_class_name(UC_ELITE_USER, false, false, true), $eliteusers);
				?>
			</tr>
			<tr>
				<?php
				twotd(get_user_class_name(UC_CRAZY_USER, false, false, true), $crazyusers);
				twotd(get_user_class_name(UC_INSANE_USER, false, false, true), $insaneusers);
				?>
			</tr>
			<tr>
				<?php
				twotd(get_user_class_name(UC_VETERAN_USER, false, false, true), $veteranusers);
				twotd(get_user_class_name(UC_EXTREME_USER, false, false, true), $extremeusers);
				?>
			</tr>
			<tr>
				<?php
				twotd(get_user_class_name(UC_ULTIMATE_USER, false, false, true), $ultimateusers);
				twotd(get_user_class_name(UC_NEXUS_MASTER, false, false, true), $nexusmasters);
				?>
			</tr>
			<?php
			$Cache->end_whole_row();
			$Cache->cache_page();
		}
		echo $Cache->next_row();
		?>

		</td></tr></table>
	<?php
}

// ------------- end: stats ------------------//


// ------------- start: polls ------------------//

if ($CURUSER && $showpolls_main == "yes") {
	// Get current poll
	if (!$arr = $Cache->get_value('current_poll_content')) {
		$res = sql_query("SELECT * FROM polls ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_array($res);
		$Cache->cache_value('current_poll_content', $arr, 7226);
	}
	if (!$arr)
		$pollexists = false;
	else $pollexists = true;

	print("<h4 style='float: left'><sapn class='icon-bookmark'></sapn>" . $lang_index['text_polls']);

	if (get_user_class() >= $pollmanage_class) {
		print("&nbsp;<sapn class='icon-pencil'></sapn><a class=\"altlink\" href=\"makepoll.php?returnto=main\"><b>" . $lang_index['text_new'] . "</b></a></h>\n");
		if ($pollexists) {
			print("&nbsp;<sapn class='icon-edit'></sapn><a class=\"altlink\" href=\"makepoll.php?action=edit&amp;pollid=" . $arr[id] . "&amp;returnto=main\"><b>" . $lang_index['text_edit'] . "</b></a>\n");
			print("&nbsp;<span class='icon-trash'></span><a class=\"altlink\" href=\"log.php?action=poll&amp;do=delete&amp;pollid=" . $arr[id] . "&amp;returnto=main\"><b>" . $lang_index['text_delete'] . "</b></a>");
			print("&nbsp;<span class='icon-spinner'></span><a class=\"altlink\" href=\"polloverview.php?id=" . $arr[id] . "\"><b>" . $lang_index['text_detail'] . "</b></a>");
		}
		print("</font>");
	}
	print("</h4>");
	if ($pollexists) {
		$pollid = 0 + $arr["id"];
		$userid = 0 + $CURUSER["id"];
		$question = $arr["question"];
		$o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
			$arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
			$arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
			$arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

		print("<table width=\"100%\" ><tr style='border: 0px'><td style='border: 0px'>\n");
		print("<table width=\"100%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">");
		print("<h4 style='text-align: center'>" . $question . "</h4>\n");

		// Check if user has already voted
		$res = sql_query("SELECT selection FROM pollanswers WHERE pollid=" . sqlesc($pollid) . " AND userid=" . sqlesc($CURUSER["id"])) or sqlerr();
		$voted = mysql_fetch_assoc($res);
		if ($voted) //user has already voted
		{
			$uservote = $voted["selection"];
			$Cache->new_page('current_poll_result', 3652, true);
			if (!$Cache->get_page()) {
				// we reserve 255 for blank vote.
				$res = sql_query("SELECT selection FROM pollanswers WHERE pollid=" . sqlesc($pollid) . " AND selection < 20") or sqlerr();

				$tvotes = mysql_num_rows($res);

				$vs = array();
				$os = array();

				// Count votes
				while ($arr2 = mysql_fetch_row($res))
					$vs[$arr2[0]]++;

				reset($o);
				for ($i = 0; $i < count($o); ++$i) {
					if ($o[$i])
						$os[$i] = array($vs[$i], $o[$i], $i);
				}

				function srt($a, $b)
				{
					if ($a[0] > $b[0]) return -1;
					if ($a[0] < $b[0]) return 1;
					return 0;
				}

				// now os is an array like this: array(array(123, "Option 1", 1), array(45, "Option 2", 2))
				$Cache->add_whole_row();
				print("<table class=\"main\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
				$Cache->end_whole_row();
				$i = 0;
				while ($a = $os[$i]) {
					if ($tvotes == 0)
						$p = 0;
					else
						$p = round($a[0] / $tvotes * 100);
					$Cache->add_row();
					$Cache->add_part();
					print("<tr><td width=\"1%\" class=\"embedded nowrap\">" . $a[1] . "&nbsp;&nbsp;</td><td width=\"99%\" class=\"embedded nowrap\"><img class=\"bar_end\" src=\"pic/trans.gif\" alt=\"\" /><img ");
					$Cache->end_part();
					$Cache->add_part();
					print(" src=\"pic/trans.gif\" style=\"width: " . ($p * 3) . "px;\" alt=\"\" /><img class=\"bar_end\" src=\"pic/trans.gif\" alt=\"\" /> $p%</td></tr>\n");
					$Cache->end_part();
					$Cache->end_row();
					++$i;
				}
				$Cache->break_loop();
				$Cache->add_whole_row();
				print("</table>\n");
				$tvotes = number_format($tvotes);
//				print("<p align=\"center\">".$lang_index['text_votes']." ".$tvotes."</p>\n");
				$Cache->end_whole_row();
				$Cache->cache_page();
			}
			echo $Cache->next_row();
			$i = 0;
			while ($Cache->next_row()) {
				echo $Cache->next_part();
				if ($i == $uservote)
					echo "class=\"sltbar\"";
				else
					echo "class=\"unsltbar\"";
				echo $Cache->next_part();
				$i++;
			}
			echo $Cache->next_row();
		} else //user has not voted yet
		{
			print("<form method=\"post\" action=\"index.php\">\n");
			$i = 0;
			while ($a = $o[$i]) {
				print("<label class=\"checkbox\"><input style='margin-left: 20%' type=\"radio\" name=\"choice\" value=\"" . $i . "\">" . $a . "<br /></label>\n");
				++$i;
			}
			print("<br />");
			print("<label class=\"checkbox\"><input style='margin-left: 20%' type=\"radio\" name=\"choice\" value=\"255\">" . $lang_index['radio_blank_vote'] . "<br />\n");
			print("<p align=\"center\"><input  type=\"submit\" class=\"btn\" value=\"" . $lang_index['submit_vote'] . "\" /></p></label>");
		}
		print("</table>");

		if ($voted && get_user_class() >= $log_class)
			print("<p align=\"center\"><a href=\"log.php?action=poll\">" . $lang_index['text_previous_polls'] . "</a></p>\n");

		print("	</table>");
	}
}

// ------------- end: polls ------------------//

// ------------- start: forums post ------------------//

//if ($showlastxforumposts_main == "yes" && $CURUSER) {
	$res = sql_query("SELECT posts.id AS pid, posts.userid AS userpost, posts.added, topics.id AS tid, topics.subject, topics.forumid, topics.views, forums.name FROM posts, topics, forums WHERE posts.topicid = topics.id AND topics.forumid = forums.id AND minclassread <=" . sqlesc(get_user_class()) . " ORDER BY posts.id DESC LIMIT 5") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) != 0) {
		print("<h4 class='panel-title'>" . $lang_index['text_last_five_posts'] . "</h4>");
		print("
<table style='margin-top: 10px' class='table table-striped'  width=\"100%\"  cellspacing=\"0\" cellpadding=\"5\">
<tr>
<td class=\"colhead\" align=\"left\">" . $lang_index['col_topic_title'] . "</td>
<td class=\"colhead\" align=\"center\">" . $lang_index['col_view'] . "</td>
<td class=\"colhead\" align=\"center\">" . $lang_index['col_author'] . "</td>
<td class=\"colhead\" align=\"left\">" . $lang_index['col_posted_at'] . "</td>
</tr>");
		echo "
			<style>
				.links-blue{
				    font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;
				    font-size: 14px;
				    color: #337ab7;
					}
			</style>
		";
		while ($postsx = mysql_fetch_assoc($res)) {

			print("
<tr>
<td>
	[<a class='links-blue' href=\"forums.php ? action = viewforum & amp;forumid =  '. $postsx[forumid].\" >" . htmlspecialchars($postsx["name"]) . "</a>]
	<a class='links-blue' href=\"forums.php?action=viewtopic&amp;topicid=" . $postsx["tid"] . "&amp;page=p" . $postsx["pid"] . "#pid" . $postsx["pid"] . "\">" . htmlspecialchars($postsx["subject"]) . "</a>
</td>
<td align=\"center\">" . $postsx["views"] . "</td><td align=\"center\">" . get_username($postsx["userpost"]) . "</td>
<td>" . gettime($postsx["added"]) . "</td>
</tr>");
		}
		print("</table>");
	}
//}

// ------------- end: latest forum posts ------------------//



// ------------- start: tracker load   服务器负载    ---//
//if ($showtrackerload == "yes") {
//$uptimeresult=exec('uptime');
//if ($uptimeresult){
//?>
<!--<h2>--><?php //echo $lang_index['text_tracker_load'] ?><!--</h2>-->
<!--<!--<table width="100%"  cellspacing="0" cellpadding="10"><tr><td class="text" align="center">-->
<?php
//			//uptime, work in *nix system
//			print ("<div align=\"center\">" . trim($uptimeresult) . "</div>");
//			print("</td></tr></table>");
//			}
//			}
//?>
<!--// ------------- end: tracker load ------------------//-->

<!--// ------------- start: disclaimer  免责声明   ----------//-->
<?php  ?>
<h4 style="float: left"><span class=" icon-tasks"></span><?php echo $lang_index['text_disclaimer'] ?></h4>
<table width="100%">
	<tr>
		<td style="border: 0px">
			<?php echo "<blockquote style='font-size: 12px'>" . $lang_index['text_disclaimer_content'] . "</blockquote>" ?></td>
	</tr>
</table>
<?php  ?>
<!--// ------------- end: disclaimer ------------------//



<?php
			
			panel_end();
// ------------- start: browser, client and code note ------------------//
?>
<table width="100%" class="main" border="0" cellspacing="0" cellpadding="0"><tr><td class="embedded">
<div align="center"><br /><font class="medium"><?php echo $lang_index['text_browser_note'] ?></font></div>
<!--<div align="center"><a href="http://www.nexusphp.com" title="--><?php //echo PROJECTNAME?><!--" target="_blank"><img src="pic/nexus.png" alt="--><?php //echo PROJECTNAME?><!--" /></a></div>-->
</td></tr></table>
<?php
// ------------- end: browser, client and code note ------------------//
if ($CURUSER)
	$USERUPDATESET[] = "last_home = ".sqlesc(date("Y-m-d H:i:s"));
$Cache->delete_value('user_'.$CURUSER["id"].'_unread_news_count');
end_main_frame();
stdfoot();
?>
