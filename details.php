<?php
ob_start(); //Do not delete this line
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
if ($showextinfo['imdb'] == 'yes')
	require_once("imdb/imdb.class.php");
loggedinorreturn();

$id = 0 + $_GET["id"];
int_check($id);
if (!isset($id) || !$id)
die();
$res = sql_query("SELECT torrents.cache_stamp,torrents.category, torrents.sp_state, torrents.url, torrents.small_descr, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, nfo, LENGTH(torrents.nfo) AS nfosz, torrents.last_action, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, torrents.anonymous, categories.name AS cat_name, sources.name AS source_name, media.name AS medium_name, codecs.name AS codec_name, standards.name AS standard_name, processings.name AS processing_name, teams.name AS team_name, audiocodecs.name AS audiocodec_name FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN sources ON torrents.source = sources.id LEFT JOIN media ON torrents.medium = media.id LEFT JOIN codecs ON torrents.codec = codecs.id LEFT JOIN standards ON torrents.standard = standards.id LEFT JOIN processings ON torrents.processing = processings.id LEFT JOIN teams ON torrents.team = teams.id LEFT JOIN audiocodecs ON torrents.audiocodec = audiocodecs.id WHERE torrents.id = $id LIMIT 1") or sqlerr();
$row = mysql_fetch_array($res);


	if ($row['category'] == '403' ||$row['category'] == '402' ||$row['category'] == '401' || $row['category'] == '405' || $row['category'] == '407' || $row['category'] == '410') {
		$v6button =
			"<script type=\"text/javascript\">function play(){window.location.href='6xvod://{$id}&ty=1&ro=1&id={$id}&ua={$CURUSER['passkey']}';}</script>" . "&nbsp;&nbsp;&nbsp;<a class='btn btn-circle green-turquoise' style='color:white' href=\"v6player.php?id={$id}&u={$CURUSER['passkey']}\" target=\"_blank\"> 直接播放</a>";
	} else {
		$v6button = "";
	}
if (get_user_class() >= $torrentmanage_class || $CURUSER["id"] == $row["owner"])
$owned = 1;
else $owned = 0;

if (!$row)
	stderr($lang_details['std_error'], $lang_details['std_no_torrent_id']);
elseif ($row['banned'] == 'yes' && get_user_class() < $seebanned_class)
	permissiondenied();
else {
	if ($_GET["hit"]) {
		sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
	}

	if (!isset($_GET["cmtpage"])) {
		stdhead();
		if ($_GET["uploaded"])
		{
			print("<h1 align=\"center\">".$lang_details['text_successfully_uploaded']."</h1>");
			print("<p>".$lang_details['text_redownload_torrent_note']."</p>");
			header("refresh: 1; url=download.php?id=$id");
		}
		elseif ($_GET["edited"]) {
			print("<h1 align=\"center\">".$lang_details['text_successfully_edited']."</h1>");
			if (isset($_GET["returnto"]))
				print("<p>".$lang_details['text_go_back'] . "<a href=\"".htmlspecialchars($_GET["returnto"])."\">" . $lang_details['text_whence_you_came']."</a></p>");
		}
		$sp_torrent = get_torrent_promotion_append($row[sp_state],'word');

		$s=htmlspecialchars($row["name"]).($sp_torrent ? "&nbsp;&nbsp;&nbsp;".$sp_torrent : "");
		print("<h1 align=\"center\" id=\"top\">".$s."</h1>\n");







		echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-list-alt font-green-dark'></i>
                                <span class='caption-subject font-green-dark sbold uppercase'>种子详情页 </span>
                            </h2>
                        </div>
                    </div>
                    <div class='portlet-body form'>";
		print("<form class='form-horizontal form-bordered'>");
		echo "<div class='form-body'>";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>上传信息 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";

						if ($CURUSER['timetype'] != 'timealive')
							$uploadtime = $lang_details['text_at'] . $row['added'];
						else $uploadtime = $lang_details['text_blank'] . gettime($row['added'], true, false);

						if ($row['anonymous'] == 'yes') {
							if (get_user_class() < $viewanonymous_class){
							echo "<img class='img img-circle' height = '60px' width = '60px' src =" . get_user_avatar_url() . ">";
							echo $lang_details['text_anonymous'].$uploadtime;
							} else{
								echo "<img class='img img-circle' height = '60px' width = '60px' src =" . get_user_avatar_url($row['owner']) . ">";
								echo $lang_details['text_anonymous'] ." (". get_username($row['owner'], false, true, true, false, false, true) . ")" . $uploadtime;
							}
						} else {
							echo "<img class='img img-circle' height = '60px' width = '60px' src =".get_user_avatar_url($row['owner']) . ">";
							echo (isset($row['owner']) ? get_username($row['owner'], false, true, true, false, false, true) : "<i>" . $lang_details['text_unknown'] . "</i>");
							echo $uploadtime;
						}
		echo "                 </div>
                    </div>
         </div>
";

		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>下载 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		if ($CURUSER["id"] == $row["owner"])
			$CURUSER["downloadpos"] = "yes";
		if ($CURUSER["downloadpos"] != "no") {
			if ($CURUSER['timetype'] != 'timealive')
				$uploadtime = $lang_details['text_at'] . $row['added'];
			else $uploadtime = $lang_details['text_blank'] . gettime($row['added'], true, false);
			print("<a class=\"index\" href=\"download.php?id=$id\">" . htmlspecialchars($torrentnameprefix . "." . $row["save_as"]) . ".torrent</a>&nbsp;&nbsp;<a id=\"bookmark0\" href=\"javascript: bookmark(" . $row['id'] . ",0);\">" . get_torrent_bookmark_state($CURUSER['id'], $row['id'], false) . "</a>&nbsp;&nbsp;&nbsp;");
			print("</td></tr>");
		} else
			tr($lang_details['row_download'], $lang_details['text_downloading_not_allowed']);
echo "              			 </div>
                    </div>
         </div>
";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>副标题 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		if ($smalldescription_main == 'yes')
			 echo htmlspecialchars(trim($row["small_descr"]));
		echo "              			 </div>
                    </div>
         </div>
";

		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>基本信息 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		$size_info =  mksize($row["size"]);
		$type_info =  $row["cat_name"];
		echo "大小： <span class='label label-success'>$size_info</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分类： <span class='label label-warning'>$type_info</span>";

		echo "              			 </div>
                    </div>
         </div>
";
        if ($row["type"] == "multi")
        {
            $files_info = "<b>".$lang_details['text_num_files']."</b>". $row["numfiles"] . $lang_details['text_files'] . "<br />";
            $files_info .= "<span id=\"showfl\"><a href=\"javascript: viewfilelist(".$id.")\" >".$lang_details['text_see_full_list']."</a></span><span id=\"hidefl\" style=\"display: none;\"><a href=\"javascript: hidefilelist()\">".$lang_details['text_hide_list']."</a></span>";
        }
        function hex_esc($matches) {
            return sprintf("%02x", ord($matches[0]));
        }
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>文件列表 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
//		echo "$files_info";
        tr($lang_details['row_torrent_info'], "<table><tr>" . ($files_info != "" ? "<td class=\"no_border_wide\">" . $files_info . "</td>" : "") . "<td class=\"no_border_wide\"><b>".$lang_details['row_info_hash'].":</b>&nbsp;".preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"]))."</td>". (get_user_class() >= $torrentstructure_class ? "<td class=\"no_border_wide\"></td>" : "") . "</tr></table><span id='filelist'></span>",1);

        echo "              			 </div>
                    </div>
         </div>
";

		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>操作 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		$url = "edit.php?id=" . $row["id"];
		if (isset($_GET["returnto"])) {
			$url .= "&returnto=" . rawurlencode($_GET["returnto"]);
		}
		$editlink = "a class='btn btn-circle purple-medium' style='color:white' title=\"" . $lang_details['title_edit_torrent'] . "\" href=\"$url\"";
		if ($CURUSER["downloadpos"] != "no")
			$download = "&nbsp;&nbsp;&nbsp;<a class='btn btn-circle blue-sharp' style='color:white' title=\"" . $lang_details['title_download_torrent'] . "\" href=\"download.php?id=" . $id . "\"><font >" . $lang_details['text_download_torrent'] . "</font></a>";
		else $download = "";
		echo  $download . ($owned == 1 ? "&nbsp;&nbsp;&nbsp;<$editlink></span>&nbsp;<font >" . $lang_details['text_edit_torrent'] . "</font></a>" : "") . (get_user_class() >= $askreseed_class && $row[seeders] == 0 ? "&nbsp;&nbsp;&nbsp;<a class='btn btn-circle yellow-gold' style='color:white' title=\"" . $lang_details['title_ask_for_reseed'] . "\" href=\"takereseed.php?reseedid=$id\"><font >"
				. $lang_details['text_ask_for_reseed'] . "</font></a>&nbsp;</span>&nbsp;" : "") . "<a style='color:white' title=\""
			. $lang_details['title_report_torrent'] . "\" class='btn btn-circle red-flamingo' href=\"report.php?torrent=$id\"><font >"
			. $lang_details['text_report_torrent'] . "</font></a>" . $v6button;
		echo "              			 </div>
                    </div>
         </div>
";

		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>简介 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		if ($CURUSER['showdescription'] != 'no' && !empty($row["descr"])) {

			 echo "<div class='well well-lg'>".format_comment($row["descr"]);
			echo "</div>";
		}
		echo "              			 </div>
                    </div>
         </div>
";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>热度 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		echo "
		浏览次数： 	<span class='label label-info'>$row[views]</span>
		点击次数： 	<span class='label label-success'>$row[hits]</span>
		完成次数： 	<span class='label label-warning'><a style='color: #fff;' href=\"viewsnatches.php?id =\".$id>$row[times_completed]</a></span>
		上次活动时间： 	<span class='label label-default'>".gettime($row[last_action])."</span>
		";
		echo "              			 </div>
                    </div>
         </div>
";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>字幕 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";

		// ---------------- start subtitle block -------------------//
		$r = sql_query("SELECT subs.*, language.flagpic, language.lang_name FROM subs LEFT JOIN language ON subs.lang_id=language.id WHERE torrent_id = " . sqlesc($row["id"]) . " ORDER BY subs.lang_id ASC") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($r) > 0) {
			while ($a = mysql_fetch_assoc($r)) {
				$lang = "<tr><td class=\"embedded\"><img border=\"0\" src=\"pic/flag/" . $a["flagpic"] . "\" alt=\"" . $a["lang_name"] . "\" title=\"" . $a["lang_name"] . "\" style=\"padding-bottom: 4px\" /></td>";
				$lang .= "<td class=\"embedded\">&nbsp;&nbsp;<a href=\"downloadsubs.php?torrentid=" . $a[torrent_id] . "&subid=" . $a[id] . "\"><u>" . $a["title"] . "</u></a>" . (get_user_class() >= $submanage_class || (get_user_class() >= $delownsub_class && $a["uppedby"] == $CURUSER["id"]) ? " <font class=\"small\"><a href=\"subtitles.php?delete=" . $a[id] . "\">[" . $lang_details['text_delete'] . "</a>]</font>" : "") . "</td><td class=\"embedded\">&nbsp;&nbsp;" . ($a["anonymous"] == 'yes' ? $lang_details['text_anonymous'] . (get_user_class() >= $viewanonymous_class ? get_username($a['uppedby'], false, true, true, false, true) : "") : get_username($a['uppedby'])) . "</td></tr>";
				print($lang);
			}
		} else
			print("<tr><td class=\"embedded\">" . $lang_details['text_no_subtitles'] . "</td></tr>");
		print("</table>");
		if ($CURUSER['id'] == $row['owner'] || get_user_class() >= $uploadsub_class) {
			//为了解决提交出去的时候没有form的bug特意增加的无用表单
			echo "
			<form class='form' method='' action=''>
			</form>
			";

			print("	
						<form method=post action=subtitles.php>
							<input type=\"hidden\" name=\"torrent_name\" value=\"" . $row["name"] . "\" />
							<input type=\"hidden\" name=\"detail_torrent_id\" value=\"" . $row["id"] . "\" />
							<input type=\"hidden\" name=\"in_detail\" value=\"in_detail\" />
							<input class='btn btn-success' type=\"submit\" value=\"" . $lang_details['submit_upload_subtitles'] . "\" />
						</form>
					");
		}
		$moviename = "";
		$imdb_id = parse_imdb_id($row["url"]);
		if ($imdb_id && $showextinfo['imdb'] == 'yes') {
			$thenumbers = $imdb_id;
			if (!$moviename = $Cache->get_value('imdb_id_' . $thenumbers . '_movie_name')) {
				$movie = new imdb ($thenumbers);
				$target = array('Title');
				switch ($movie->cachestate($target)) {
					case "1": {
						$moviename = $movie->title();
						break;
						$Cache->cache_value('imdb_id_' . $thenumbers . '_movie_name', $moviename, 1296000);
					}
					default:
						break;
				}
			}
		}
		print("
		<form class='form' method=\"get\" action=\"http://shooter.cn/sub/\" target=\"_blank\" >
			<input style='margin: 7px' class='form-control' type=\"text\" name=\"searchword\" id=\"keyword\"  value=\"" . $moviename . "\" />
			<br>
			<input class='btn btn-info pull-left' style='margin-right:7px' type=\"submit\" value=\"" . $lang_details['submit_search_at_shooter'] . "\" />
		</form>
		<form method=\"get\" action=\"http://www.opensubtitles.org/en/search2/\" target=\"_blank\">
			<input type=\"hidden\" id=\"moviename\" name=\"MovieName\" />
			<input type=\"hidden\" name=\"action\" value=\"search\" />
			<input type=\"hidden\" name=\"SubLanguageID\" value=\"all\" />
			<input class='btn btn-info' onclick=\"document.getElementById('moviename').value=document.getElementById('keyword').value;\" type=\"submit\" value=\"" . $lang_details['submit_search_at_opensubtitles'] . "\" />
		</form>
");
		print("</tr></table>");
		print("</td></tr>\n");
		// ---------------- end subtitle block -------------------//
		echo "              			 </div>
                    </div>
         </div>
";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>感谢 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		// ------------- start thanked-by block--------------//

		$torrentid = $id;
		$thanksby = "";
		$nothanks = "";
		$thanks_said = 0;
		$thanks_sql = sql_query("SELECT userid FROM thanks WHERE torrentid=" . sqlesc($torrentid) . " ORDER BY id DESC LIMIT 20");
		$thanksCount = get_row_count("thanks", "WHERE torrentid=" . sqlesc($torrentid));
		$thanks_all = mysql_num_rows($thanks_sql);
		if ($thanks_all) {
			while ($rows_t = mysql_fetch_array($thanks_sql)) {
				$thanks_userid = $rows_t["userid"];
				if ($rows_t["userid"] == $CURUSER['id']) {
					$thanks_said = 1;
				} else {
					$thanksby .= get_username($thanks_userid) . " ";
				}
			}
		} else $nothanks = $lang_details['text_no_thanks_added'];

		if (!$thanks_said) {
			$thanks_said = get_row_count("thanks", "WHERE torrentid=$torrentid AND userid=" . sqlesc($CURUSER['id']));
		}
		if ($thanks_said == 0) {
			$buttonvalue = " value=\"" . $lang_details['submit_say_thanks'] . "\"";
		} else {
			$buttonvalue = " value=\"" . $lang_details['submit_you_said_thanks'] . "\" disabled=\"disabled\"";
			$thanksby = get_username($CURUSER['id']) . " " . $thanksby;
		}
		$thanksbutton = "<input class=\"btn btn-success\" type=\"button\" id=\"saythanks\"  onclick=\"saythanks(" . $torrentid . ");\" " . $buttonvalue . " />";
		echo "<span id=\"thanksadded\" style=\"display: none;\"><input class=\"btn\" type=\"button\" value=\"" . $lang_details['text_thanks_added'] . "\" disabled=\"disabled\" /></span><span id=\"curuser\" style=\"display: none;\">" . get_username($CURUSER['id']) . " </span><span id=\"thanksbutton\">" . $thanksbutton . "</span>&nbsp;&nbsp;<span id=\"nothanks\">" . $nothanks . "</span><span id=\"addcuruser\"></span>" . $thanksby . ($thanks_all < $thanksCount ? $lang_details['text_and_more'] . $thanksCount . $lang_details['text_users_in_total'] : "");
		// ------------- end thanked-by block--------------//
		echo "              			 </div>
                    </div>
         </div>
";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>做种者 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
		echo "<span id=\"seeders\"></span><span id=\"leechers\"></span><span id=\"showpeer\">
<a class='btn btn-success ' style='color: white' href=\"javascript: viewpeerlist(" . $row['id'] . ");\" class=\"sublink\">查看列表</a>
</span><span id=\"hidepeer\" style=\"display: none;\">
<a class='btn btn-success ' style='color: white' href=\"javascript: hidepeerlist();\" class=\"sublink\">隐藏列表</a>
</span><br>
<div id=\"peercount\" style='margin-top:8px'>
<span class='label label-success'>" . $row['seeders'] . $lang_details['text_seeders'] . add_s($row['seeders']) . "</span> 
<span class='label label-info'>" . $row['leechers'] . $lang_details['text_leechers'] . add_s($row['leechers']) . "</span>
</div><div id=\"peerlist\"></div>";
		if ($_GET['dllist'] == 1) {
			$scronload = "viewpeerlist(" . $row['id'] . ")";
			echo "<script type=\"text/javascript\">\n";
			echo $scronload;
			echo "</script>";
		}

		echo "              			 </div>
                    </div>
         </div>
";
//		echo "
//        <div class='form-group' >
//                    <label class='control-label col-md-3'>评论 </label>
//                    <div class=col-md-9>
//                        <div class='input-group'>";
//		// -----------------COMMENT SECTION ---------------------//
////		if ($CURUSER['showcomment'] != 'no') {
//			$count = get_row_count("comments", "WHERE torrent=" . sqlesc($id));
//			if ($count) {
//				print("<br /><br />");
//				print("<h1 align=\"center\" id=\"startcomments\">" . $lang_details['h1_user_comments'] . "</h1>\n");
//				list($pagertop, $pagerbottom, $limit) = pager(10, $count, "details.php?id=$id&cmtpage=1&", array(lastpagedefault => 1), "page");
//
//				$subres = sql_query("SELECT id, text, user, added, editedby, editdate FROM comments WHERE torrent = $id ORDER BY id $limit") or sqlerr(__FILE__, __LINE__);
//				$allrows = array();
//				while ($subrow = mysql_fetch_array($subres)) {
//					$allrows[] = $subrow;
//				}
//				print($pagertop);
//				commenttable($allrows, "torrent", $id);
//				print($pagerbottom);
//			}
////		}
//		print("<br /><br />");
//		print ("
//<table class='table'>
//<tr>
//<td class=\"text\" align=\"center\"><b>" . $lang_details['text_quick_comment'] . "</b>
//<form id=\"compose\" name=\"comment\" method=\"post\" action=\"".htmlspecialchars("comment.php?action=add&type=torrent")."\" onsubmit=\"return postvalid(this);\">
//<input type=\"hidden\" name=\"pid\" value=\"" . $id . "\" />
//");
////		quickreply('comment', 'body', $lang_details['submit_add_comment']);
//		print("<textarea id='comment_short_text' name='body'  id=\"replaytext\" cols=\"100\" rows=\"8\" style=\"width: 450px\" onkeydown=\"ctrlenter(event,'compose','qr')\"></textarea>");
//		$res=sql_query("SELECT * FROM comment ") or sqlerr(__FILE__,__LINE__);
//		$comment_str="";
//		while ($arr=mysql_fetch_assoc($res)){
//			$comment_str.="<option name=\"$arr[id]\" >".$arr['text']."</option>";
//		}
//		echo "<select id='comment_short_select'>
//            $comment_str
//          </select>";
//		print("<br />");
//		print("<input type=\"submit\" id=\"qr\" class=\"btn\" value=\"评论\" />");
//		print("</form></td></tr></table>");
//
//
//		echo "              			 </div>
//                    </div>
//         </div>
//";

		$bwres = sql_query("SELECT uploadspeed.name AS upname, downloadspeed.name AS downname, isp.name AS ispname FROM users LEFT JOIN uploadspeed ON users.upload = uploadspeed.id LEFT JOIN downloadspeed ON users.download = downloadspeed.id LEFT JOIN isp ON users.isp = isp.id WHERE users.id=".$row['owner']);
		$bwrow = mysql_fetch_array($bwres);
		if ($bwrow['upname'] && $bwrow['downname'])
			tr($lang_details['row_uploader_bandwidth'], "<img class=\"speed_down\" src=\"pic/trans.gif\" alt=\"Downstream Rate\" /> ".$bwrow['downname']."&nbsp;&nbsp;&nbsp;&nbsp;<img class=\"speed_up\" src=\"pic/trans.gif\" alt=\"Upstream Rate\" /> ".$bwrow['upname']."&nbsp;&nbsp;&nbsp;&nbsp;".$bwrow['ispname'],1);
	}
	else {
		stdhead($lang_details['head_comments_for_torrent']."\"" . $row["name"] . "\"");
		print("<h1 id=\"top\" style='word-wrap: break-word;word-break: break-all'>".$lang_details['text_comments_for']."<a href=\"details.php?id=".$id."\">" . htmlspecialchars($row["name"]) . "</a></h1>\n");
	}
}


stdfoot();
