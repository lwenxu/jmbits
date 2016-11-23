<?php
require "include/bittorrent.php";
dbconn(true);
require_once(get_langfile_path());
loggedinorreturn (true);
parked ();
$torrentsperpage = 25;
$action = isset ( $_POST ['action'] ) ? htmlspecialchars ( $_POST ['action'] ) : (isset ( $_GET ['action'] ) ? htmlspecialchars ( $_GET ['action'] ) : '');
$allowed_actions = array (
		"list",
		"new",
		"view",
		"edit",
		"takeedit",
		"takeadded",
		"res",
		"takeres",
		"addamount",
		"delete",
		"delres",
		"fastdel",
		"confirm",
		"cancel",
		"reseed",
		"keepseed",
		"needtran" 
);
if (! $action)
	$action = 'list';
if (! in_array ( $action, $allowed_actions ))
	stderr ( $lang_req ['std_error'], $lang_req ['std_invalid_action'] );
else {
	function reqmenu($selected = "viewrequest") {
		global $title;
		stdhead ( $title );
		begin_main_frame ();
		end_main_frame ();
	}
	
	switch ($action) { // 显示菜单
		case "reseed" :
			reqmenu ( "reseed" );
			break;
		case "keepseed" :
			reqmenu ( "keepseed" );
			break;
		case "needtran" :
			reqmenu ( "needtran" );
			break;
		default :
	}
	
	switch ($action) {
		case "list" :
			{
				global $title;
				$title = $lang_req ['head_req'];
				reqmenu ();
				
				$count_get = 0;
				$count_order = 0;
				$oldlink = "";
				foreach ( $_GET as $get_name => $get_value ) {
					$get_name = mysql_real_escape_string ( strip_tags ( str_replace ( array (
							"\"",
							"'" 
					), array (
							"",
							"" 
					), $get_name ) ) );
					$get_value = mysql_real_escape_string ( strip_tags ( str_replace ( array (
							"\"",
							"'" 
					), array (
							"",
							"" 
					), $get_value ) ) );
					
					if ($get_name != "page") {
						if ($count_get > 0) {
							$oldlink .= "&" . $get_name . "=" . $get_value;
						} else {
							$oldlink .= $get_name . "=" . $get_value;
						}
						$count_get ++;
					}
					if ($get_name != "sort" && $get_name != "type" && $get_name != "page") {
						if ($count_get > 0) {
							$orderlink .= "&" . $get_name . "=" . $get_value;
						} else {
							$orderlink .= $get_name . "=" . $get_value;
						}
						$count_order ++;
					}
				
				}
				if ($count_get > 0) {
					$oldlink = $oldlink . "&";
				}
				if ($count_order > 0) {
					$orderlink = $orderlink . "&";
				}
				
				$finished = isset ( $_GET ['finished'] ) ? $_GET ['finished'] : '';
				$allowed_finished = array (
						"yes",
						"no",
						"cancel",
						"all" 
				);
				if (! in_array ( $finished, $allowed_finished ))
					$limit = "finish = 'no'";
				else
					$limit = ($finished == "all" ? '1' : "finish ='" . $finished . "'");
				$rows = sql_query ( "SELECT count(*) FROM req WHERE " . $limit ) or sqlerr ( __FILE__, __LINE__ );
				$rownumber = mysql_fetch_array ( $rows );
				if ($rownumber [0] == 0)
					stderr ( "没有求种", "没有符合条件的求种项目，<a href=viewrequest.php?action=new>点击这里增加新求种</a>", 0, 0, 0, 0 );
				else {
					$addparam = $oldlink;
					list ( $pagertop, $pagerbottom, $page ) = pager ( $torrentsperpage, $rownumber [0], "?" . $addparam );
					
					switch ($_GET ['sort']) {
						case '1' :
							$column = "catid";
							break;
						case '2' :
							$column = "name";
							break;
						case '3' :
							$column = "amount";
							break;
						case '4' :
							$column = "ori_amount";
							break;
						case '5' :
							$column = "userid";
							break;
						case '6' :
							$column = "comments";
							break;
						case '7' :
							$column = "added";
							break;
						case '8' :
							$column = "finish";
							break;
						case '9' :
							$column = "resetdate";
							break;
						default :
							$column = "resetdate";
							break;
					}
					switch ($_GET ['type']) {
						case 'asc' :
							$ascdesc = "ASC,";
							$linkascdesc = "asc";
							break;
						case 'desc' :
							$ascdesc = "DESC,";
							$linkascdesc = "desc";
							break;
						default :
							$ascdesc = "DESC,";
							$linkascdesc = "desc";
							break;
					}
					
					$order = $column . " " . $ascdesc;
					if ($order != "resetdate DESC,")
						$order .= " resetdate DESC";
					else
						$order .= " amount DESC";
					
					$rows = sql_query ( "SELECT * FROM req WHERE " . $limit . "  ORDER BY $order $page" ) or sqlerr ( __FILE__, __LINE__ );
					print ("<h2 align=center><a href=viewrequest.php?action=new><input class='btn btn-success' type=\"button\" value=\"" . $lang_req ['head_req'] . "\" onclick=\"window.location='viewrequest.php?action=new';\" style=\"font-weight: bold\"/></a> <a href=forums.php?action=viewtopic&topicid=4818&page=0><input class='btn btn-danger' type=\"button\" value=\"规则\" onclick=\"window.location='forums.php?action=viewtopic&topicid=4818';\"/></a></h2>") ;
//					print ($pagertop) ;
					print ("<table class='table table-striped' width=100% border=1 cellspacing=0 cellpadding=5 style=border-collapse:collapse >\n") ;
					if (get_user_class () >= 13) {
						print ("<form action=\"viewrequest.php\" method=\"GET\">\n") ;
						print ("<input type=\"hidden\" name=\"action\" value=\"fastdel\" />") ;
					}
					print ("<table class=\"table table-striped\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">\n") ;
					print ("<tr>" . "<td class=colhead width=3% align=center><a href=?" . $orderlink . "sort=1&type=" . ($column == "catid" && $ascdesc == "ASC," ? "desc" : "asc") . ">类型</a></td>" . "<td class=colhead width=35% align=left><a href=?" . $orderlink . "sort=2&type=" . ($column == "name" && $ascdesc == "ASC," ? "desc" : "asc") . ">" . $lang_req ['name'] . "</a></td>" . "<td class=colhead width=7% align=center><a href=?" . $orderlink . "sort=4&type=" . ($column == "ori_amount" && $ascdesc == "DESC," ? "asc" : "desc") . ">" . $lang_req ['ori_amount'] . "</a></td>" . "<td class=colhead width=7% align=center><a href=?" . $orderlink . "sort=3&type=" . ($column == "amount" && $ascdesc == "DESC," ? "asc" : "desc") . ">" . $lang_req ['new_amount'] . "</a></td>" . "<td class=colhead width=7% align=center><a href=?" . $orderlink . "sort=5&type=" . ($column == "userid" && $ascdesc == "ASC," ? "desc" : "asc") . ">" . $lang_req ['addedby'] . "</a></td>" . "<td class=colhead width=4% align=center><a href=?" . $orderlink . "sort=6&type=" . ($column == "comments" && $ascdesc == "DESC," ? "asc" : "desc") . ">评论</a></td>" . "<td class=colhead width=4% align=center ><font color=black>应求</font></a></td>" . "<td class=colhead width=9% align=center><a href=?" . $orderlink . "sort=7&type=" . ($column == "added" && $ascdesc == "DESC," ? "asc" : "desc") . ">" . $lang_req ['addedtime'] . "</a></td>" . "<td class=colhead width=8% align=center><a href=?" . $orderlink . "sort=9&type=" . ($column == "resetdate" && $ascdesc == "DESC," ? "asc" : "desc") . ">最后悬赏</a></td>" . "<td class=colhead width=8% align=center><a href=?" . $orderlink . "sort=8&type=" . ($column == "finish" && $ascdesc == "DESC," ? "asc" : "desc") . ">" . $lang_req ['state'] . "</a></td>\n" . (get_user_class () >= 13 ? "<td width=13% class=colhead align=center><font color=black>行为</font></td>\n" : "") . "</tr>") ;
					
					// $cat = mysql_fetch_array($cat);
					// $cat=array(
					// 401=>"电影",402=>"剧集",403=>"综艺",404=>"资料",405=>"动漫",406=>"音乐",407=>"体育",408=>"软件",409=>"游戏",410=>"其他",411=>"纪录片",4013=>"试种");
					while ( $row = mysql_fetch_array ( $rows ) ) {
						$cat = mysql_fetch_array ( sql_query ( "SELECT name FROM categories where id={$row["catid"]}" ) );
						// if
						$count = mysql_fetch_assoc ( sql_query ( "SELECT count(*) FROM resreq WHERE reqid=" . $row ["id"] ) );
						$man = (get_user_class () >= 13) ? "<input type=\"checkbox\" name=\"id[]\" value=\"{$row['id']}\"/> " : '';
						print ("<tr>" . "<td  align=center>" . $cat ["name"] . "</td>" . "<td align=left>{$man}<a href=viewrequest.php?action=view&id=" . $row ["id"] . "><span>" . $row ["name"] . "</span></a></td>" . "<td align=center>" . $row ['ori_amount'] . "</td>" . "<td align=center><font color=#ff0000><span>" . ($row ['amount'] - $row ['ori_amount']) . "</span></font></td>" . "<td align=center>" . get_username ( $row ['userid'] ) . "</td>" . "<td align=center>" . $row ['comments'] . "</td>" . "<td align=center>" . $count ["count(*)"] . "</td>" . "<td align=center>" . gettime ( $row ['added'], true, false ) . "</td>" . "<td align=center>" . gettime ( $row ['resetdate'], true, false ) . "</td>" . "<td align=center>" . ($row ['finish'] == "yes" ? $lang_req ['finished'] : ($row ['finish'] == "cancel" ? "已撤消" : ($row ['userid'] == $CURUSER [id] ? $lang_req ['unfinished'] : "<a href=viewrequest.php?action=res&id=" . $row ["id"] . " >" . $lang_req ['unfinished'] . "</a>"))) . "</td>" . (get_user_class () >= 13 ? "<td align=center><font color=black><a href=viewrequest.php?action=fastdel&id=" . $row ["id"] . " >删</a> <a href=viewrequest.php?action=edit&id=" . $row ["id"] . " >改</a> <a href=viewrequest.php?action=cancel&id=" . $row ["id"] . " >撤</a></td>\n" : "") . 

						"</tr>") ;
					}
					
					if (get_user_class () >= 13) {
						print ("<tr>") ;
						print ("<td class=\"rowfollow\" colspan=\"11\"><a class='btn btn-success' href=\"#\" onclick=\"set_checked_torrent(true); return false;\">全选</a> <a class='btn btn-success' href=\"#\" onclick=\"set_checked_torrent(false); return false;\">全不选</a>, 选中项: <input class='btn btn-danger' type=\"submit\" name=\"job\" value=\"删除\"> <input class='btn btn-success' type=\"submit\" name=\"job\" value=\"撤销\"></td>\n") ;
						print ("</tr>\n") ;
						print ("</form>") ;
						print ("<script type=\"text/javascript\">function set_checked_torrent(val){var checkboxs=document.getElementsByName(\"id[]\"); for (var i=0; i<checkboxs.length; i++) checkboxs[i].checked=val; }</script>") ;
					}
					print ("</table>\n") ;
					print ($pagerbottom) ;
				}
				print ("<spanr><a href=viewrequest.php?finished=all>查看所有</a> <a href=viewrequest.php?finished=yes>查看已解决</a> <a href=viewrequest.php?finished=no>查看未解决</a> <a href=viewrequest.php?finished=cancel>查看已关闭</a>\n") ;
				stdfoot ();
				die ();
				break;
			}
		
		case "view" :
			{
				if (is_numeric ( $_GET ["id"] )) {
					$id = $_GET ["id"];
					$res = sql_query ( "SELECT * FROM req WHERE id ='" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
					if (mysql_num_rows ( $res ) == 0)
						stderr ( $lang_req ['std_error'], $lang_req ['id_not_exist'] );
					else
						$arr = mysql_fetch_assoc ( $res );
					stdhead ( "求种详情" );
					print ("<h1 align=center id=top>" . ($arr [finish] == "no" ? "求种中" : ($arr [finish] == "yes" ? "已解决" : "已撤销")) . "-" . htmlspecialchars ( $arr ["name"] ) . "</h1>\n") ;
					print ("<table class='table table-striped' width=100% cellspacing=0 cellpadding=5>\n") ;
					tr ( "基本信息", $lang_req ['text_reqed_by'] . get_username ( $arr ['userid'] ) . $lang_req ['text_reqed_at'] . gettime ( $arr ["added"], true, false ) . "\n", 1 );
					tr ( "悬赏", "累计悬赏" . $arr ['amount'] . $lang_req ['ori_amount_is_1'] . $arr ["ori_amount"] . $lang_req ['ori_amount_is_2'] . "\n", 1 );
					tr ( $lang_req ['do'], (($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) && $arr ["finish"] == "no" ? "<a class='btn btn-success' href=viewrequest.php?action=edit&id=" . $id . " >" . $lang_req ['tr_edit'] : "") . "\n" . 					// 编辑权限（带分隔线）
					($arr ['userid'] == $CURUSER [id] || $arr ["finish"] != "no" ? "" : "<a  href=viewrequest.php?action=res&id=" . $id . " ><span class='btn btn-success'>" . $lang_req ['tr_res'] . "</span>\n") . 					// 应求权限
					(get_user_class () >= 13 && $arr ['userid'] != $CURUSER [id] && $arr ["finish"] == "no" ? "  " : "") . 					// 应求分隔线
					(get_user_class () >= 13 ? "<a class='btn btn-danger' href=viewrequest.php?action=delete&id=" . $id . " >" . $lang_req ['tr_delete'] . 					// 删除权限
					($arr ["finish"] == "no" ? "  " : "") : "") . "\n" . 					// 删除分隔线
					(($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) && $arr ["finish"] == "no" ? "<a class='btn btn-info' href=viewrequest.php?action=cancel&id=" . $id . " >" . "<span>撤销求种</span></a>" : "") . "\n" . 					// 撤销权限
					($arr ['userid'] == $CURUSER [id] ? "" : ("  <a href=report.php?reportrequestid=" . $id . " >" . "<span>举报求种</span></a>") . "\n"), 					// 举报
					1 );
					
					{ // 检查是否有非求种人追加悬赏
						$amountadds = sql_query ( "SELECT * FROM givebonus WHERE bonustotorrentid ='" . $id . "' AND type='3'" );
						if (mysql_num_rows ( $amountadds ) > 0) {
							$amountadder = "<spanr/>";
							while ( $amountadd = mysql_fetch_array ( $amountadds ) )
								$amountadder .= get_username ( $amountadd ['bonusfromuserid'] ) . "(" . $amountadd ['bonus'] . ")&nbsp;&nbsp;&nbsp;";
						}
					}
					
					if ($arr ["finish"] == "no")
						tr ( "追加悬赏", "<form action=viewrequest.php method=post> <input type=hidden name=action value=addamount><input type=hidden name=reqid value=" . $arr ["id"] . "><input class='input tip-focus' size=6 name=amount value=1000 >&nbsp;&nbsp;<input  class='btn btn-success' type=submit value=提交 > 追加悬赏每次将扣减25个魔力值 作为手续费" . $amountadder . "</form>", 1 );
					tr ( "介绍", format_comment ( unesc ( $arr ["introduce"] ) ), 1 );
					$limit = ($arr ['finish'] == "no" ? "" : " AND chosen = 'yes' ");
					$res = sql_query ( "SELECT * FROM resreq WHERE reqid ='" . $_GET ["id"] . "'" . $limit ) or sqlerr ( __FILE__, __LINE__ );
					$ress = "";
					if (mysql_num_rows ( $res ) == 0)
						$ress = "还没有应求";
					else {
						$ress = "";
						if (($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) && $arr ['finish'] == "no")
							$ress .= "<form action=viewrequest.php method=post>\n<input type=hidden name=action value=confirm > <input type=hidden name=id value=" . $id . " >\n";
						while ( $row = mysql_fetch_array ( $res ) ) {
							$each = mysql_fetch_assoc ( sql_query ( "SELECT * FROM torrents WHERE id = '" . $row ["torrentid"] . "'" ) );
							$ress .= (($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) && $arr ['finish'] == "no" ? "<input type=checkbox name=torrentid[] value=" . $each ["id"] . ">" : "");
							if (mysql_num_rows ( sql_query ( "SELECT * FROM torrents WHERE id = '" . $row ["torrentid"] . "'" ) ) == 1)
								$ress .= "<a href=details.php?id=" . $each ["id"] . "&hit=1 >" . $each ["name"] . "</a> by " . (($each ["anonymous"] == "yes" && get_user_class () < 12 && $each ["owner"] != $CURUSER ["id"]) ? "<i>匿名</i>" : get_username ( $each [owner] ));
							else
								$ress .= "<i>种子已被删除</i>";
							$ress .= (get_user_class () >= 13 ? " [<a href=viewrequest.php?action=delres&id=" . $row ["id"] . " >X</a>]" : "") . "<spanr/>\n";
						}
						if (($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) && $arr ['finish'] == "no")
							$ress .= "<input type=submit value=确认选中应求></form>\n";
					}
					tr ( "应求", $ress . "&nbsp;&nbsp;&nbsp;<a class='btn btn-success' href=viewrequest.php?action=res&id=" . $id . " ><span>" . $lang_req ['tr_res'] . "</span>\n", 1 );
					print ("</table><spanr/><spanr/>\n") ;
					
					$commentbar = "<p align=\"center\"><a class=\"index\" href=\"comment.php?action=add&amp;pid=" . $id . "&amp;type=request\">添加留言</a></p>\n";
					$subres = sql_query ( "SELECT COUNT(*) FROM comments WHERE request = $id" );
					$subrow = mysql_fetch_array ( $subres );
					$count = $subrow [0];

//					删除评论
//					if (! $count) {
//						print ("<h1 id=\"startcomments\" align=\"center\">没有留言</h1>\n") ;
//					} else {
//						list ( $pagertop, $pagerbottom, $limit ) = pager ( 10, $count, "viewrequest.php?action=view&id=$id&", array (
//								lastpagedefault => 1
//						) );
//						$subres = sql_query ( "SELECT id, text, user, added, editedby, editdate FROM comments  WHERE request = " . sqlesc ( $id ) . " ORDER BY id $limit" ) or sqlerr ( __FILE__, __LINE__ );
//						$allrows = array ();
//						while ( $subrow = mysql_fetch_array ( $subres ) )
//							$allrows [] = $subrow;
//						print ($pagertop) ;
//						commenttable ( $allrows, "request", $id );
//						print ($pagerbottom) ;
//					}
					print ('<script type="text/javascript">
function quick_reply_to(username)
{
	parent.document.getElementById("quickreplytext").focus();
    parent.document.getElementById("quickreplytext").value = "@" + username + " "+parent.document.getElementById("quickreplytext").value;
}
</script>') ;
//					print ("<a name='quickreply' id='quickreply'> </a><table style='border:1px solid #000000;'><tr>" . "<td class=\"text\" align=\"center\"><span>快速留言</span><spanr /><spanr />" . "<form id=\"compose\" name=\"comment\" method=\"post\" action=\"comment.php?action=add&amp;type=request\" onsubmit=\"return postvalid(this);\">" . "<input type=\"hidden\" name=\"pid\" value=\"" . $id . "\" /><spanr />") ;
//					quickreply ( 'comment', 'body', '我要留言' );
//					print ("</form></td></tr></table>") ;
//					print ($commentbar) ;
					stdfoot ();
				
				} else
					stderr ( $lang_req ['std_error'], $lang_req ['id_not_exist'] );
				break;
			}
		
		case "edit" :
			{
				if (! is_numeric ( $_GET ["id"] ))
					stderr ( "出错了！！！", "求种ID必须为数字" );
				$res = sql_query ( "SELECT * FROM req WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！" );
				$arr = mysql_fetch_assoc ( $res );
				if ($arr ["finish"] == "yes")
					stderr ( "出错了！", "该求种已完成！" );
				if ($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) {
					stdhead ( "编辑求种" );
					print ("<form id=edit method=post name=edit action=viewrequest.php >\n
		<input type=hidden name=action  value=takeedit >
		<input type=hidden name=reqid  value=" . $_GET ["id"] . " >
		") ;
					$cat = array (
							401 => "电影",
							402 => "剧集",
							403 => "综艺",
							404 => "资料",
							405 => "动漫",
							406 => "音乐",
							407 => "体育",
							408 => "软件",
							409 => "游戏",
							410 => "其他",
							411 => "纪录片" 
					);
					$select = "<select  name = catid>";
					foreach ( $cat as $name => $value )
						$select .= "<option value=\"" . $name . "\" " . ($arr ["catid"] == $name ? "selected " : "") . ">" . $value . "</option>";
					$select .= "</select>";
					
					print ("<table class='table table-striped' width=100% cellspacing=0 cellpadding=3><tr><td class=colhead align=center colspan=2>编辑求种</td></tr>") ;
					tr ( "类型：", $select, 401 );
					tr ( "标题：", "<input name=name value=\"" . $arr ["name"] . "\" size=134 ><spanr/>", 1 );
					print ("<tr><td class=rowhead align=right valign=top><span>介绍：</span></td><td class=rowfollow align=left>") ;
					textbbcode ( "edit", "introduce", $arr ["introduce"] );
					print ("</td></tr>") ;
					print ("</td></tr><tr><td class=toolbox align=center colspan=2><input id=qr type=submit class=btn value=编辑求种 ></td></tr></table></form><spanr />\n") ;
					stdfoot ();
					die ();
				} else
					stderr ( "出错了！！！", "你没有该权限！！！<a href=viewrequest.php?action=view&id=" . $_GET ["id"] . ">点击这里返回</a>", 0 );
			}
		
		case "new" : // 新增求种
			{
				if (get_user_class () >= 2) {
					stdhead ( "新增求种" );
					$cat = sql_query ( "SELECT id,name FROM categories" );
					// $cat=array(
					// 401=>"电影",402=>"剧集",403=>"综艺",404=>"资料",405=>"动漫",406=>"音乐",407=>"体育",408=>"软件",409=>"游戏",410=>"其他",411=>"纪录片");
					$select = "<select class='btn btn-success' name = catid>";
					$select .= "<option value=\"\">- 请选择 -</option>";
					while ( $rows = mysql_fetch_array ( $cat ) ) {
						$select .= "<option value=\"" . $rows ["id"] . "\">" . $rows ["name"] . "</option>";
					}
					$select .= "</select>";
					print ("
<h2 align=center>
<a href=viewrequest.php?action=new>
<input class='btn btn-success' type=\"button\" value=\"" . $lang_req ['head_req'] . "\" onclick=\"window.location='viewrequest.php?action=new';\" style=\"font-weight: bold\"/>
</a>
 <a href=forums.php?action=viewtopic&topicid=4818>
 <input class='btn btn-danger' type=\"button\" value=\"规则\" onclick=\"window.location='forums.php?action=viewtopic&topicid=4818';\"/>
 </a>
</h2>") ;
					print ("<form id=edit method=post name=edit action='viewrequest.php' >
							<input type=hidden name=action  value=takeadded >\n") ;
					print ("<table class='table table-striped' width=100% cellspacing=0 cellpadding=3>
<tr>
<td class=colhead align=center colspan=2>新增求种</td></tr>\n") ;
					tr ( "类型：", $select, 1 );
					tr ( "标题：", "<input class='input tip-focus fullwidth' name=name size=134><spanr/>", 1 );
					tr ( "悬赏：", "<input class='input tip-focus' name=amount size=11 value=2000>赏金不得低于1000魔力值，每次求种将扣去100魔力值作为手续费。<spanr/>", 1 );
//					tr("介绍：");
					print ("<tr>
					<td class=embedded-head >介绍：</td><td class=rowfollow align=left>") ;
					textbbcode ( "edit", "introduce", $arr ["introduce"] );
					print ("</td></tr>") ;
					print ("<tr>
						<td class=toolbox style=vertical-align: middle; padding-top: 10px; padding-bottom: 10px; align=center colspan=2>
						<input style='margin-left: 9%' id=qr type=submit value=新增求种 class='btn btn-success ' />
						</td></tr></table></form><spanr />\n") ;
					print ("<script language=\"javascript\">\n" . "alert(\"新增求种前请先阅读规则，\\n\\n不符合要求的求种可能会被直接删除，赏金不予返还！\");\n" . "</script>\n") ;
					stdfoot ();
					die ();
				} else
					stderr ( "出错了！！！", "你没有该权限！！！<a href=viewrequest.php>点击这里返回</a>", 0 );
			}
		
		case "takeadded" :
			{
				
				if (! $_POST ["introduce"])
					stderr ( "出错了！", "介绍未填！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (! $_POST ["name"])
					stderr ( "出错了！", "名称未填！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (! $_POST ["catid"])
					stderr ( "出错了！", "类型未选！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (! $_POST ["amount"])
					stderr ( "出错了！", "赏金未填！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (! is_numeric ( $_POST ["amount"] ))
					stderr ( "出错了！！！", "赏金必须为数字！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				$amount = $_POST ["amount"];
				if ($amount < 1000)
					stderr ( "出错了！", "发布求种赏金不得小于1000个魔力值！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if ($amount > 10000)
					stderr ( "出错了！", "发布求种赏金不得大于10000个魔力值！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				$amount += 100;
				if ($amount + 100 > $CURUSER [seedbonus])
					stderr ( "出错了！", "你没有那么多魔力值！！！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (get_user_class () >= 2) {
					sql_query ( "UPDATE users SET seedbonus = seedbonus - " . $amount . " WHERE id = " . $CURUSER [id] );
//					writeBonusComment($CURUSER [id],"使用$amount 魔力值新增了求种 " . mysql_insert_id (). sqlesc($_POST["name"]));
					sql_query ( "INSERT req ( name , catid , introduce, ori_introduce ,amount , ori_amount , userid ,added, resetdate ) VALUES ( " . sqlesc ( $_POST ["name"] ) . " , " . sqlesc ( $_POST ["catid"] ) . " , " . sqlesc ( $_POST ["introduce"] ) . " , " . sqlesc ( $_POST ["introduce"] ) . " , " . sqlesc ( $_POST ["amount"] ) . " , " . sqlesc ( $_POST ["amount"] ) . " , " . sqlesc ( $CURUSER [id] ) . " , '" . date ( "Y-m-d H:i:s" ) . "', '" . date ( "Y-m-d H:i:s" ) . "' )" ) or sqlerr ( __FILE__, __LINE__ );
					
					write_log ( "求种：用户 $CURUSER[username] 新增了求种 " . mysql_insert_id (). sqlesc ( $_POST ["name"] ) );
					stderr ( "成功", "新增求种成功，<a href=viewrequest.php>点击这里返回</a>", 0 );
				} else
					stderr ( "出错了！！！", "你没有该权限！！！<a href=viewrequest.php>点击这里返回</a>", 0 );
				die ();
				break;
			}
		
		case "takeedit" :
			{
				if (! is_numeric ( $_POST ["reqid"] ))
					stderr ( "出错了！！！", "求种ID必须为数字！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				$res = sql_query ( "SELECT * FROM req WHERE id =" . sqlesc ( $_POST ["reqid"] ) . "" ) or sqlerr ( __FILE__, __LINE__ );
				if (! $_POST ["introduce"])
					stderr ( "出错了！！！", "介绍未填！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (! $_POST ["name"])
					stderr ( "出错了！！！", "名称未填！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (! $_POST ["catid"])
					stderr ( "出错了！！！", "类型未选！<a href=javascript:history.go(-1)>点击这里返回</a>", 0 );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！<a href=viewrequest.php>点击这里返回</a>", 0 );
				$arr = mysql_fetch_assoc ( $res );
				if ($arr ["finish"] == "yes")
					stderr ( "出错了！", "该求种已完成！<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				if ($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) {
					sql_query ( "UPDATE req SET introduce = " . sqlesc ( $_POST ["introduce"] ) . " , name = " . sqlesc ( $_POST ["name"] ) . ", catid = " . sqlesc ( $_POST ["catid"] ) . " WHERE id =" . sqlesc ( $_POST ["reqid"] ) . "" ) or sqlerr ( __FILE__, __LINE__ );
					$Cache->delete_value ( 'req_' . $_POST ["reqid"] . '_req_name' );
					stderr ( "成功", "编辑成功，<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				} else
					stderr ( "出错了！！！", "你没有该权限！！！<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				die ();
				break;
			}
		
		case "res" :
			{
				stdhead ( $lang_req ['head_resreq'] );
				stdmsg ( "我要应求", "
	<form action=viewrequest.php method=post>
	<input type=hidden name=action value=takeres />
	<input type=hidden name=reqid value=\"" . $_GET ["id"] . "\" />
	请输入种子的id:（请根据地址栏填写）<spanr/>http://127.0.0.1/nwupt?id=<input type=text name=torrentid size=11/><spanr/>
	<input type=submit value=提交 >&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onclick=\"location.href='viewrequest.php'\" value=\"返回\" ></form>", 0 );
				stdfoot ();
				die ();
				break;
			}
		
		case "takeres" :
			{
				if (! is_numeric ( $_POST ["reqid"] ))
					stderr ( "出错了！！！", "不要试图入侵系统！" );
				$res = sql_query ( "SELECT * FROM req WHERE id =" . sqlesc ( $_POST ["reqid"] ) . "" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！<a href=viewrequest.php>点击这里返回</a>", 0 );
				$arr = mysql_fetch_assoc ( $res );
				if ($arr ["finish"] != "no")
					stderr ( "出错了！", "该求种已完成或撤销！<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				if (! is_numeric ( $_POST ["torrentid"] ))
					stderr ( "出错了！！！", "种子ID必须为数字！<a href=viewrequest.php?action=res&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				$res = sql_query ( "SELECT * FROM torrents WHERE id =" . sqlesc ( $_POST ["torrentid"] ) . "" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该种子不存在！<a href=viewrequest.php?action=res&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				$tor = mysql_fetch_assoc ( $res );
				if ($tor [last_seed] == "0000-00-00 00:00:00")
					stderr ( "出错了！！！", "该种子尚未正式发布！<a href=viewrequest.php?action=res&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				$res = sql_query ( "SELECT * FROM resreq WHERE reqid=" . sqlesc ( $_POST ["reqid"] ) . " AND torrentid=" . sqlesc ( $_POST ["torrentid"] ) . "" );
				if (mysql_num_rows ( $res ) != 0)
					stderr ( "出错了！", "请不要重复应求，<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0 );
				sql_query ( "INSERT resreq (reqid , torrentid) VALUES ( " . sqlesc ( $_POST ["reqid"] ) . " , " . sqlesc ( $_POST ["torrentid"] ) . ")" );
				stderr ( "成功", "应求成功，<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", 0, 1, 1, 0 );
				$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
				$subject = sqlesc ( "你发布的求种" . $arr ["name"] . "有应求" );
				$notifs = sqlesc ( $CURUSER ["username"] . "应求了你的求种" . "[url=viewrequest.php?action=view&id=" . $arr ["id"] . "]" . $arr ["name"] . "[/url]" );
				sql_query ( "INSERT INTO messages (sender, receiver, subject, msg, added, goto) VALUES(0, '" . $arr ['userid'] . "', $subject, $notifs, $added, 0)" ) or sqlerr ( __FILE__, __LINE__ );
				$Cache->delete_value ( 'user_' . $arr ['userid'] . '_unread_message_count' );
				$Cache->delete_value ( 'user_' . $arr ['userid'] . '_inbox_count' );
				break;
			}
		
		case "addamount" :
			{
				if (! is_numeric ( $_POST ["reqid"] ))
					stderr ( "出错了！！！", "不要试图入侵系统" );
				$res = sql_query ( "SELECT * FROM req WHERE id =" . sqlesc ( $_POST ["reqid"] ) . "" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！" );
				$arr = mysql_fetch_assoc ( $res );
				if ($arr ["finish"] == "yes")
					stderr ( "出错了！", "该求种已完成！" );
				if (! is_numeric ( $_POST ["amount"] ))
					stderr ( "出错了！", "赏金必须为数字！" );
				$amount = 0 + $_POST ["amount"];
				if ($amount < 100)
					stderr ( "出错了！", "追加悬赏赏金不得小于100个魔力值 ！" );
				if ($amount > 5000)
					stderr ( "出错了！", "追加悬赏赏金不得大于5000个魔力值 ！" );
					// $amount += 25;
				$newseedbonus = $amount + 25;
				$newamount = $arr ["amount"] + $amount;
				if ($amount > $CURUSER [seedbonus])
					stderr ( "出错了！", "你没有那么多魔力值 ！" );
				sql_query ( "UPDATE users SET seedbonus = seedbonus - " . $newseedbonus . " WHERE id = " . $CURUSER [id] );
//				writeBonusComment($CURUSER [id],"使用$newseedbonus 魔力值 追加了悬赏". sqlesc ( $_POST ["reqid"] ));
				sql_query ( "UPDATE req SET amount = " . $newamount . ", resetdate = '" . date ( "Y-m-d H:i:s" ) . "' WHERE id = " . sqlesc ( $_POST ["reqid"] ) );
				if ($arr ["userid"] != $CURUSER ["id"]) {
					$res = sql_query ( "SELECT * FROM givebonus WHERE bonusfromuserid = '" . $CURUSER ["id"] . "' AND bonustotorrentid =" . sqlesc ( $_POST ["reqid"] ) . " AND type='3'" );
					// $amount -= 25;
					if (mysql_num_rows ( $res ) == 0)
						sql_query ( "INSERT INTO givebonus ( bonusfromuserid , bonustotorrentid, bonus, type ) VALUES ( '" . $CURUSER ["id"] . "'," . sqlesc ( $_POST ["reqid"] ) . ",'" . $amount . "','3')" );
					else {
						$arr = mysql_fetch_assoc ( $res );
						$amount += $arr ["bonus"];
						sql_query ( "UPDATE givebonus SET bonus = '" . $amount . "' WHERE bonusfromuserid = '" . $CURUSER ["id"] . "' AND bonustotorrentid =" . sqlesc ( $_POST ["reqid"] ) . " AND type='3'" );
					}
				}
				stderr ( "成功", "追加悬赏成功，<a href=viewrequest.php?action=view&id=" . $_POST ["reqid"] . ">点击这里返回</a>", false );
				die ();
				break;
			}
		
		case "delete" :
			{
				if (! is_numeric ( $_GET ["id"] ))
					stderr ( "出错了！！！", "求种ID必须为数字", true );
				$res = sql_query ( "SELECT * FROM req WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！", true );
				$arr = mysql_fetch_assoc ( $res );
				if (get_user_class () >= 13) {
					if ($_POST ["reasontype"]) {
						$rt = 0 + $_POST ["reasontype"];
						if (! is_int ( $rt ) || $rt < 1 || $rt > 5)
							stderr ( "出错了！", "删除理由" . $rt . "不成立，<a href=viewrequest.php?action=delete&id=" . $_GET ["id"] . " >请返回删除页面重选</a>", false );
						$r = $_POST ["reason"];
						if ($rt == 1)
							$reason = "求种成功";
						elseif ($rt == 2)
							$reason = "长时间无人应求";
						elseif ($rt == 3)
							$reason = "与既有求种" . ($r [0] ? (" " . trim ( $r [0] )) . " " : "") . "重复";
						elseif ($rt == 4)
							$reason = "格式不符合要求" . ($r [1] ? (" - " . trim ( $r [1] )) : "");
						elseif ($rt == 5) {
							if (! $r [2])
								stderr ( "出错了！", "删除理由未填写，<a href=viewrequest.php?action=delete&id=" . $_GET ["id"] . " >请返回删除页面重填</a>", false );
							$reason = trim ( $r [2] );
						}
						sql_query ( "DELETE FROM req WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM resreq WHERE reqid ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM comments WHERE request ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM givebonus WHERE bonustotorrentid ='" . $_GET ["id"] . "' AND type = '3'" ) or sqlerr ( __FILE__, __LINE__ );
						write_log ( "求种：管理员 " . $CURUSER ["username"] . " 删除了求种 " . $_GET ["id"] . " ( " . $arr ["name"] . " ) ,理由：" . $reason );
						
						if ($CURUSER ["id"] != $arr ['userid']) {
							$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
							$subject = sqlesc ( "你发布的求种" . $arr ["name"] . "被删除" );
							$notifs = sqlesc ( "管理员" . $CURUSER ["username"] . "删除了你的求种 " . $arr ["name"] . " 。原因：" . $reason );
							sql_query ( "INSERT INTO messages (sender, receiver, subject, msg, added) VALUES(0, '" . $arr ['userid'] . "', $subject, $notifs, $added)" ) or sqlerr ( __FILE__, __LINE__ );
							$Cache->delete_value ( 'user_' . $arr ['userid'] . '_unread_message_count' );
							$Cache->delete_value ( 'user_' . $arr ['userid'] . '_inbox_count' );
						}
						$Cache->delete_value ( 'req_' . $_GET ["id"] . '_req_name' );
						stderr ( "成功", "删除求种成功，<a href=viewrequest.php>点击这里返回</a>", false );
					} else {
						print ("<form action=\"viewrequest.php?action=delete&id=" . $_GET ["id"] . "\" method=\"post\" >") ;
						stderr ( "删除求种-理由：", "<table align=\"center\"  cellspacing=\"0\" cellpadding=\"5\">\n" . "<tr><td class=\"rowhead\" valign=\"top\" align=\"right\"><input name=\"reasontype\" type=\"radio\" value=\"1\" />&nbsp;成功</td>" . "<td class=\"rowfollow\" valign=\"middle\" align=\"left\">已经有用户成功发布该资源</td></tr>" . "<td class=\"rowhead\" valign=\"top\" align=\"right\"><input name=\"reasontype\" type=\"radio\" value=\"2\" />&nbsp;过期" . "</td>" . "<td class=\"rowfollow\" valign=\"middle\" align=\"left\">长时间无人应求</td></tr>" . "<td class=\"rowhead\" valign=\"top\" align=\"right\"><input name=\"reasontype\" type=\"radio\" value=\"3\" />&nbsp;重复</td>" . "<td class=\"rowfollow\" ><input type=\"text\" style=\"width: 200px\" name=\"reason[]\" /></td></tr>" . "<td class=\"rowhead\" valign=\"top\" align=\"right\"><input name=\"reasontype\" type=\"radio\" value=\"4\" />&nbsp;" . "格式错</td>" . "<td class=\"rowfollow\" valign=\"top\" align=\"left\"><input type=\"text\" style=\"width: 200px\" name=\"reason[]\" /></td></tr>" . "<td class=\"rowhead \" valign=\"top\" align=\"right\"><input name=\"reasontype\" type=\"radio\" value=\"5\" checked=\"checked\" />&nbsp;" . "其他</td>" . "<td class=\"rowfollow\" valign=\"top\" align=\"left\"><input type=\"text\" style=\"width: 200px\" name=\"reason[]\" />(必填)</td></tr>" . "<tr><td class=\"toolbox\" colspan=\"2\" align=\"center\"><input type=\"submit\" style='height: 25px' value=\"删除\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=button style='height: 25px' onclick=\"location.href='javascript:history.go(-1)'\" value=\"返回\" ></td></tr></table>\n" . "</form>", false );
					}
				} else
					stderr ( "出错了！！！", "你没有该权限！！！", true );
				die ();
				break;
			}
		
		case "fastdel" :
			{
				$ids = $_GET ["id"];
				if (is_array ( $ids )) {
					foreach ( $ids as $id ) {
						if (! is_numeric ( $id ))
							stderr ( "出错了！！！", "求种ID必须为数字", true );
					}
				} else {
					if (! is_numeric ( $ids ))
						stderr ( "出错了！！！", "求种ID必须为数字", true );
					$ids = array (
							$ids 
					);
				}
				if (get_user_class () < 13) {
					stderr ( "出错了！！！", "你没有该权限！！！", true );
				}
				foreach ( $ids as $id ) {
					$res = sql_query ( "SELECT * FROM req WHERE id ='$id'" ) or sqlerr ( __FILE__, __LINE__ );
					if (mysql_num_rows ( $res ) == 0)
						stderr ( "出错了！", "该求种已被删除！", true );
					$arr = mysql_fetch_assoc ( $res );
					if (! $_GET ['job'] || $_GET ['job'] == '删除') {
						sql_query ( "DELETE FROM req WHERE id ='" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM resreq WHERE reqid ='" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM comments WHERE request ='" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM givebonus WHERE bonustotorrentid ='" . $id . "' AND type = '3'" ) or sqlerr ( __FILE__, __LINE__ );
						write_log ( "求种：管理员 " . $CURUSER ["username"] . " 删除了求种 " . $id . " ( " . $arr ["name"] . " )" );
						$Cache->delete_value ( 'req_' . $id . '_req_name' );
						if ($CURUSER ["id"] != $arr ['userid']) {
							$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
							$subject = sqlesc ( "你发布的求种" . $arr ["name"] . "被删除" );
							$notifs = sqlesc ( "管理员" . $CURUSER ["username"] . "删除了你的求种 " . $arr ["name"] . " 。" );
							sql_query ( "INSERT INTO messages (sender, receiver, subject, msg, added) VALUES(0, '" . $arr ['userid'] . "', $subject, $notifs, $added)" ) or sqlerr ( __FILE__, __LINE__ );
							$Cache->delete_value ( 'user_' . $arr ['userid'] . '_unread_message_count' );
						}
					} else if ($_GET ['job'] == '撤销') {
						sql_query ( "UPDATE req SET finish='cancel' WHERE id ='" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
						sql_query ( "DELETE FROM resreq WHERE reqid ='" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
						$amountadds = sql_query ( "SELECT * FROM givebonus WHERE bonustotorrentid ='" . $id . "' AND type='3'" );
						$amount = 0;
						if (mysql_num_rows ( $amountadds ) > 0)
							while ( $amountadd = mysql_fetch_array ( $amountadds ) ) {
								sql_query ( "UPDATE users SET seedbonus=seedbonus +" . $amountadd ["bonus"] . " WHERE id ='" . $amountadd ["bonusfromuserid"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
//								writeBonusComment($amountadd ["bonusfromuserid"],"求种$id 被撤销，返还$amountadd[bonus] 魔力值 ");
								$amount -= $amountadd ["bonus"];
							}
						sql_query ( "UPDATE users SET seedbonus=seedbonus +" . $amount . " WHERE id ='" . $arr ["userid"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
//						writeBonusComment($arr ["userid"],"求种$id 被撤销，返还$amount 魔力值 ");
						sql_query ( "DELETE FROM givebonus WHERE bonustotorrentid ='" . $id . "' AND type = '3'" ) or sqlerr ( __FILE__, __LINE__ );
						write_log ( "求种：".($arr ['userid'] != $CURUSER ["id"] ? "管理员 " : "求种人 ") . $CURUSER ["username"] . " 撤销了求种 " . $id );
						if ($CURUSER ["id"] != $arr ['userid']) {
							$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
							$subject = sqlesc ( "你发布的求种" . $arr ["name"] . "被撤销" );
							$notifs = sqlesc ( "管理员" . $CURUSER ["username"] . "撤销了你的求种 [url=viewrequest.php?action=view&id=" . $arr ["id"] . "]" . $arr ["name"] . "[/url],赏金已经退回到相关账户中。" );
							sql_query ( "INSERT INTO messages (sender, receiver, subject, msg, added,goto) VALUES(0, '" . $arr ['userid'] . "', $subject, $notifs, $added,1)" ) or sqlerr ( __FILE__, __LINE__ );
							$Cache->delete_value ( 'user_' . $arr ['userid'] . '_unread_message_count' );
							$Cache->delete_value ( 'user_' . $arr ['userid'] . '_inbox_count' );
						}
					}
				}
				stderr ( "成功", "操作成功，<a href=\"viewrequest.php\">点击这里返回</a>", false );
				die ();
				break;
			}
		
		case "delres" :
			{
				if (! is_numeric ( $_GET ["id"] ))
					stderr ( "出错了！！！", "非法操作I", true );
				$res = sql_query ( "SELECT * FROM resreq WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该应求已删除", true );
				$arr = mysql_fetch_assoc ( $res );
				if (get_user_class () >= 13) {
					sql_query ( "DELETE FROM resreq WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
					header ( "Location: viewrequest.php?action=view&id=" . $arr ["reqid"] );
				} else
					stderr ( "出错了！！！", "你没有该权限！！！", true );
				die ();
				break;
			}
		
		case "cancel" :
			{
				if (! is_numeric ( $_GET ["id"] ))
					stderr ( "出错了！！！", "求种ID必须为数字", true );
				$res = sql_query ( "SELECT * FROM req WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！", true );
				$arr = mysql_fetch_assoc ( $res );
				if ($arr ['finish'] == "yes")
					stderr ( "出错了！", "该求种已经完成，无法撤销！", true );
				if ($arr ['finish'] == "cancel")
					stderr ( "出错了！", "该求种已经撤销！", true );
				if ($arr ['userid'] != $CURUSER [id] && get_user_class () < 13)
					stderr ( "出错了！", "你没有该权限！", true );
				$res = sql_query ( "SELECT * FROM resreq WHERE reqid ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) > 0 && get_user_class () < 13)
					stderr ( "出错了！", "	
	<form id=contact_cancel method=post name=contact_cancel action=takecontact.php><input type=hidden name=returnto value=\"viewrequest.php?action=view&id=" . $_GET ["id"] . "\"><input type=hidden name=\"subject\" value=\"撤消求种请求\" /><input type=hidden name=\"body\" value=\"" . $CURUSER [username] . "请求撤销求种[url=viewrequest.php?action=view&id=" . $_GET ["id"] . "]" . $arr ["name"] . "[/url]，请审批。\" /></form>	
	有应求的种子不允许自行撤销求种，<a href=\"javascript:document.contact_cancel.submit();\">点击这里联系管理组</a>（请勿重复点击！），<a href=viewrequest.php?action=view&id=" . $_GET ["id"] . ">点击这里返回</a>", false );

				if ($arr ['added'] > date ( "Y-m-d H:i:s", time () - 14 * 86400 ) && get_user_class () < 13)
					stderr ( "出错了！", "发布求种2周后才允许自行撤销！！<a href=viewrequest.php?action=view&id=" . $_GET ["id"] . ">点击这里返回</a>", false );
				else{
					$amount = $arr ["amount"];
					sql_query ( "UPDATE req SET finish='cancel' WHERE id ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
					sql_query ( "DELETE FROM resreq WHERE reqid ='" . $_GET ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
					 // 检查是否有非求种人追加悬赏
						$amountadds = sql_query ( "SELECT * FROM givebonus WHERE bonustotorrentid ='" . $id . "' AND type='3'" );
						if (mysql_num_rows ( $amountadds ) > 0)
							while ( $amountadd = mysql_fetch_array ( $amountadds ) ) {
								sql_query ( "UPDATE users SET seedbonus=seedbonus +" . $amountadd ["bonus"] . " WHERE id ='" . $amountadd ["bonusfromuserid"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
								$amount -= $amountadd ["bonus"];
//								writeBonusComment($amountadd ["bonusfromuserid"],"求种$id 被撤销，返还$amountadd[bonus] 魔力值");
							}
						sql_query ( "UPDATE users SET seedbonus=seedbonus +" . $amount . " WHERE id ='" . $arr ["userid"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
//						writeBonusComment($arr ["userid"],"求种$id 被撤销，返还$amount 魔力值");
					}
					sql_query ( "DELETE FROM givebonus WHERE bonustotorrentid ='" . $_GET ["id"] . "' AND type = '3'" ) or sqlerr ( __FILE__, __LINE__ );
					write_log ( "求种：".($arr ['userid'] != $CURUSER ["id"] ? "管理员 " : "求种人 ") . $CURUSER ["username"] . " 撤销了求种 " . $_GET ["id"] );
					if ($CURUSER ["id"] != $arr ['userid']) {
						$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
						$subject = sqlesc ( "你发布的求种" . $arr ["name"] . "被撤销" );
						$notifs = sqlesc ( "管理员" . $CURUSER ["username"] . "撤销了你的求种 [url=viewrequest.php?action=view&id=" . $arr ["id"] . "]" . $arr ["name"] . "[/url],赏金已经退回到相关账户中。" );
						sql_query ( "INSERT INTO messages (sender, receiver, subject, msg, added, goto) VALUES(0, '" . $arr ['userid'] . "', $subject, $notifs, $added,1)" ) or sqlerr ( __FILE__, __LINE__ );
						$Cache->delete_value ( 'user_' . $arr ['userid'] . '_unread_message_count' );
						$Cache->delete_value ( 'user_' . $arr ['userid'] . '_inbox_count' );
					}
					stderr ( "成功", "撤销求种成功，<a href=viewrequest.php>点击这里返回</a>", false );
				}
				die ();
				break;

		
		case "confirm" :
			{
				if (! is_numeric ( $_POST ["id"] ))
					stderr ( "出错了！！！", "不要试图入侵系统", true );
				$res = sql_query ( "SELECT * FROM req WHERE id ='" . $_POST ["id"] . "'" ) or sqlerr ( __FILE__, __LINE__ );
				if (mysql_num_rows ( $res ) == 0)
					stderr ( "出错了！", "该求种已被删除！", true );
				$arr = mysql_fetch_assoc ( $res );
				if (empty ( $_POST ["torrentid"] ))
					stderr ( "出错了！", "你没有选择符合条件的应求！", true );
				else if ($arr ['finish'] == "yes")
					stderr ( "出错了！", "该应求已经确认！<a href=viewrequest.php?action=view&id=" . $_POST ["id"] . ">点击这里返回</a>", false );
				else
					$torrentid = $_POST ["torrentid"];
				if ($arr ['userid'] == $CURUSER [id] || get_user_class () >= 13) {
					$amount = $arr ["amount"] / count ( $torrentid );
					sql_query ( "UPDATE req SET finish = 'yes', finished_time = '" . date ( "Y-m-d H:i:s" ) . "' WHERE id = " . $_POST ["id"] ) or sqlerr ( __FILE__, __LINE__ );
					sql_query ( "UPDATE resreq SET chosen = 'yes' WHERE reqid = " . $_POST ["id"] . " AND ( torrentid = '" . join ( "' OR torrentid = '", $torrentid ) . "' )" ) or sqlerr ( __FILE__, __LINE__ );
					sql_query ( "DELETE FROM resreq WHERE reqid ='" . $_POST ["id"] . "' AND chosen = 'no'" ) or sqlerr ( __FILE__, __LINE__ );
					$res = sql_query ( "SELECT owner FROM torrents WHERE ( id = '" . join ( "' OR id = '", $torrentid ) . "' ) " ) or sqlerr ( __FILE__, __LINE__ );
					while ( $row = mysql_fetch_array ( $res ) )
						$owner [] = $row [0];
					$resuser = get_user_row ( $arr ['userid'] );
					$subject = ($arr ['userid'] != $CURUSER [id] ? "管理员" . $CURUSER ['username'] . "代替" : "") . $resuser [username] . "通过了你的应求";
					$notifs = "你因此获得了悬赏的" . $amount . "魔力值 。详情请见[url=viewrequest.php?action=view&id=" . $_POST ["id"] . "]这里[/url]";
					$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
					foreach ( $owner as $id ) {
						sql_query ( "UPDATE users SET seedbonus = seedbonus + $amount WHERE id = '" . $id . "'" ) or sqlerr ( __FILE__, __LINE__ );
//						writeBonusComment($id,"求种$_POST[id] 被确认，增加悬赏$amount 魔力值 ");
						sql_query ( "INSERT INTO messages (sender, receiver, subject, msg, added,goto) VALUES(0, " . $id . ", '$subject', '$notifs', $added,1)" ) or sqlerr ( __FILE__, __LINE__ );
					}
					write_log ("求种：". ($arr ['userid'] != $CURUSER ["id"] ? "管理员 " : "求种人 ") . $CURUSER ["username"] . " 确认了求种 " . $_POST ["id"] );
					stderr ( "成功", "确认成功，<a href=viewrequest.php?action=view&id=" . $_POST ["id"] . ">点击这里返回</a>", false );
				
				}
				break;
			}
	}

}
die ();

?>