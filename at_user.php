<?php
require "include/bittorrent.php";
dbconn();
//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");
//if(!(0+$CURUSER["id"])){header("Content-Type: text/html; charset=utf-8");die("你寻找的萝莉已经被推倒!");}
if (isset($_POST['str']) && $_POST['str'] != '')
{	$searchstr = unesc(trim($_POST['str']));
	
	$suggest_query = sql_query("SELECT id , username FROM users  WHERE username LIKE " . sqlesc($searchstr . "%")." ORDER BY id LIMIT 5");
	unset($result);
	
	while($suggest = mysql_fetch_array($suggest_query)){
		$result[] = array( 'username'=>$suggest[username] , 'userid'=>$suggest[id] );
	}
	
}
if(!$result)$result[]=array( 'userid'=>"查无此人" , 'username'=>$_POST['str'] );
echo json_encode($result);
?>
