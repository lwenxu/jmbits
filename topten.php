<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

function bark($msg) {
	global $lang_topten;
	genbark($msg, $lang_topten['std_error']);
}
if (get_user_class() < $topten_class){
	stderr($lang_topten['std_sorry'],$lang_topten['std_permission_denied_only'].get_user_class_name($topten_class,false,true,true).$lang_topten['std_or_above_can_view'],false);
}
stdhead($lang_topten['head_top_ten']);
begin_main_frame();
$type = isset($_GET["type"]) ? 0 + $_GET["type"] : 0;
if (!in_array($type,array(1,2,3,4,5,6,7)))
$type = 1;
$limit = isset($_GET["lim"]) ? 0 + $_GET["lim"] : false;
$subtype = isset($_GET["subtype"]) ? $_GET["subtype"] : false;

print("<div id=\"usercpnav\" style='margin-left: 36%'>
  <ul id=\"\" class=\"nav nav-pills\">"  .
($type != 1 || $limit ? "<li class='nav-settings-item'><a href='topten.php?type=1'>".$lang_topten['text_users']."</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=1\">".$lang_topten['text_users']."</a></li>")  .
($type != 2 || $limit ? "<li class='nav-settings-item'><a href='topten.php?type=2'>".$lang_topten['text_torrents']."</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=2\">".$lang_topten['text_torrents']."</a></li>")  .

($type != 4 || $limit ? "<li class='nav-settings-item'><a href='topten.php?type=4'>".$lang_topten['text_peers']."</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=4\">".$lang_topten['text_peers']."</a></li>")   .
($type != 5 || $limit ? "<li class='nav-settings-item'><a href='topten.php?type=5'>".$lang_topten['text_community']."</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=5\">".$lang_topten['text_community']."</a></li>")   .
($type != 7 || $limit ? "<li class='nav-settings-item'> <a href='topten.php?type=7'> ".$lang_topten['text_search']."</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=7\">".$lang_topten['text_search']."</a></li>")   .
($type != 6 || $limit ? "<li class='nav-settings-item'><a href='topten.php?type=6'>".$lang_topten['text_other']."</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=6\">".$lang_topten['text_other']."</a></li>")  . " 
 </ul>
</div>\n");
//去掉国家
//($type != 3 || $limit ? "<li class='nav-settings-item'><a href='topten.php?type=3'>" . $lang_topten['text_countries'] . "</a></li>" : "<li class=\"active\"><a class=\"nav-settings-item\" href=\"topten.php?type=3\">" . $lang_topten['text_countries'] . "</a></li>") .


if (!$limit || $limit > 250)
$limit = 10;

$cachename = "topten_type_".$type."_limit_".$limit."_subtype_".$subtype;
$cachetime = 60 * 60; // 60 minutes
// START CACHE
$Cache->new_page($cachename, $cachetime, true);
if (!$Cache->get_page())
{
$Cache->add_whole_row();

/////////////////////////////////////////////////////////

if ($type == 1)
{
	$mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";


	if ($limit == 10 || $subtype == "ul")
	{
		$order = "uploaded DESC";
		$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_uploaders'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=ul\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=ul\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "dl")
	{
		$order = "downloaded DESC";
		$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_downloaders']  . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=dl\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=dl\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "uls")
	{
		$order = "upspeed DESC";
		$extrawhere = " AND uploaded > 53687091200";
		$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_uploaders'] . "<font class=\"small\">".$lang_topten['text_fastest_up_note'] . "</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=uls\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=uls\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "dls")
	{
		$order = "downspeed DESC";
		$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_downloaders'] ."<font class=\"small\">" . $lang_topten['text_fastest_note'] . "</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=dls\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=dls\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "bsh")
	{
		$order = "uploaded / downloaded DESC";
		$extrawhere = " AND downloaded > 53687091200";
		$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_best_sharers'] ."<font class=\"small\">".$lang_topten['text_sharers_note']."</font>"  . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=bsh\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=bsh\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "wsh")
	{
		$order = "uploaded / downloaded ASC, downloaded DESC";
		$extrawhere = " AND downloaded > 53687091200";
		$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_worst_sharers'] .$lang_topten['text_sharers_note'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=wsh\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=wsh\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
/*
	if ($limit == 10 || $subtype == "sp")
	{
		$r = sql_query( "SELECT users_torrents.userid, users_torrents.supplied, users_torrents.uploaded, users_torrents.downloaded, users_torrents.added, COUNT(snatched.id) as snatched FROM (SELECT users.id as userid, COUNT(torrents.id) as supplied, users.uploaded, users.downloaded, users.added from users LEFT JOIN torrents ON torrents.owner = users.id GROUP BY userid) as users_torrents LEFT JOIN snatched ON snatched.userid = users_torrents.userid where snatched.finished='yes' AND snatched.torrentid IN(SELECT id FROM torrents where torrents.owner != users_torrents.userid) GROUP BY users_torrents.userid ORDER BY users_torrents.supplied DESC LIMIT $limit") or sqlerr();
		supply_snatchtable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_supplied'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=sp\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=sp\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "sn")
	{
		$r = sql_query( "SELECT users_torrents.userid, users_torrents.supplied, users_torrents.uploaded, users_torrents.downloaded, users_torrents.added, COUNT(snatched.id) as snatched FROM (SELECT users.id as userid, COUNT(torrents.id) as supplied, users.uploaded, users.downloaded, users.added from users LEFT JOIN torrents ON torrents.owner = users.id GROUP BY userid) as users_torrents LEFT JOIN snatched ON snatched.userid = users_torrents.userid where snatched.finished='yes' AND snatched.torrentid IN(SELECT id FROM torrents where torrents.owner != users_torrents.userid) GROUP BY users_torrents.userid ORDER BY snatched DESC LIMIT $limit") or sqlerr();
		supply_snatchtable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_snatched'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=sn\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=sn\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
	*/
}
elseif ($type == 2)
{
	if ($limit == 10 || $subtype == "act")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_active_torrents']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=act\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=act\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "sna")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_snatched_torrents']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&lim=25&subtype=sna\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=sna\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "mdt")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND times_completed > 0 GROUP BY t.id ORDER BY data DESC, added ASC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_data_transferred_torrents']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=mdt\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=mdt\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "bse")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND seeders >= 5 GROUP BY t.id ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_best_seeded_torrents']."<font class=\"small\">".$lang_topten['text_best_seeded_torrents_note']."</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=bse\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=bse\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "wse")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed) AS data FROM torrents AS t WHERE leechers > 0 AND times_completed > 0 ORDER BY seeders / leechers ASC, leechers DESC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_worst_seeded_torrents']."<font class=\"small\">" . $lang_topten['text_worst_seeded_torrents_note'] . "</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=wse\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=wse\">Top 50</a>]</font>" : ""));
	}
}
elseif ($type == 3)
{
	if ($limit == 10 || $subtype == "us")
	{
		$r = sql_query("SELECT name, flagpic, COUNT(users.country) as num FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_users']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=us\">Top 25</a>]</font>" : ""),$lang_topten['col_users']);
	}

	if ($limit == 10 || $subtype == "ul")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_uploaded']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=ul\">Top 25</a>]</font>" : ""),$lang_topten['col_uploaded']);
	}

	if ($limit == 10 || $subtype == "avg")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY ul_avg DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_per_user']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=avg\">Top 25</a>]</font>" : ""),$lang_topten['col_average']);
	}

	if ($limit == 10 || $subtype == "r")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) AS r FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND sum(u.downloaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY r DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_ratio']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=r\">Top 25</a>]</font>" : ""),$lang_topten['col_ratio']);
	}
}
/*
elseif ($type == 4)
{
	if ($limit == 10 || $subtype == "ul")
	{
		$r = sql_query( "SELECT users.id AS userid, username,snatched.upspeed AS uprate, snatched.downspeed AS downrate FROM peers LEFT JOIN snatched ON snatched.userid = peers.userid AND snatched.torrentid = peers.torrent LEFT JOIN users ON users.id = peers.userid ORDER BY uprate DESC LIMIT $limit") or sqlerr();
		peerstable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_uploaders'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=ul\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=ul\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "dl")
	{
		$r = sql_query("SELECT users.id AS userid, username,snatched.upspeed AS uprate, snatched.downspeed AS downrate FROM peers LEFT JOIN snatched ON snatched.userid = peers.userid AND snatched.torrentid = peers.torrent LEFT JOIN users ON users.id = peers.userid ORDER BY downrate DESC LIMIT $limit") or sqlerr();

		peerstable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_downloaders'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=dl\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=dl\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "mloc")
	{
		$r = sql_query( "SELECT FROM peers LEFT JOIN locations ON peers.ip GROUP BY users.id ORDER BY commentnum DESC LIMIT $limit") or sqlerr();
		locationtable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_locations'] . ($limit == 10 ? " <font class=\"small\"> - [<a href=\"topten.php?type=$type&lim=100&subtype=mloc>".$lang_topten['text_one_hundred']."</a>] - [<a href=\"topten.php?type=$type&lim=250&subtype=mloc>".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
}
*/
elseif ($type == 5)
{
	if ($limit == 10 || $subtype == "mtop")
	{
		$r = sql_query( "SELECT users_topics.userid,  users_topics.usertopics, COUNT(posts.id) as userposts FROM (SELECT users.id as userid, COUNT(topics.id) as usertopics from users LEFT JOIN topics ON users.id = topics.userid GROUP BY users.id) as users_topics LEFT JOIN posts ON users_topics.userid = posts.userid GROUP BY users_topics.userid ORDER BY usertopics DESC LIMIT $limit") or sqlerr();
		postable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_topic'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mtop\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mtop\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
	
	if ($limit == 10 || $subtype == "mpos")
	{
		$r = sql_query( "SELECT users_topics.userid,  users_topics.usertopics, COUNT(posts.id) as userposts FROM (SELECT users.id as userid, COUNT(topics.id) as usertopics from users LEFT JOIN topics ON users.id = topics.userid GROUP BY users.id) as users_topics LEFT JOIN posts ON users_topics.userid = posts.userid GROUP BY users_topics.userid ORDER BY userposts DESC LIMIT $limit") or sqlerr();
		postable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_post'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mpos\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mpos\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
	
	if ($reviewenabled == 'yes' && ($limit == 10 || $subtype == "mrev"))
	{
		$r = sql_query( "SELECT users.id as userid, COUNT(reviews.id) as num FROM users LEFT JOIN reviews ON users.id = reviews.user GROUP BY users.id ORDER BY num DESC LIMIT $limit") or sqlerr();
		cmttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_reviewer'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mrev\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mrev\">".$lang_topten['text_top_250']."</a>]</font>" : ""), $lang_topten['col_reviews']);
	}	

	if ($limit == 10 || $subtype == "mcmt")
	{
		$r = sql_query( "SELECT users.id as userid, COUNT(comments.id) as num FROM users LEFT JOIN comments ON users.id = comments.user GROUP BY users.id ORDER BY num DESC LIMIT $limit") or sqlerr();
		cmttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_commenter'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mcmt\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mcmt\">".$lang_topten['text_top_250']."</a>]</font>" : ""), $lang_topten['col_comments']);
	}
	
	if ($limit == 10 || $subtype == "btop")
	{
		$r = sql_query("SELECT topics_posts.topicid, topics_posts.topicsubject, topics_posts.postnum, forums.id as forumid FROM (SELECT topics.id as topicid, topics.subject as topicsubject, COUNT(posts.id) as postnum, topics.forumid FROM topics LEFT JOIN posts ON topics.id = posts.topicid GROUP BY topics.id) as topics_posts LEFT JOIN forums ON topics_posts.forumid = forums.id AND forums.minclassread <= 1 ORDER BY postnum DESC LIMIT $limit") or sqlerr();
		bigtopic_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_biggest_topics'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=btop\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=btop\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
}
elseif ($type == 6)
{
	if ($limit == 10 || $subtype == "bo")
	{
		$r = sql_query("SELECT * FROM users ORDER BY seedbonus DESC LIMIT $limit") or sqlerr();
		bonustable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_bonuses'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=bo\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=bo\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

if ($prolinkpoint_bonus){
	if ($limit == 10 || $subtype == "pl")
	{
		$r = sql_query("SELECT userid, COUNT(id) AS count FROM prolinkclicks GROUP BY userid ORDER BY count DESC LIMIT $limit") or sqlerr();
		prolinkclicktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_clicks'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=pl\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=pl\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
}

	if ($limit == 10 || $subtype == "charity")
	{
		$r = sql_query("SELECT * FROM users ORDER BY charity DESC LIMIT $limit") or sqlerr();
		charityTable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_charity_giver'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=charity\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=charity\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

if ($enabledonation == 'yes'){
	if ($limit == 10 || $subtype == "do_usd")
	{
		$r = sql_query( "SELECT id, donated, donated_cny from users where donated > 0 ORDER BY donated DESC, donated_cny DESC LIMIT $limit") or sqlerr();
		donortable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_donated_USD'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=do_usd\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=do_usd\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "do_cny")
	{
		$r = sql_query( "SELECT id, donated, donated_cny from users where donated_cny > 0 ORDER BY donated DESC, donated_cny DESC LIMIT $limit") or sqlerr();
		donortable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_donated_CNY'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=do_cny\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=do_cny\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
}
	
	/*
	if ($limit == 10 || $subtype == "mbro")
	{
		$r = sql_query( "SELECT id, donated, donated_cny from users where donated_cny > 0 ORDER BY donated DESC, donated_cny DESC LIMIT $limit") or sqlerr();
		donortable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_browser'] . ($limit == 10 ? " <font class=\"small\"> - [<a href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mbro\">".$lang_topten['text_one_hundred']."</a>] - [<a href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mbro\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
	*/
	
	if ($limit == 10 || $subtype == "mcli")
	{
		$r = sql_query( "SELECT agent_allowed_family.family as client_name, COUNT(users.id) as client_num from users RIGHT JOIN agent_allowed_family ON agent_allowed_family.id = users.clientselect GROUP BY clientselect ORDER BY client_num DESC LIMIT $limit") or sqlerr();
		clienttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_client'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mcli\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mcli\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
	if ($limit == 10 || $subtype == "ss")
	{
		$r = sql_query( "SELECT stylesheets.name as stylesheet_name, COUNT(users.id) as stylesheet_num from users JOIN stylesheets ON stylesheets.id = users.stylesheet GROUP BY stylesheet ORDER BY stylesheet_num DESC LIMIT $limit") or sqlerr();
		stylesheettable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_stylesheet'] . ($limit == 10 ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=ss\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=ss\">Top 50</a>]</font>" : ""));
	}
	if ($limit == 10 || $subtype == "lang")
	{
		$r = sql_query( "SELECT language.lang_name as lang_name, COUNT(users.id) as lang_num from users JOIN language ON language.id = users.lang WHERE site_lang=1 GROUP BY lang ORDER BY lang_num DESC LIMIT $limit") or sqlerr();
		languagetable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_language'] . ($limit == 10 ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=lang\">Top 25</a>]</font>" : ""));
	}
}
/*
elseif ($type == 7)	// search
{
	if ($limit == 10 || $subtype == "lse")
	{
		$r = sql_query( "SELECT keywords, adddate from suggest ORDER BY adddate DESC LIMIT $limit") or sqlerr();
		lastsearch_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_latest_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=lse\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=lse\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "tcmo")
	{
		$current_month = mktime(0, 0, 0, date("m"), 1,   date("Y"));
		$r = sql_query("SELECT keywords, COUNT(id) as count FROM suggest WHERE UNIX_TIMESTAMP(adddate) >" . $current_month . " GROUP BY keywords ORDER BY count DESC LIMIT $limit") or sqlerr();
		search_ranktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_current_month_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=tcmo\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=tcmo\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "tlmo")
	{
		$last_month_begin = mktime(0, 0, 0, date("m")-1, 1,   date("Y"));
		$last_month_end = mktime(23, 59, 59, date("m")-1, date("t",$last_month_begin), date("Y"));
		$r = sql_query("SELECT keywords, COUNT(id) as count FROM suggest WHERE UNIX_TIMESTAMP(adddate) >" . $last_month_begin . " AND UNIX_TIMESTAMP(adddate) <" . $last_month_end . " GROUP BY keywords ORDER BY count DESC LIMIT $limit") or sqlerr();
		search_ranktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_last_month_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=tlmo\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=tlmo\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
	
	if ($limit == 10 || $subtype == "tcy")
	{
		$current_year = mktime(0, 0, 0, 1 , 1, date("Y"));
		$r = sql_query("SELECT keywords, COUNT(id) as count FROM suggest WHERE UNIX_TIMESTAMP(adddate) >" . $current_year . " GROUP BY keywords ORDER BY count DESC LIMIT $limit") or sqlerr();
		search_ranktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_current_year_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=tcy\">".$lang_topten['text_one_hundred']."</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=250&amp;subtype=tcy\">".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
}
*/
	end_main_frame();
	print("<p><font class=\"small\">".$lang_topten['text_this_page_last_updated'].date('Y-m-d H:i:s'). ", ".$lang_topten['text_started_recording_date'].$datefounded.$lang_topten['text_update_interval']."</font></p>");
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
stdfoot();
?>
