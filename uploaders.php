<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
$user_class=1;
if (get_user_class() < $user_class)
    permissiondenied();

$year=0+$_GET['year'];
if (!$year || $year < 2000)
$year=date('Y');
$month=0+$_GET['month'];
if (!$month || $month<=0 || $month>12)
$month=date('m');
$order=$_GET['order'];
$page=$_GET['page'];
if (!in_array($order, array('username', 'torrent_size', 'torrent_count')))
	$order='username';
if ($order=='username')
	$order .=' DESC';
else $order .= ' ASC';
stdhead($lang_uploaders['head_uploaders']);
begin_main_frame();
?>
<div style="width: 940px">
<?php
$year2 = substr($datefounded, 0, 4);
$yearfounded = ($year2 ? $year2 : 2007);
$yearnow=date("Y");

$timestart=strtotime($year."-".$month."-01 00:00:00");
$sqlstarttime=date("Y-m-d H:i:s", $timestart);
$timeend=strtotime("+1 month", $timestart);
$sqlendtime=date("Y-m-d H:i:s", $timeend);

print("<h1 align=\"center\">发布情况 - ".date("Y-m",$timestart)."</h1>");
echo "
<nav class='Page navigation'>
    <ul class='pagination'>
        <li><a href='?order=username'>用户名</a></li>
        <li><a href='?order=torrent_size'>种子大小</a></li>
        <li><a href='?order=torrent_count'>种子数量</a></li>
    </ul>
</nav>
";
$yearselection="<select name=\"year\">";
for($i=$yearfounded; $i<=$yearnow; $i++)
	$yearselection .= "<option value=\"".$i."\"".($i==$year ? " selected=\"selected\"" : "").">".$i."</option>";
$yearselection.="</select>";

$monthselection="<select name=\"month\">";
for($i=1; $i<=12; $i++)
	$monthselection .= "<option value=\"".$i."\"".($i==$month ? " selected=\"selected\"" : "").">".$i."</option>";
$monthselection.="</select>";

?>
<div>
<form method="get" action="?">
<span>
<?php echo $lang_uploaders['text_select_month']?><?php echo $yearselection?>&nbsp;&nbsp;<?php echo $monthselection?>&nbsp;&nbsp;<input type="submit" value="<?php echo $lang_uploaders['submit_go']?>" />
</span>
</form>
</div>

<?php
$numres = sql_query("SELECT COUNT(users.id) FROM users WHERE class >= ".$user_class) or sqlerr(__FILE__, __LINE__);
$numrow = mysql_fetch_array($numres);
$num=$numrow[0];
if (!$num)
	print("<p align=\"center\">".$lang_uploaders['text_no_uploaders_yet']."</p>");
else{
?>
<div style="margin-top: 8px">
<?php
    $user_class=1;
	print("<table class='table table-bordered'><tr>");
	print("<td >".$lang_uploaders['col_username']."</td>");
	print("<td >".$lang_uploaders['col_torrents_size']."</td>");
	print("<td >".$lang_uploaders['col_torrents_num']."</td>");
	print("<td >".$lang_uploaders['col_last_upload_time']."</td>");
	print("<td>".$lang_uploaders['col_last_upload']."</td>");
	print("</tr>");
    if (!isset($page)){
        $page=1;
    }
    $start=($page-1)*30;
	$res = sql_query("SELECT users.id AS userid, users.username AS username, COUNT(torrents.id) AS torrent_count, SUM(torrents.size) AS torrent_size FROM torrents LEFT JOIN users ON torrents.owner=users.id WHERE users.class >= ".$user_class." AND torrents.added > ".sqlesc($sqlstarttime)." AND torrents.added < ".sqlesc($sqlendtime)."ORDER BY ".$order." limit $start,30");
	$hasupuserid=array();
	while($row = mysql_fetch_array($res))
	{
		$res2 = sql_query("SELECT torrents.id, torrents.name, torrents.added FROM torrents WHERE owner=".$row['userid']." ORDER BY id DESC LIMIT 1");
		$row2 = mysql_fetch_array($res2);
		print("<tr>");
		print("<td >".get_username($row['userid'], false, true, true, false, false, true)."</td>");
		print("<td >".($row['torrent_size'] ? mksize($row['torrent_size']) : "0")."</td>");
		print("<td >".$row['torrent_count']."</td>");
		print("<td >".($row2['added'] ? gettime($row2['added']) : $lang_uploaders['text_not_available'])."</td>");
		print("<td>".($row2['name'] ? "<a href=\"details.php?id=".$row2['id']."\">".htmlspecialchars($row2['name'])."</a>" : $lang_uploaders['text_not_available'])."</td>");
		print("</tr>");
		$hasupuserid[]=$row['userid'];
		unset($row2);
	}
echo $order;
	$res3=sql_query("SELECT users.id AS userid, users.username AS username, 0 AS torrent_count, 0 AS torrent_size FROM users WHERE class >= ".$user_class.(count($hasupuserid) ? " AND users.id NOT IN (".implode(",",$hasupuserid).")" : "")." ORDER BY $order  limit $start,30") or sqlerr(__FILE__, __LINE__);
	while($row = mysql_fetch_array($res3))
	{
		$res2 = sql_query("SELECT torrents.id, torrents.name, torrents.added FROM torrents WHERE owner=".$row['userid']." ORDER BY id DESC LIMIT 1");
		$row2 = mysql_fetch_array($res2);
		print("<tr>");
		print("<td class=\"colfollow\">".get_username($row['userid'], false, true, true, false, false, true)."</td>");
		print("<td class=\"colfollow\">".($row['torrent_size'] ? mksize($row['torrent_size']) : "0")."</td>");
		print("<td class=\"colfollow\">".$row['torrent_count']."</td>");
		print("<td class=\"colfollow\">".($row2['added'] ? gettime($row2['added']) : $lang_uploaders['text_not_available'])."</td>");
		print("<td class=\"colfollow\">".($row2['name'] ? "<a href=\"details.php?id=".$row2['id']."\">".htmlspecialchars($row2['name'])."</a>" : $lang_uploaders['text_not_available'])."</td>");
		print("</tr>");
		$count++;
		unset($row2);
	}
	print("</table>");
?>
</div>

<?php
}
?>
</div>
<?php
$res4=mysql_query("SELECT COUNT(*) AS count FROM users");
$users=mysql_fetch_assoc($res4);
$count=$users['count'];
$pages=$count/30;
$pages=ceil($pages);
echo "
<nav class='Page navigation'>
    <ul class='pagination'>";
for ($i=1;$i<$pages;$i++){
    echo "<li><a href=\"?page=$i\">$i</a></li>";
}
echo"    </ul>
</nav>

";
end_main_frame();
stdfoot();
?>
