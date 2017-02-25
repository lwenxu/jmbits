<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
require_once(get_langfile_path("",true));
loggedinorreturn();

if (!isset($CURUSER))
	stderr($lang_subtitles['std_error'],$lang_subtitles['std_must_login_to_upload']);

stdhead($lang_subtitles['head_subtitles']);

$in_detail = $_POST['in_detail'];
$detail_torrent_id = $_POST['detail_torrent_id'];
$torrent_name = $_POST['torrent_name'];

function isInteger($n)
{
	if (preg_match("/[^0-^9]+/",$n) > 0)
	{
		return false;
	}
	return true;
}

$act = (int)$_GET["act"];
$search = trim($_GET['search']);
$letter = trim($_GET["letter"]);
if (strlen($letter) > 1)
	die;
if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
	$letter = "";

$lang_id = $_GET['lang_id'];
if (!is_valid_id($lang_id))
$lang_id = '';

$query = "";
if ($search != '')
{
	$query = "subs.title LIKE " . sqlesc("%$search%") . "";
	if ($search)
	$q = "search=" . rawurlencode($search);
}
elseif ($letter != '')
{
	$query = "subs.title LIKE ".sqlesc("$letter%");
	$q = "letter=$letter";
}

if ($lang_id)
{
	$query .= ($query ? " AND " : "")."subs.lang_id=".sqlesc($lang_id);
	$q = ($q ? $q."&amp;" : "") . "lang_id=".sqlesc($lang_id);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "upload" && ($in_detail!= 'in_detail'))
{
	//start process upload file
	$file = $_FILES['file'];

	if (!$file || $file["size"] == 0 || $file["name"] == "")
	{
	    begin_main_frame();
	    begin_frame();
		echo "</br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
		echo "
		<div class='modal show' id='no_file_tips' style='margin-top: 300px'>
		    <div class='modal-dialog'>
		        <div class='modal-content'>
		            <div class='modal-header'>
		                <div class='modal-title'> 错误 </div>
		            </div>
		            <div class='modal-body'>$lang_subtitles[std_nothing_received]</div>
		            <div class='modal-footer'><a class='btn btn-danger' href='javascript:history.go(-1);' style=\"color:white\">返回</a></div>
		        </div>
		    </div>
		</div>
		";
		end_frame();
		end_main_frame();
		die();
	}

	if ($file["size"] > $maxsubsize_main && $maxsubsize_main > 0)
	{
		begin_main_frame();
		begin_frame();
		echo "</br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
		echo "
		<div class='modal show' id='no_file_tips' style='margin-top: 300px'>
		    <div class='modal-dialog'>
		        <div class='modal-content'>
		            <div class='modal-header'>
		                <div class='modal-title'> 错误 </div>
		            </div>
		            <div class='modal-body'>$lang_subtitles[std_subs_too_big]</div>
		            <div class='modal-footer'><a class='btn btn-danger' href='javascript:history.go(-1);' style=\"color:white\">返回</a></div>
		        </div>
		    </div>
		</div>
		";
		end_frame();
		end_main_frame();
		die();
	}

	$accept_ext = array('sub' => sub, 'srt' => srt, 'zip' => zip, 'rar' => rar, 'ace' => ace, 'txt' => txt, 'SUB' => SUB, 'SRT' => SRT, 'ZIP' => ZIP, 'RAR' => RAR, 'ACE' => ACE, 'TXT' => TXT, 'ssa' => ssa, 'ass' => ass, 'cue' => cue);
	$ext_l = strrpos($file['name'], ".");
	$ext = strtolower(substr($file['name'], $ext_l+1, strlen($file['name'])-($ext_l+1)));

	if (!array_key_exists($ext, $accept_ext))
	{
		begin_main_frame();
		begin_frame();
		echo "</br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
		echo "
		<div class='modal show' id='no_file_tips' style='margin-top: 300px'>
		    <div class='modal-dialog'>
		        <div class='modal-content'>
		            <div class='modal-header'>
		                <div class='modal-title'> 错误 </div>
		            </div>
		            <div class='modal-body'>$lang_subtitles[std_wrong_subs_format]</div>
		            <div class='modal-footer'><a class='btn btn-danger' href='javascript:history.go(-1);' style=\"color:white\">返回</a></div>
		        </div>
		    </div>
		</div>
		";
		end_frame();
		end_main_frame();
		die();
	}

	/*
	if (file_exists("$SUBSPATH/$file[name]"))
	{
		echo($lang_subtitles['std_file_already_exists']);
		exit;
	}
	*/
	
	//end process upload file

	//start process torrent ID
	if(!$_POST["torrent_id"])
	{
		begin_main_frame();
		begin_frame();
		echo "</br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
		echo "
		<div class='modal show' id='no_file_tips' style='margin-top: 300px'>
		    <div class='modal-dialog'>
		        <div class='modal-content'>
		            <div class='modal-header'>
		                <div class='modal-title'> 错误 </div>
		            </div>
		            <div class='modal-body'>$lang_subtitles[std_missing_torrent_id].$file[name]</div>
		            <div class='modal-footer'><a class='btn btn-danger' href='javascript:history.go(-1);' style=\"color:white\">返回</a></div>
		        </div>
		    </div>
		</div>
		";
		end_frame();
		end_main_frame();
		die();
	}
	else
	{
		$torrent_id = $_POST["torrent_id"];
		if(!is_numeric($_POST["torrent_id"]) || !isInteger($_POST["torrent_id"]))
		{
			echo($lang_subtitles['std_invalid_torrent_id']);
			exit;
		}

		$r = sql_query("SELECT * from torrents where id = ". sqlesc($torrent_id)) or sqlerr(__FILE__, __LINE__);
		if(!mysql_num_rows($r))
		{
			begin_main_frame();
			begin_frame();
			echo "</br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
			echo "
		<div class='modal show' id='no_file_tips' style='margin-top: 300px'>
		    <div class='modal-dialog'>
		        <div class='modal-content'>
		            <div class='modal-header'>
		                <div class='modal-title'> 错误 </div>
		            </div>
		            <div class='modal-body'>$lang_subtitles[std_invalid_torrent_id]</div>
		            <div class='modal-footer'><a class='btn btn-danger' href='javascript:history.go(-1);' style=\"color:white\">返回</a></div>
		        </div>
		    </div>
		</div>
		";
			end_frame();
			end_main_frame();
			die();
		}
		else
		{
			$r_a = mysql_fetch_assoc($r);
			if($r_a["owner"] != $CURUSER["id"] && get_user_class() < $uploadsub_class)
			{
				echo($lang_subtitles['std_no_permission_uploading_others']);
				exit;
			}
		}
	}
	//end process torrent ID

	//start process title
	$title = trim($_POST["title"]);
	if ($title == "")
	{
		$title = substr($file["name"], 0, strrpos($file["name"], "."));
		if (!$title)
		$title = $file["name"];

		$file["name"] = str_replace(" ", "_", htmlspecialchars("$file[name]"));
	}

	/*
	$r = sql_query("SELECT id FROM subs WHERE title=" . sqlesc($title)) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($r) > 0)
	{
		echo($lang_subtitles['std_file_same_name_exists']."<font color=red><b>" . htmlspecialchars($title) . "</b></font> ");
		exit;
	}
	*/
	//end process title

	//start process language
	if($_POST['sel_lang'] == 0)
	{
		begin_main_frame();
		begin_frame();
		echo "</br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
		echo "
		<div class='modal show' id='no_file_tips' style='margin-top: 300px'>
		    <div class='modal-dialog'>
		        <div class='modal-content'>
		            <div class='modal-header'>
		                <div class='modal-title'> 错误 </div>
		            </div>
		            <div class='modal-body'>$lang_subtitles[std_must_choose_language]</div>
		            <div class='modal-footer'><a class='btn btn-danger' href='javascript:history.go(-1);' style=\"color:white\">返回</a></div>
		        </div>
		    </div>
		</div>
		";
		end_frame();
		end_main_frame();
		die();
	}
	else
	{
		$lang_id = $_POST['sel_lang'];
	}
	//end process language

	if ($_POST['uplver'] == 'yes' && get_user_class()>=$beanonymous_class) {
		$anonymous = "yes";
		$anon = "Anonymous";
	}
	else {
		$anonymous = "no";
		$anon = $CURUSER["username"];
	}
	
	//$file["name"] = str_replace("", "_", htmlspecialchars("$file[name]"));
	//$file["name"] = preg_replace('/[^a-z0-9_\-\.]/i', '_', $file[name]);
	
	//make_folder($SUBSPATH."/",$detail_torrent_id);
	//stderr("",$file["name"]);
	
	$r = sql_query("SELECT lang_name from language WHERE sub_lang=1 AND id = " . sqlesc($lang_id)) or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($r);

	$filename = $file["name"];
	$added = date("Y-m-d H:i:s");
	$uppedby = $CURUSER["id"];
	$size = $file["size"];

	sql_query("INSERT INTO subs (torrent_id, lang_id, title, filename, added, uppedby, anonymous, size, ext) VALUES (" . implode(",", array_map("sqlesc", array($torrent_id, $lang_id, $title, $filename, $added, $uppedby, $anonymous, $size, $ext))). ")") or sqlerr();
	
	$id = mysql_insert_id();
	
	//stderr("",make_folder($SUBSPATH."/",$torrent_id). "/" . $id . "." .$ext);
	if (!move_uploaded_file($file["tmp_name"], make_folder($SUBSPATH."/",$torrent_id). "/" . $id . "." .$ext))
		echo($lang_subtitles['std_failed_moving_file']);
	
	KPS("+",$uploadsubtitle_bonus,$uppedby); //subtitle uploader gets bonus
	
	write_log("$arr[lang_name] Subtitle $id ($title) was uploaded by $anon");
	$msg_bt = "$arr[lang_name] Subtitle $id ($title) was uploaded by $anon, Download: " . get_protocol_prefix() . "$BASEURL/downloadsubs.php/".$file["name"]."";
}

if (get_user_class() >= $delownsub_class)
{
	$delete = $_GET["delete"];
	if (is_valid_id($delete))
	{
		$r = sql_query("SELECT id,torrent_id,ext,lang_id,title,filename,uppedby,anonymous FROM subs WHERE id=".sqlesc($delete)) or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($r) == 1)
		{
			$a = mysql_fetch_assoc($r);
			if (get_user_class() >= $submanage_class || $a["uppedby"] == $CURUSER["id"])
			{
				$sure = $_GET["sure"];
				if ($sure == 1)
				{
					$reason = $_POST["reason"];
					sql_query("DELETE FROM subs WHERE id=$delete") or sqlerr(__FILE__, __LINE__);
					if (!unlink("$SUBSPATH/$a[torrent_id]/$a[id].$a[ext]"))
					{
						stdmsg($lang_subtitles['std_error'], $lang_subtitles['std_this_file']."$a[filename]".$lang_subtitles['std_is_invalid']);
						stdfoot();
						die;
					}
					else {
					KPS("-",$uploadsubtitle_bonus,$a["uppedby"]); //subtitle uploader loses bonus for deleted subtitle
					}
					if ($CURUSER['id'] != $a['uppedby']){
						$msg = $CURUSER['username'].$lang_subtitles_target[get_user_lang($a['uppedby'])]['msg_deleted_your_sub']. $a['title'].($reason != "" ? $lang_subtitles_target[get_user_lang($a['uppedby'])]['msg_reason_is'].$reason : "");
						$subject = $lang_subtitles_target[get_user_lang($a['uppedby'])]['msg_your_sub_deleted'];
						$time = date("Y-m-d H:i:s");
						sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $a[uppedby], '" . $time . "', " . sqlesc($msg) . ", ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);
					}
					$res = sql_query("SELECT lang_name from language WHERE sub_lang=1 AND id = " . sqlesc($a["lang_id"])) or sqlerr(__FILE__, __LINE__);
					$arr = mysql_fetch_assoc($res);
					write_log("$arr[lang_name] Subtitle $delete ($a[title]) was deleted by ". (($a["anonymous"] == 'yes' && $a["uppedby"] == $CURUSER["id"]) ? "Anonymous" : $CURUSER['username']). ($a["uppedby"] != $CURUSER["id"] ? ", Mod Delete":"").($reason != "" ? " (".$reason.")" : ""));
				}
				else
				{
					stdmsg($lang_subtitles['std_delete_subtitle'], $lang_subtitles['std_delete_subtitle_note']."<br /><form method=post action=subtitles.php?delete=$delete&sure=1>".$lang_subtitles['text_reason_is']."<input type=text style=\"width: 200px\" name=reason><input type=submit value=\"".$lang_subtitles['submit_confirm']."\"></form>");
					stdfoot();
					die;
				}
			}
		}
	}
}


if (get_user_class() >= UC_PEASANT)
{
	//$url = $_COOKIE["subsurl"];

	begin_main_frame();

	?>
<!--<div align=center>-->
<?php
	if (!$size = $Cache->get_value('subtitle_sum_size')){
		$res = sql_query("SELECT SUM(size) AS size FROM subs");
		$row5 = mysql_fetch_array($res);
		$size = $row5['size'];
		$Cache->cache_value('subtitle_sum_size', $size, 3600);
	}

	begin_frame("", true,10,"100%","left");
	?>
	</div>




<?php
	echo "
		<div class=\"panel panel-success\" >
		     <div class=\"panel-heading\">
		           <h2 class=\"panel-title\" style='font-size: 33px;text-align: center'>" . $lang_subtitles['text_rules'] ."</h2>
		     </div>
		     <div class=\"panel-body\" style='margin-left:220px'>
                <p align=left>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $lang_subtitles['text_rule_one'] . "</p>
                <p align=left>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $lang_subtitles['text_rule_two'] . "</p>
                <p align=left>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $lang_subtitles['text_rule_three'] . "</p>
                <p align=left>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $lang_subtitles['text_rule_four'] . "</p>
                <p align=left>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $lang_subtitles['text_rule_five'] . "</p>
                <p align=left>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $lang_subtitles['text_rule_six'] . "</p>
		     </div>
	    </div>
		";




	echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-open font-green'></i>
                                <span class='caption-subject font-green sbold uppercase'>字幕上传 <span class='small font-red   '>星号部分为必填</span></span>
                            </h2>
                        </div>
                    </div>
                    <div class='portlet-body form'>";



	print("<form class='form-horizontal form-bordered'  enctype=multipart/form-data method=post action=?>\n");
	echo "<div class='form-body'>";
	print("<input type=hidden name=action value=upload>");
	echo "<div class='form-group'>
            <label class='control-label col-md-3'>$lang_subtitles[row_file]<font color=red> *</font></label>
            <div class=\"col-md-9\">
                <div class=\"fileinput fileinput-new\" data-provides=\"fileinput\">
                    <div class=\"input-group input-large\">
                        <div class=\"form-control uneditable-input input-fixed input-medium\" data-trigger=\"fileinput\">
                            <i class=\"fa fa-file fileinput-exists\"></i>&nbsp;
                            <span class=\"fileinput-filename\"> </span>
                        </div>
                        <span class=\"input-group-addon btn default btn-file\">
                            <span class=\"fileinput-new\"> 选择文件 </span>
                            <span class=\"fileinput-exists\"> 修改文件 </span>
                            <input name=\"file\" type=\"file\"> </span>
                        <a href=\"javascript:;\" class=\"input-group-addon btn red fileinput-exists\" data-dismiss=\"fileinput\"> 删除 </a>
                    </div>
                </div>
            </div>
          </div>
";
	echo "<div class='form-group'>
            <label class='control-label col-md-3'>$lang_subtitles[row_torrent_id] <font color=red> *</font></label>
            <div class=\"col-md-9\">
                <input class='form-control inline' name='torrent_id' type='text' data-toggle='tooltip'  title='$lang_subtitles[text_torrent_id_note]' id='torrent_tooltip' value='$detail_torrent_id'>
            </div>
          </div>
";
	echo "<div class='form-group'>
            <label class='control-label col-md-3'>$lang_subtitles[row_title] </label>
            <div class=\"col-md-9\">
                <input class='form-control ' name='title' type='text' data-toggle='tooltip'  title='$lang_subtitles[text_title_note]' id='torrent_title_tooltip' value='$torrent_name'>
            </div>
          </div>
";
	echo "<div class='form-group' >
            <label class='control-label col-md-3'>$lang_subtitles[row_language] <font color=red>*</font> </label>
            <div class=\"col-md-9\" >";
	            $s = "<select name=\"sel_lang\" class='form-control input-large'>
                    <option value=\"0\">" . $lang_subtitles['select_choose_one'] . "</option>";
            $langs = langlist("sub_lang");

            foreach ($langs as $row) {
                $s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
            }
            $s .= "</select>";

            print($s);
            echo "</div>
                  </div>
        ";

	if (get_user_class() >= $beanonymous_class) {
		echo "<div class='form-group'>
            <label class='control-label col-md-3'>$lang_subtitles[row_show_uploader] </label>
            <div class=\"col-md-9\">
                <label class=\"mt-checkbox\">
                    <input type=checkbox name=uplver value=yes> $lang_subtitles[hide_uploader_note]
                    <span></span>
                </label>
            </div>
          </div>
";
//		tr($lang_subtitles['row_show_uploader'], "<input type=checkbox name=uplver value=yes>" . $lang_subtitles['hide_uploader_note'], 1);
	}

	print("<div class=toolbox colspan=2 align=center><input type=submit class='btn btn-success' value=" . $lang_subtitles['submit_upload_file'] . "> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type=reset class='btn btn-danger' value=\"" . $lang_subtitles['submit_reset'] . "\"></div>");
	echo "</div>";
	print("</form>");



                    echo "</div>
                </div>
            </div>
          </div>
            ";



	end_frame();

	end_main_frame();
}

if(get_user_class() >= UC_PEASANT)
{
	echo "<div class='row'>
            <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
                <div class='portlet light form-fit bordered'>
                    <div class='portlet-title'>
                        <div class='caption'>
                            <h2>
                                <i class='glyphicon glyphicon-search font-yellow-soft'></i>
                                <span class='caption-subject font-yellow-soft sbold uppercase'>搜索 </span>
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
                            <input class='form-control' type=text  name=search>
                        </div>
                    </div>
         </div>
";
		echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>语言 </label>
                    <div class=col-md-9>
                        <div class='input-group'>";
                            $s = "<select class='form-control input-large' name=\"lang_id\"><option value=\"0\">" . $lang_subtitles['select_all_languages'] . "</option>\n";
                            $langs = langlist("sub_lang");
                            foreach ($langs as $row) {
                                $s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
                            }
                            $s .= "</select>";
                            print($s);
 echo "                 </div>
                    </div>
         </div>
";

	echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>检索 </label>
                    <div class=col-md-9>
                        <div class='input-group'>
                        <input type=submit class='btn btn-success' value=\"" . $lang_subtitles['submit_search'] . "\">
                        </div>
                    </div>
         </div>
";

	echo "
        <div class='form-group' >
                    <label class='control-label col-md-3'>快速索引 </label>
                    <div class=col-md-9>
                        <div class='input-group'>
                            <ul class='pagination'>
                        ";
                            for ($i = 97; $i < 123; ++$i) {
                                $l = chr($i);
                                $L = chr($i - 32);
                                if ($l == $letter)
                                    print("<li class='disabled'><a href=?letter=$l>$L</a></li>");
                                else
                                    print("<li><a href=?letter=$l>$L</a></li>");
                            }
 echo "                    </ul>       
                       </div>
                    </div>
         </div>
";
		print("</form>");



		$perpage = 30;
		$query = ($query ? " WHERE ".$query : "");
		$res = sql_query("SELECT COUNT(*) FROM subs $query") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res);
		$num = $arr[0];
		if (!$num)
		{
			info_msg($lang_subtitles['text_sorry'],$lang_subtitles['text_nothing_here']);
			stdfoot();
			die;
		}
		list($pagertop, $pagerbottom, $limit) = pager($perpage, $num, "subtitles.php?".$q."&");

		print($pagertop);

		$i = 0;
		$res = sql_query("SELECT subs.*, language.flagpic, language.lang_name FROM subs LEFT JOIN language ON subs.lang_id=language.id $query ORDER BY id DESC $limit") or sqlerr();
        echo "
        <div class=\"portlet box green\">
                                    <div class=\"portlet-title\">
                                        <div class=\"caption\">
                                            <i class=\"fa fa-comments\"></i>字幕列表 </div>
                                        <div class=\"tools\">
                                            <a href=\"javascript:;\" class=\"collapse\" data-original-title=\"\" title=\"\"> </a>
                                            <a href=\"#portlet-config\" data-toggle=\"modal\" class=\"config\" data-original-title=\"\" title=\"\"> </a>
                                            <a href=\"javascript:;\" class=\"reload\" data-original-title=\"\" title=\"\"> </a>
                                            <a href=\"javascript:;\" class=\"remove\" data-original-title=\"\" title=\"\"> </a>
                                        </div>
                                    </div>
                                    <div class=\"portlet-body\">
                                        <div class=\"table-scrollable\">
                                            <table class=\"table table-striped table-hover\">
                                                <thead>
                                                    <tr>
                                                        <th> $lang_subtitles[col_lang] </th>
                                                        <th> $lang_subtitles[col_title] </th>
                                                        <th> $lang_subtitles[title_date_added] </th>
                                                        <th> $lang_subtitles[title_size] </th>
                                                        <th> $lang_subtitles[col_hits] </th>
                                                        <th> $lang_subtitles[col_upped_by] </th>
                                                        <th> $lang_subtitles[col_report] </th>
                                                    </tr>
                                                </thead>
                                                <tbody>";
	while ($arr = mysql_fetch_assoc($res)) {
		// the number $start_subid is just for legacy support of prevoiusly uploaded subs, if the site is completely new, it should be 0 or just remove it
		$lang = "<td class=rowfollow align=left valign=middle>" . "<img border=\"0\" src=\"pic/flag/" . $arr["flagpic"] . "\" alt=\"" . $arr["lang_name"] . "\" title=\"" . $arr["lang_name"] . "\"/>" . "</td>\n";
		$title = "<td class=rowfollow align=left><a href=\"" . ($arr['id'] <= $start_subid ? "downloadsubs_legacy.php/" . $arr['filename'] : "downloadsubs.php?torrentid=" . $arr['torrent_id'] . "&subid=" . $arr['id']) . "\"<b>" . htmlspecialchars($arr["title"]) . "</b></a>" .
			($mod || ($pu && $arr["uppedby"] == $CURUSER["id"]) ? " <font class=small><a href=?delete=$arr[id]>" . $lang_subtitles['text_delete'] . "</a></font>" : "") . "</td>\n";
		$addtime = gettime($arr["added"], false, false);
		$added = "<td class=rowfollow align=left><nobr>" . $addtime . "</nobr></td>\n";
		$size = "<td class=rowfollow align=left>" . mksize_loose($arr['size']) . "</td>\n";
		$hits = "<td class=rowfollow align=left>" . number_format($arr['hits']) . "</td>\n";
		$uppedby = "<td class=rowfollow align=left>" . ($arr["anonymous"] == 'yes' ? $lang_subtitles['text_anonymous'] . (get_user_class() >= $viewanonymous_class ? "<br />" . get_username($arr['uppedby'], false, true, true, false, true) : "") : get_username($arr['uppedby'])) . "</td>\n";
		$report = "<td class=rowfollow align=left><a href=\"report.php?subtitle=$arr[id]\"><span class=\"label label-md label-danger\"> 举报 </span></a></td>\n";
		print("<tr>" . $lang . $title . $added . $size . $hits . $uppedby . $report . "</tr>\n");
		$i++;
	}
 echo "                                         </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
        ";
		print("<table width=940 border=1 cellspacing=0 cellpadding=5>\n");


		$mod = get_user_class() >= $submanage_class;
		$pu = get_user_class() >= $delownsub_class;



		print("</table>\n");
		print($pagerbottom);
}
stdfoot();
?>
