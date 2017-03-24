<?php
require "include/bittorrent.php";
dbconn(true);
require_once(get_langfile_path());
loggedinorreturn(true);
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($showpolls_main == "yes")
	{
		$choice = $_POST["choice"];
		if (isset($choice)){            if ($CURUSER && $choice != "" && $choice < 256 && $choice == floor($choice))
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
            else{
                stderr($lang_index['std_error'], $lang_index['std_option_unselected']);
            }
        }
	}
}


// ------------------------------------------  index strat ---------------------------------------------------------------
stdhead($lang_index['head_home']);
begin_main_frame();
main_content_start();
echo "<div class='row' id='indexmain'>";

echo "<div class='col-md-6'>";
// ------------- start: new-boxes ------------------//
echo "                     <div class='portlet light bordered '>
                                <div class=\"portlet-title\">
                                        <div class=\"caption \">
                                        <h3>
                                            <i class=\" glyphicon glyphicon-bell font-purple-plum\"></i>
                                            <span class=\"caption-subject bold uppercase font-purple-plum\">最新公告</span>
                                        </h3>
                                        </div>
                                        ";
if (get_user_class() >= $newsmanage_class) {
	echo "
                                        <div class=\"actions\">
                                            <a class=\"btn btn-circle btn-icon-only btn-default\" href=\"news.php\">
                                                <i class=\"icon-wrench\"></i>
                                            </a>
                                        </div>
";
}
echo "</div>";
$Cache->new_page('recent_news', 86400, true);
if (!$Cache->get_page()) {
	$res = sql_query("SELECT * FROM news ORDER BY added DESC LIMIT " . (int)$maxnewsnum_main) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0) {
		echo "<div class=\"panel-body scroll\" style=\"overflow: auto; width: auto;height: 400px\">";
		print("<table class=\"table\">");
		$news_flag = 0;
		echo "<div class='panel-group' id='accordion'>";
		while ($array = mysql_fetch_array($res)) {
			if ($news_flag < 1) {
				echo "
				<div class=\"panel panel-success\">
						<div class=\"panel-heading\" role=\"tab\" id=\"headingOne\">
							<h2 class=\"panel-title\">
								<a role=\"button\" data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapseOne\" aria-expanded=\"true\" aria-controls=\"collapseOne\">
									<span class='icon-tags' style='color:#2DCB70'></span>&nbsp;" . "" . $array['title'] . "(" . date("Y.m.d", strtotime($array['added'])) . ")
								</a>
							</h2>
						</div>
						<div id=\"collapseOne\" class=\"panel-collapse collapse in\" role=\"tabpanel\" aria-labelledby=\"headingOne\">
							<div class=\"panel-body\">
							<p>";
				echo format_comment($array["body"], 0);
				if (get_user_class() >= $newsmanage_class) {
					echo "
                        <br><br>
                            <span class='icon-edit' style='color:#2DCB70'></span> <a class=\"faqlink\" href=\"news.php?action=edit&amp;newsid=" . $array['id'] . "\">" . $lang_index['text_e'] . "</a>
                            <span class='icon-trash' style='color: tomato'></span> <a class=\"faqlink\" href=\"news.php?action=delete&amp;newsid=" . $array['id'] . "\">" . $lang_index['text_d'] . "</a>
                                
				";
				}
				echo "</p>
							</div>
						</div>
					</div>";
				$news_flag = $news_flag + 1;
			} else {

				echo "
				<div class=\"panel panel-success\">
						<div class=\"panel-heading\" role=\"tab\" id=\"headingTwo\">
							<h2 class=\"panel-title\">
								<a class=\"collapsed\" role=\"button\" data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapseTwo$array[id]\" aria-expanded=\"false\" aria-controls=\"collapseTwo\">
									<span class='icon-tags' style='color:#2DCB70'></span>&nbsp;" . "" . $array['title'] . "(" . date("Y.m.d", strtotime($array['added'])) . ")
								</a>
							</h2>
						</div>
						<div id=\"collapseTwo$array[id]\" class=\"panel-collapse collapse\" role=\"tabpanel\" aria-labelledby=\"headingTwo\">
							<div class=\"panel-body\"><p>";
				echo format_comment($array["body"], 0);
				if (get_user_class() >= $newsmanage_class) {
					echo "
                            <br><br>
                            <span class='icon-edit' style='color:#2DCB70'></span> <a class=\"faqlink\" href=\"news.php?action=edit&amp;newsid=" . $array['id'] . "\">" . $lang_index['text_e'] . "</a>
                            <span class='icon-trash' style='color: tomato'></span> <a class=\"faqlink\" href=\"news.php?action=delete&amp;newsid=" . $array['id'] . "\">" . $lang_index['text_d'] . "</a>
                           
				";
				}
				echo " </p></div>
						</div>
					</div>";
			}
		}
		print("</div></td></tr></table>\n");
		echo "</div></div>";
	}
	$Cache->cache_page();
}
echo $Cache->next_row();
while ($Cache->next_row()) {
	echo $Cache->next_part();
	if (get_user_class() >= $newsmanage_class)
		echo $Cache->next_part();
}
echo $Cache->next_row();
// ------------- end : new box  ------------------//
echo "            <div class='portlet light bordered '>
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                        <h3>
                                            <i class=\"icon-cloud-upload font-red-sunglo\"></i>
                                            <span class=\"caption-subject font-red-sunglo bold uppercase\">最近的种子</span>
                                         </h3>
                                        </div>
                                    </div>
                                        ";
// ------------- start: latest torrents ------------------//
if ($showlastxtorrents_main == "yes") {
	$result = sql_query("SELECT * FROM torrents where visible='yes' ORDER BY added DESC LIMIT 5") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($result) != 0) {
echo "<table  class='table' width=\"100%\">
        <thead>
            <tr>
                <th>" . $lang_index['col_name'] . "</th>
                <th>" . $lang_index['col_seeder'] . "</td>
                <th>" . $lang_index['col_leecher'] . "</td>
            </tr>
        </thead>
        <tbody>";

		while ($row = mysql_fetch_assoc($result)) {
			if (strlen($row['name']) > 30) {
				$title = mb_substr($row['name'], 0, 20) . "...";
			} else {
				$title = $row['name'];
			}
			echo "

<tr>
<a href=\"details.php?id=" . $row['id'] . "&amp;hit=1\">
<td><a class='sans' href=\"details.php?id=" . $row['id'] . "&amp;hit=1\">" . htmlspecialchars($title) . "</td>
</a>
<td align=\"center\" class='sans'>" . $row['seeders'] . "</td>
<td align=\"center\" class='sans'>" . $row['leechers'] . "</td>
</tr>
";
		}
		print ("</tbody></table>");
	}
}
echo "</div>";
// ------------- end: latest torrents ------------------//

echo "            <div class='portlet light bordered '>
                        <div class=\"portlet-title\">
                                        <div class=\"caption font-purple-plum\">
                                        <h3>
                                            <i class=\" glyphicon glyphicon-link font-green-sharp\"></i>
                                            <span class=\"caption-subject font-green-sharp bold uppercase\">友情链接</span>
                                         </h3>
                                        </div>
                                        <div class=\"actions\">
                                        ";
if (get_user_class() >= $newsmanage_class) {
	echo "
                                            <a class=\"btn btn-circle btn-icon-only btn-default\" href=\"linksmanage.php\">
                                                <i class=\"icon-wrench\" alt='你好'></i>
                                            </a>
                                            <span data-toggle='tooltip' title='连接管理' id='linksadmin'></span>
";
}
echo "
     <a class=\"btn btn-circle btn-icon-only btn-default\" href=\"linksmanage.php?action=apply\">
            <i class=\"glyphicon glyphicon-send\"></i>
     </a>
       </div>
           
";
echo "</div>";
// ------------- start: links ------------------//-->
print("</h3>");

$Cache->new_page('links', 86400, false);
if (!$Cache->get_page()) {
	$Cache->add_whole_row();
	$res = sql_query("SELECT * FROM links ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0) {
		$links = "";
		while ($array = mysql_fetch_array($res)) {
			$links .= "&nbsp;&nbsp;&nbsp;<a class='altlink' href=\"" . $array['url'] . "\" title=\"" . $array['title'] . "\" target=\"_blank\">" . $array['name'] . "</a>&nbsp;&nbsp;&nbsp;";
		}
		print("<table class='table table-hover' width=\"100%\"><tr><td class=\"text\" >" . trim($links) . "</td></tr></table>");
	}
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
echo "</div>";
// ------------- end: links ------------------//

// ------------- start: polls ------------------//
if ($CURUSER && $showpolls_main == "yes") {

	echo "          <div class='portlet light bordered '>          
                        <div class=\"portlet-title\">
                                        <div class=\"caption font-purple-plum\">
                                        <h3>
                                            <i class=\"glyphicon glyphicon-th font-purple-plum\"></i>
                                            <span class=\"caption-subject bold uppercase font-purple-plum\"> 投票 </span>
                                        </h3>
                                        </div>";
    if (!$arr = $Cache->get_value('current_poll_content')) {
        $res = sql_query("SELECT * FROM polls ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_array($res);
        $Cache->cache_value('current_poll_content', $arr, 7226);
    }
if (get_user_class() >= $pollmanage_class) {
	echo "
                                        <div class=\"actions\">
                                            <a class=\"btn btn-circle btn-icon-only btn-default\" href=\"makepoll.php?returnto=main\">
                                                <i class=\"icon-cloud-upload\"></i>
                                            </a>
                                            <a class=\"btn btn-circle btn-icon-only btn-default\" href=\"makepoll.php?action=edit&amp;pollid=" . $arr[id] . "&amp;returnto=main\">
                                                <i class=\"icon-wrench\"></i>
                                            </a>
                                            <a class=\"btn btn-circle btn-icon-only btn-default\" href=\"log.php?action=poll&amp;do=delete&amp;pollid=" . $arr[id] . "&amp;returnto=main\">
                                                <i class=\"icon-trash\"></i>
                                            </a>
                                            <a class=\"btn btn-circle btn-icon-only btn-default fullscreen\" href=\"polloverview.php?id=" . $arr[id] . "\" data-original-title=\"\" title=\"\"> </a>
                                        </div>
                                    ";
}
	echo "</div>";
	// Get current poll
	if (!$arr)
		$pollexists = false;
	else $pollexists = true;

	if ($pollexists) {
		$pollid = 0 + $arr["id"];
		$userid = 0 + $CURUSER["id"];
		$question = $arr["question"];
		$o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
			$arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
			$arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
			$arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

		print("<table width=\"100%\" >
                <tr style='border: 0px'>
                     <td style='border: 0px'>\n");
		print("<table width=\"100%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">");
		print("<h4 style='text-align: center'>" . $question . "</h4>\n");

		// Check if user has already voted
		$respoll = sql_query("SELECT selection FROM pollanswers WHERE pollid=" . sqlesc($pollid) . " AND userid=" . sqlesc($CURUSER["id"])) or sqlerr();
		$voted = mysql_fetch_assoc($respoll);
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
					echo "
                        $a[1] 
					    <div class='progress'>
					        <div class='progress-bar progress-bar-success' style=\"width: " . ($p) . "%;\">$p%</div>
                        </div>
					";
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
				echo $Cache->next_part();
				$i++;
			}
			echo $Cache->next_row();
		}
		else //user has not voted yet
		{
			print("<form method=\"post\" action=\"index.php\">\n");
			$i = 0;
			echo "<div class=\"mt-radio-list\" style='margin-left: 190px'>";
			while ($a = $o[$i]) {
				echo "
                                                            <label class=\"mt-radio\">
                                                                <input type=\"radio\" name=\"choice\" id=\"optionsRadios22\" value=\"" . $i . "\">" . $a . "
                                                                <span></span>
                                                            </label>
				";
				++$i;
			}
			echo "
			                                                <label class=\"mt-radio\">
                                                                <input type=\"radio\" name=\"choice\" id=\"optionsRadios22\" value=\"255\">" . $lang_index['radio_blank_vote'] . "
                                                                <span></span>
                                                            </label>
			";
			echo "</div>";
			print("<p align=\"center\"><input  type=\"submit\" class=\"btn btn-success\" value=\"" . $lang_index['submit_vote'] . "\" /></p></label>");
            echo "</form>";
		}
		print("</table>");

		if ($voted && get_user_class() >= $log_class)
			print("<p align=\"center\"><a href=\"log.php?action=poll\">" . $lang_index['text_previous_polls'] . "</a></p>\n");

		print("</table>");
	}





}
echo "</div>";
// ------------- end: polls ------------------//
echo "          <div class=\"portlet light bordered \">
                        <div class=\"portlet-title\">
                                        <div class=\"caption font-red-sunglo\">
                                            <h3><i class=\"icon-tasks\"></i>
                                            <span class=\"caption-subject font-red-sunglo bold uppercase\"> 免责声明 </span></h3>
                                        </div>
                        </div>
                    ";
// ------------- start: disclaimer  免责声明   ----------//-->
echo "
<table width=\"100%\">
    <tr>
        <td style=\"border: 0px\">
";
echo "<blockquote style='font-size: 15px'>" . $lang_index['text_disclaimer_content'].$lang_index['text_service_for'] . "</blockquote></td>
    </tr>
</table>";
echo "</div>";
// ------------- end: disclaimer ------------------//-->

echo "          <div class=\"portlet light bordered \">
                        <div class=\"portlet-title\">
                                        <div class=\"caption font - purple - plum\">
                                            <h3><i class=\"glyphicon glyphicon-list font-green-meadow\"></i>
                                            <span class=\"caption-subject font-green-meadow bold uppercase\"> 温馨提示 </span></h3>
                                        </div>
                        </div>
                    ";
echo "
<table style=\"margin-left: 5%\" width=\"90%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr ><td class=\"embedded\" >
<div align=\"center\"><br /><font class=\"medium\">";
echo $lang_index['text_browser_note'] ."</font></div>
</td></tr></table>
";
echo "</div>";
echo "</div>";  //-------------col-md-6 end------------------------//






















//---------------------col-6-md  start----------------------------------//
echo "<div class='col-md-6'>";
	echo "          <div class=\"portlet light bordered \">
                        <div class=\"portlet-title\">
                                        <div class=\"caption font - purple - plum\">
                                            <h3><i class=\"icon-comments\"></i>
                                            <span class=\"caption-subject font-red-sunglo bold uppercase\"> 群聊区 </span></h3>
                                        </div>
                        </div>
                    ";
// ------------- start: shut box box ------------------//

if ($showshoutbox_main == "yes") {
	?>


	<?php
	print("<table style='width: 100%;;'><tr><td class=\"text\" style='width: 100px'>\n");
	print("<iframe  id='shutbox' src='shoutbox.php?type=shoutbox' width='100%' height='520' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
	print("<form action='shoutbox.php' method='get' target='sbox' name='shbox'>\n");
	print("<label for='shbox_text'>" . $lang_index['text_message'] . "</label>
	<div class=\"vtop td-fat pd5\">
	<textarea class=\"input fullwidth inputor\" name='shbox_text' id='shbox_text' rows=\"2\" placeholder=\"请输入聊天内容\" style=\"height: 4em; background-color: rgb(255, 255, 255);width:98%;margin-left:1%\"></textarea>
    </div>
	<input style='margin:7px' type='submit' id='hbsubmit' class=\"btn btn-success\" name='shout' value=\"" . $lang_index['sumbit_shout'] . "\" />");
	if ($CURUSER['hidehb'] != 'yes' && $showhelpbox_main == 'yes')
//		print("<input type='submit' class='btn' name='toguest' value=\"" . $lang_index['sumbit_to_guest'] . "\" />");
	print("<input type='reset' class=\"btn btn-danger\" value=\"" . $lang_index['submit_clear'] . "\" /> <input type='hidden' name='sent' value='yes' /><input type='hidden' name='type' value='shoutbox' />");
	echo "
	<div class='dropdown pull-left' style='margin:7px'>
        <button class='btn btn-success dropdown-toggle' data-toggle='dropdown'>
        <span class='glyphicon glyphicon-heart-empty fa-lg'></span>
           表情
          </button>
           <ul class='dropdown-menu'>
                <li>";
	print(smile_row("shbox", "shbox_text"));
	echo "      </li>
            </ul>
    </div>";

	print("</form></td></tr></table>");
}
echo "</div>";
// ------------- end: shut box ------------------//
    echo "
    <div class=\"portlet light bordered \">
    <div class=\"portlet-title\">
        <div class=\"caption font - purple - plum\">
        <h3><i class=\"icon-comments\"></i>
            <span class=\"caption-subject font-red-sunglo bold uppercase\"> 站点数据 </span></h3>
        </div>
    </div>
    ";
if ($showstats_main == "yes") {
?>
        <table width="100%" class="table">
            <tr>
				<?php
				$Cache->new_page('stats_users', 3000, true);
				if (!$Cache->get_page()) {
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
			}
			?>

            </td></tr></table>
    </div>
</div>
</div>
<!--row end -->
<?php
echo "
<!-- Modal -->
<div class=\"modal fade\" id=\"uploadModel\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\">
  <div class=\"modal-dialog\" role=\"document\">
    <div class=\"modal-content\">
      <div class=\"modal-header\">
        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
        <h4 class=\"modal-title\" id=\"myModalLabel\">上传教程</h4>
      </div>
      <div class=\"modal-body\">
        <video src='./assets/video/upload.mp4' preload='auto' controls height='400px' width='500px'></video>
      </div>
      <div class=\"modal-footer\">
        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">关闭</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class=\"modal fade\" id=\"downloadModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\">
  <div class=\"modal-dialog\" role=\"document\">
    <div class=\"modal-content\">
      <div class=\"modal-header\">
        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
        <h4 class=\"modal-title\" id=\"myModalLabel\">上传教程</h4>
      </div>
      <div class=\"modal-body\">
        <video src='./assets/video/download.mp4' preload='auto' controls height='400px' width='500px'></video>
      </div>
      <div class=\"modal-footer\">
        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">关闭</button>
      </div>
    </div>
  </div>
</div>
    <div id='notice'>
                                   <div class=\"alert alert-block fade in\">
                                       <button type=\"button\" class=\"close\" ></button>
                                       <h4 class=\"alert-heading\">通知!</h4>
                                       <p> 不清楚如何下载？如何发种？请用几分钟的视频了解一下吧！ </p>
                                       <br>
                                       <p>
                                           <a data-toggle=\"modal\" data-target=\"#downloadModal\"><img src='./assets/img/download.png'/ style='height:70px'></a>
                                           <a data-toggle=\"modal\" data-target=\"#uploadModel\" ><img src='./assets/img/upload.png'/ style='height:70px'></a>
                                           <br><br>
                                           <a class=\"btn green\" data-dismiss=\"alert\"> 暂时隐藏 </a>
                                           <a class=\"btn green\" href='./assets/utorrent_2.2.1.exe'> 下载客户端 </a>
                                       </p>
                                   </div>
    </div>";
end_main_frame();
stdfoot();
?>