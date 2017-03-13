<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$id = 0 + $_GET['id'];
if(isset($CURUSER))
{
	$s = "<table class=\"table table-bordered\"  cellspacing=0 cellpadding=\"5\">\n";

	$subres = sql_query("SELECT * FROM files WHERE torrent = ".sqlesc($id)." ORDER BY id");
	$s.="<tr><td>".$lang_viewfilelist['col_path']."</td><td >文件大小</td></tr>\n";
	while ($subrow = mysql_fetch_array($subres)) {
		$s .= "<tr><td class=rowfollow>" . $subrow["filename"] . "</td><td >" . mksize($subrow["size"]) . "</td></tr>\n";
	}
	$s .= "</table>\n";
	echo $s;
}
?>
