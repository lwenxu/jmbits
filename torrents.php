<?php
require_once("include/bittorrent.php");
dbconn(true);
require_once(get_langfile_path("torrents.php"));
loggedinorreturn();
parked();
//check searchbox
$sectiontype = $browsecatmode;
$showsubcat = get_searchbox_value($sectiontype, 'showsubcat');//whether show subcategory (i.e. sources, codecs) or not
$showsource = get_searchbox_value($sectiontype, 'showsource'); //whether show sources or not
$showmedium = get_searchbox_value($sectiontype, 'showmedium'); //whether show media or not
$showcodec = get_searchbox_value($sectiontype, 'showcodec'); //whether show codecs or not
$showstandard = get_searchbox_value($sectiontype, 'showstandard'); //whether show standards or not
$showprocessing = get_searchbox_value($sectiontype, 'showprocessing'); //whether show processings or not
$showteam = get_searchbox_value($sectiontype, 'showteam'); //whether show teams or not
$showaudiocodec = get_searchbox_value($sectiontype, 'showaudiocodec'); //whether show audio codec or not
$catsperrow = get_searchbox_value($sectiontype, 'catsperrow'); //show how many cats per line in search box
$catpadding = get_searchbox_value($sectiontype, 'catpadding'); //padding space between categories in pixel

$cats = genrelist($sectiontype);
if ($showsubcat){
	if ($showsource) $sources = searchbox_item_list("sources");
	if ($showmedium) $media = searchbox_item_list("media");
	if ($showcodec) $codecs = searchbox_item_list("codecs");
	if ($showstandard) $standards = searchbox_item_list("standards");
	if ($showprocessing) $processings = searchbox_item_list("processings");
	if ($showteam) $teams = searchbox_item_list("teams");
	if ($showaudiocodec) $audiocodecs = searchbox_item_list("audiocodecs");
}

$searchstr_ori = htmlspecialchars(trim($_GET["search"]));
$searchstr = mysql_real_escape_string(trim($_GET["search"]));
if (empty($searchstr))
	unset($searchstr);

// sorting by MarkoStamcar
if ($_GET['sort'] && $_GET['type']) {

	$column = '';
	$ascdesc = '';

	switch($_GET['sort']) {
		case '1': $column = "name"; break;
		case '2': $column = "numfiles"; break;
		case '3': $column = "comments"; break;
		case '4': $column = "added"; break;
		case '5': $column = "size"; break;
		case '6': $column = "times_completed"; break;
		case '7': $column = "seeders"; break;
		case '8': $column = "leechers"; break;
		case '9': $column = "owner"; break;
		default: $column = "id"; break;
	}

	switch($_GET['type']) {
		case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
		case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
		default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
	}

	if($column == "owner")
	{
		$orderby = "ORDER BY pos_state DESC, torrents.anonymous, users.username " . $ascdesc;
	}
	else
	{
		$orderby = "ORDER BY pos_state DESC, torrents." . $column . " " . $ascdesc;
	}

	$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";

} else {

	$orderby = "ORDER BY pos_state DESC, torrents.id DESC";
	$pagerlink = "";

}

$addparam = "";
$wherea = array();
$wherecatina = array();
if ($showsubcat){
	if ($showsource) $wheresourceina = array();
	if ($showmedium) $wheremediumina = array();
	if ($showcodec) $wherecodecina = array();
	if ($showstandard) $wherestandardina = array();
	if ($showprocessing) $whereprocessingina = array();
	if ($showteam) $whereteamina = array();
	if ($showaudiocodec) $whereaudiocodecina = array();
}
//----------------- start whether show torrents from all sections---------------------//
if ($_GET)
	$allsec = 0 + $_GET["allsec"];
else $allsec = 0;
if ($allsec == 1)		//show torrents from all sections
{
	$addparam .= "allsec=1&";
}
// ----------------- end whether ignoring section ---------------------//
// ----------------- start bookmarked ---------------------//
if ($_GET)
	$inclbookmarked = 0 + $_GET["inclbookmarked"];
elseif ($CURUSER['notifs']){
	if (strpos($CURUSER['notifs'], "[inclbookmarked=0]") !== false)
		$inclbookmarked = 0;
	elseif (strpos($CURUSER['notifs'], "[inclbookmarked=1]") !== false)
		$inclbookmarked = 1;
	elseif (strpos($CURUSER['notifs'], "[inclbookmarked=2]") !== false)
		$inclbookmarked = 2;
}
else $inclbookmarked = 0;

if (!in_array($inclbookmarked,array(0,1,2)))
{
	$inclbookmarked = 0;
	write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking inclbookmarked field in" . $_SERVER['SCRIPT_NAME'], 'mod');
}
if ($inclbookmarked == 0)  //all(bookmarked,not)
{
	$addparam .= "inclbookmarked=0&";
}
elseif ($inclbookmarked == 1)		//bookmarked
{
	$addparam .= "inclbookmarked=1&";
	if(isset($CURUSER))
	$wherea[] = "torrents.id IN (SELECT torrentid FROM bookmarks WHERE userid=" . $CURUSER['id'] . ")";
}
elseif ($inclbookmarked == 2)		//not bookmarked
{
	$addparam .= "inclbookmarked=2&";
	if(isset($CURUSER))
	$wherea[] = "torrents.id NOT IN (SELECT torrentid FROM bookmarks WHERE userid=" . $CURUSER['id'] . ")";
}
// ----------------- end bookmarked ---------------------//

if (!isset($CURUSER) || get_user_class() < $seebanned_class)
	$wherea[] = "banned != 'yes'";
// ----------------- start include dead ---------------------//

if (isset($_GET["incldead"]))
	$include_dead = 0 + $_GET["incldead"];
elseif ($CURUSER['notifs']){
	if (strpos($CURUSER['notifs'], "[incldead=0]") !== false)
		$include_dead = 0;
	elseif (strpos($CURUSER['notifs'], "[incldead=1]") !== false)
		$include_dead = 1;
	elseif (strpos($CURUSER['notifs'], "[incldead=2]") !== false)
		$include_dead = 2;
	else $include_dead = 1;
}
else $include_dead = 1;

if (!in_array($include_dead,array(0,1,2,3,4)))
{
	$include_dead = 0;
	write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking incldead field in" . $_SERVER['SCRIPT_NAME'], 'mod');
}
if ($include_dead == 0)  //all(active,dead)
{
	$addparam .= "incldead=0&";
}
elseif ($include_dead == 1)		//active
{
	$addparam .= "incldead=1&";
	$wherea[] = "visible = 'yes'";
}
elseif ($include_dead == 2)		//dead
{
	$addparam .= "incldead=2&";
	$wherea[] = "visible = 'no'";
}elseif ($include_dead == 3)		//conservation
{
	$addparam .= "incldead=2&";
	$wherea[] = "conservation = 1";
}elseif ($include_dead == 4)		//recommend
{
	$addparam .= "incldead=2&";
	$wherea[] = "recommend = 1";
}
// ----------------- end include dead ---------------------//
if ($_GET)
	$special_state = 0 + $_GET["spstate"];
elseif ($CURUSER['notifs']){
	if (strpos($CURUSER['notifs'], "[spstate=0]") !== false)
		$special_state = 0;
	elseif (strpos($CURUSER['notifs'], "[spstate=1]") !== false)
		$special_state = 1;
	elseif (strpos($CURUSER['notifs'], "[spstate=2]") !== false)
		$special_state = 2;
	elseif (strpos($CURUSER['notifs'], "[spstate=3]") !== false)
		$special_state = 3;
	elseif (strpos($CURUSER['notifs'], "[spstate=4]") !== false)
		$special_state = 4;
	elseif (strpos($CURUSER['notifs'], "[spstate=5]") !== false)
		$special_state = 5;
	elseif (strpos($CURUSER['notifs'], "[spstate=6]") !== false)
		$special_state = 6;
	elseif (strpos($CURUSER['notifs'], "[spstate=6]") !== false)
		$special_state = 7;
}
else $special_state = 0;

if (!in_array($special_state,array(0,1,2,3,4,5,6,7)))
{
	$special_state = 0;
	write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking spstate field in " . $_SERVER['SCRIPT_NAME'], 'mod');
}
if($special_state == 0)	//all
{
	$addparam .= "spstate=0&";
}
elseif ($special_state == 1)	//normal
{
	$addparam .= "spstate=1&";

	$wherea[] = "sp_state = 1";

	if(get_global_sp_state() == 1)
	{
		$wherea[] = "sp_state = 1";
	}
}
elseif ($special_state == 2)	//free
{
	$addparam .= "spstate=2&";

	if(get_global_sp_state() == 1)
	{
		$wherea[] = "sp_state = 2";
	}
	else if(get_global_sp_state() == 2)
	{
		;
	}
}
elseif ($special_state == 3)	//2x up
{
	$addparam .= "spstate=3&";
	if(get_global_sp_state() == 1)	//only sp state
	{
		$wherea[] = "sp_state = 3";
	}
	else if(get_global_sp_state() == 3)	//all
	{
		;
	}
}
elseif ($special_state == 4)	//2x up and free
{
	$addparam .= "spstate=4&";

	if(get_global_sp_state() == 1)	//only sp state
	{
		$wherea[] = "sp_state = 4";
	}
	else if(get_global_sp_state() == 4)	//all
	{
		;
	}
}
elseif ($special_state == 5)	//half down
{
	$addparam .= "spstate=5&";

	if(get_global_sp_state() == 1)	//only sp state
	{
		$wherea[] = "sp_state = 5";
	}
	else if(get_global_sp_state() == 5)	//all
	{
		;
	}
}
elseif ($special_state == 6)	//half down
{
	$addparam .= "spstate=6&";

	if(get_global_sp_state() == 1)	//only sp state
	{
		$wherea[] = "sp_state = 6";
	}
	else if(get_global_sp_state() == 6)	//all
	{
		;
	}
}
elseif ($special_state == 7)	//30% down
{
	$addparam .= "spstate=7&";

	if(get_global_sp_state() == 1)	//only sp state
	{
		$wherea[] = "sp_state = 7";
	}
	else if(get_global_sp_state() == 7)	//all
	{
		;
	}
}

$category_get = 0 + $_GET["cat"];
$cate2_get=$_GET['cate2id'];
if ($showsubcat){
if ($showsource) $source_get = 0 + $_GET["source"];
if ($showmedium) $medium_get = 0 + $_GET["medium"];
if ($showcodec) $codec_get = 0 + $_GET["codec"];
if ($showstandard) $standard_get = 0 + $_GET["standard"];
if ($showprocessing) $processing_get = 0 + $_GET["processing"];
if ($showteam) $team_get = 0 + $_GET["team"];
if ($showaudiocodec) $audiocodec_get = 0 + $_GET["audiocodec"];
}

$all = 0 + $_GET["all"];

if (!$all)
{
	if (!$_GET && $CURUSER['notifs'])
	{
		$all = true;
		foreach ($cats as $cat)
		{
			$all &= $cat[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[cat'.$cat['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$catcheck = false;
			else
			$catcheck = true;

			if ($catcheck)
			{
				$wherecatina[] = $cat[id];
				$addparam .= "cat$cat[id]=1&";
			}
		}
		if ($showsubcat){
		if ($showsource)
		foreach ($sources as $source)
		{
			$all &= $source[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[sou'.$source['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$sourcecheck = false;
			else
			$sourcecheck = true;

			if ($sourcecheck)
			{
				$wheresourceina[] = $source[id];
				$addparam .= "source$source[id]=1&";
			}
		}
		if ($showmedium)
		foreach ($media as $medium)
		{
			$all &= $medium[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[btn btn-warning'.$medium['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$mediumcheck = false;
			else
			$mediumcheck = true;

			if ($mediumcheck)
			{
				$wheremediumina[] = $medium[id];
				$addparam .= "medium$medium[id]=1&";
			}
		}
		if ($showcodec)
		foreach ($codecs as $codec)
		{
			$all &= $codec[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[cod'.$codec['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$codeccheck = false;
			else
			$codeccheck = true;

			if ($codeccheck)
			{
				$wherecodecina[] = $codec[id];
				$addparam .= "codec$codec[id]=1&";
			}
		}
		if ($showstandard)
		foreach ($standards as $standard)
		{
			$all &= $standard[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[sta'.$standard['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$standardcheck = false;
			else
			$standardcheck = true;

			if ($standardcheck)
			{
				$wherestandardina[] = $standard[id];
				$addparam .= "standard$standard[id]=1&";
			}
		}
		if ($showprocessing)
		foreach ($processings as $processing)
		{
			$all &= $processing[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[pro'.$processing['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$processingcheck = false;
			else
			$processingcheck = true;

			if ($processingcheck)
			{
				$whereprocessingina[] = $processing[id];
				$addparam .= "processing$processing[id]=1&";
			}
		}
		if ($showteam)
		foreach ($teams as $team)
		{
			$all &= $team[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[tea'.$team['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$teamcheck = false;
			else
			$teamcheck = true;

			if ($teamcheck)
			{
				$whereteamina[] = $team[id];
				$addparam .= "team$team[id]=1&";
			}
		}
		if ($showaudiocodec)
		foreach ($audiocodecs as $audiocodec)
		{
			$all &= $audiocodec[id];
			$mystring = $CURUSER['notifs'];
			$findme  = '[aud'.$audiocodec['id'].']';
			$search = strpos($mystring, $findme);
			if ($search === false)
			$audiocodeccheck = false;
			else
			$audiocodeccheck = true;

			if ($audiocodeccheck)
			{
				$whereaudiocodecina[] = $audiocodec[id];
				$addparam .= "audiocodec$audiocodec[id]=1&";
			}
		}
		}	
	}
	// when one clicked the cat, source, etc. name/image
	elseif ($category_get)
	{
		int_check($category_get,true,true,true);
		$wherecatina[] = $category_get;
		$addparam .= "cat=$category_get&";
	}elseif ($cate2_get){
		int_check($cate2_get, true, true, true);
		$wherecat2id[] = $cate2_get;
		$addparam .= "cate2id=$cate2_get&";
    }
	elseif ($medium_get)
	{
		int_check($medium_get,true,true,true);
		$wheremediumina[] = $medium_get;
		$addparam .= "medium=$medium_get&";
	}
	elseif ($source_get)
	{
		int_check($source_get,true,true,true);
		$wheresourceina[] = $source_get;
		$addparam .= "source=$source_get&";
	}
	elseif ($codec_get)
	{
		int_check($codec_get,true,true,true);
		$wherecodecina[] = $codec_get;
		$addparam .= "codec=$codec_get&";
	}
	elseif ($standard_get)
	{
		int_check($standard_get,true,true,true);
		$wherestandardina[] = $standard_get;
		$addparam .= "standard=$standard_get&";
	}
	elseif ($processing_get)
	{
		int_check($processing_get,true,true,true);
		$whereprocessingina[] = $processing_get;
		$addparam .= "processing=$processing_get&";
	}
	elseif ($team_get)
	{
		int_check($team_get,true,true,true);
		$whereteamina[] = $team_get;
		$addparam .= "team=$team_get&";
	}
	elseif ($audiocodec_get)
	{
		int_check($audiocodec_get,true,true,true);
		$whereaudiocodecina[] = $audiocodec_get;
		$addparam .= "audiocodec=$audiocodec_get&";
	}
	else	//select and go
	{
		$all = True;
		foreach ($cats as $cat)
		{
			$all &= $_GET["cat$cat[id]"];
			if ($_GET["cat$cat[id]"])
			{
				$wherecatina[] = $cat[id];
				$addparam .= "cat$cat[id]=1&";
			}
		}
		if ($showsubcat){
		if ($showsource)
		foreach ($sources as $source)
		{
			$all &= $_GET["source$source[id]"];
			if ($_GET["source$source[id]"])
			{
				$wheresourceina[] = $source[id];
				$addparam .= "source$source[id]=1&";
			}
		}
		if ($showmedium)
		foreach ($media as $medium)
		{
			$all &= $_GET["medium$medium[id]"];
			if ($_GET["medium$medium[id]"])
			{
				$wheremediumina[] = $medium[id];
				$addparam .= "medium$medium[id]=1&";
			}
		}
		if ($showcodec)
		foreach ($codecs as $codec)
		{
			$all &= $_GET["codec$codec[id]"];
			if ($_GET["codec$codec[id]"])
			{
				$wherecodecina[] = $codec[id];
				$addparam .= "codec$codec[id]=1&";
			}
		}
		if ($showstandard)
		foreach ($standards as $standard)
		{
			$all &= $_GET["standard$standard[id]"];
			if ($_GET["standard$standard[id]"])
			{
				$wherestandardina[] = $standard[id];
				$addparam .= "standard$standard[id]=1&";
			}
		}
		if ($showprocessing)
		foreach ($processings as $processing)
		{
			$all &= $_GET["processing$processing[id]"];
			if ($_GET["processing$processing[id]"])
			{
				$whereprocessingina[] = $processing[id];
				$addparam .= "processing$processing[id]=1&";
			}
		}
		if ($showteam)
		foreach ($teams as $team)
		{
			$all &= $_GET["team$team[id]"];
			if ($_GET["team$team[id]"])
			{
				$whereteamina[] = $team[id];
				$addparam .= "team$team[id]=1&";
			}
		}
		if ($showaudiocodec)
		foreach ($audiocodecs as $audiocodec)
		{
			$all &= $_GET["audiocodec$audiocodec[id]"];
			if ($_GET["audiocodec$audiocodec[id]"])
			{
				$whereaudiocodecina[] = $audiocodec[id];
				$addparam .= "audiocodec$audiocodec[id]=1&";
			}
		}
		}
	}
}

if ($all)
{
	//stderr("in if all","");
	$wherecatina = array();
	$wherecat2id =array();
	if ($showsubcat){
	$wheresourceina = array();
	$wheremediumina = array();
	$wherecodecina = array();
	$wherestandardina = array();
	$whereprocessingina = array();
	$whereteamina = array();
	$whereaudiocodecina = array();}
	$addparam .= "";
}
//stderr("", count($wherecatina)."-". count($wheresourceina));

if (count($wherecatina) > 1)
$wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
$wherea[] = "category = $wherecatina[0]";

if ($wherecat2id){
	$wherea[] = "secondcate = $wherecat2id[0]";
}

if ($showsubcat){
if ($showsource){
if (count($wheresourceina) > 1)
$wheresourcein = implode(",",$wheresourceina);
elseif (count($wheresourceina) == 1)
$wherea[] = "source = $wheresourceina[0]";}

if ($showmedium){
if (count($wheremediumina) > 1)
$wheremediumin = implode(",",$wheremediumina);
elseif (count($wheremediumina) == 1)
$wherea[] = "medium = $wheremediumina[0]";}

if ($showcodec){
if (count($wherecodecina) > 1)
$wherecodecin = implode(",",$wherecodecina);
elseif (count($wherecodecina) == 1)
$wherea[] = "codec = $wherecodecina[0]";}

if ($showstandard){
if (count($wherestandardina) > 1)
$wherestandardin = implode(",",$wherestandardina);
elseif (count($wherestandardina) == 1)
$wherea[] = "standard = $wherestandardina[0]";}

if ($showprocessing){
if (count($whereprocessingina) > 1)
$whereprocessingin = implode(",",$whereprocessingina);
elseif (count($whereprocessingina) == 1)
$wherea[] = "processing = $whereprocessingina[0]";}
}
if ($showteam){
if (count($whereteamina) > 1)
$whereteamin = implode(",",$whereteamina);
elseif (count($whereteamina) == 1)
$wherea[] = "team = $whereteamina[0]";}

if ($showaudiocodec){
if (count($whereaudiocodecina) > 1)
$whereaudiocodecin = implode(",",$whereaudiocodecina);
elseif (count($whereaudiocodecina) == 1)
$wherea[] = "audiocodec = $whereaudiocodecina[0]";}

$wherebase = $wherea;

if (isset($searchstr))
{
	if (!$_GET['notnewword']){
		insert_suggest($searchstr, $CURUSER['id']);
		$notnewword="";
	}
	else{
		$notnewword="notnewword=1&";
	}
	$search_mode = 0 + $_GET["search_mode"];
	if (!in_array($search_mode,array(0,1,2)))
	{
		$search_mode = 0;
		write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking search_mode field in" . $_SERVER['SCRIPT_NAME'], 'mod');
	}

	$search_area = 0 + $_GET["search_area"];

	if ($search_area == 4) {
		$searchstr = (int)parse_imdb_id($searchstr);
	}
	$like_expression_array =array();
	unset($like_expression_array);

	switch ($search_mode)
	{
		case 0:	// AND, OR
		case 1	:
			{
				$searchstr = str_replace(".", " ", $searchstr);
				$searchstr_exploded = explode(" ", $searchstr);
				$searchstr_exploded_count= 0;
				foreach ($searchstr_exploded as $searchstr_element)
				{
					$searchstr_element = trim($searchstr_element);	// furthur trim to ensure that multi space seperated words still work
					$searchstr_exploded_count++;
					if ($searchstr_exploded_count > 10)	// maximum 10 keywords
					break;
					$like_expression_array[] = " LIKE '%" . $searchstr_element. "%'";
				}
				break;
			}
		case 2	:	// exact
		{
			$like_expression_array[] = " LIKE '%" . $searchstr. "%'";
			break;
		}
		/*case 3 :	// parsed
		{
		$like_expression_array[] = $searchstr;
		break;
		}*/
	}
	$ANDOR = ($search_mode == 0 ? " AND " : " OR ");	// only affects mode 0 and mode 1

	switch ($search_area)
	{
		case 0   :	// torrent name
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element = "(torrents.name" . $like_expression_array_element." OR torrents.small_descr". $like_expression_array_element.")";
			$wherea[] =  implode($ANDOR, $like_expression_array);
			break;
		}
		case 1	:	// torrent description
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element = "torrents.descr". $like_expression_array_element;
			$wherea[] =  implode($ANDOR,  $like_expression_array);
			break;
		}
		/*case 2	:	// torrent small description
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element =  "torrents.small_descr". $like_expression_array_element;
			$wherea[] =  implode($ANDOR, $like_expression_array);
			break;
		}*/
		case 3	:	// torrent uploader
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element =  "users.username". $like_expression_array_element;

			if(!isset($CURUSER))	// not registered user, only show not anonymous torrents
			{
				$wherea[] =  implode($ANDOR, $like_expression_array) . " AND torrents.anonymous = 'no'";
			}
			else
			{
				if(get_user_class() > $torrentmanage_class)	// moderator or above, show all
				{
					$wherea[] =  implode($ANDOR, $like_expression_array);
				}
				else // only show normal torrents and anonymous torrents from hiself
				{
					$wherea[] =   "(" . implode($ANDOR, $like_expression_array) . " AND torrents.anonymous = 'no') OR (" . implode($ANDOR, $like_expression_array). " AND torrents.anonymous = 'yes' AND users.id=" . $CURUSER["id"] . ") ";
				}
			}
			break;
		}
		case 4  :  //imdb url
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element = "torrents.url". $like_expression_array_element;
			$wherea[] =  implode($ANDOR,  $like_expression_array);
			break;
		default :	// unkonwn
		{
			$search_area = 0;
			$wherea[] =  "torrents.name LIKE '%" . $searchstr . "%'";
			write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking search_area field in" . $_SERVER['SCRIPT_NAME'], 'mod');
			break;
		}
	}
	$addparam .= "search_area=" . $search_area . "&";
	$addparam .= "search=" . rawurlencode($searchstr) . "&".$notnewword;
	$addparam .= "search_mode=".$search_mode."&";
}

$where = implode(" AND ", $wherea);

if ($wherecatin)
$where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
if ($wherecat2id){
$where .= ($where ? " AND " : "") . "secondcate IN(" . $wherecat2id[0] . ")";
}
if ($showsubcat){
if ($wheresourcein)
$where .= ($where ? " AND " : "") . "source IN(" . $wheresourcein . ")";
if ($wheremediumin)
$where .= ($where ? " AND " : "") . "medium IN(" . $wheremediumin . ")";
if ($wherecodecin)
$where .= ($where ? " AND " : "") . "codec IN(" . $wherecodecin . ")";
if ($wherestandardin)
$where .= ($where ? " AND " : "") . "standard IN(" . $wherestandardin . ")";
if ($whereprocessingin)
$where .= ($where ? " AND " : "") . "processing IN(" . $whereprocessingin . ")";
if ($whereteamin)
$where .= ($where ? " AND " : "") . "team IN(" . $whereteamin . ")";
if ($whereaudiocodecin)
$where .= ($where ? " AND " : "") . "audiocodec IN(" . $whereaudiocodecin . ")";
}


if ($allsec == 1 || $enablespecial != 'yes')
{
	if ($where != "")
		$where = "WHERE $where ";
	else $where = "";
	$sql = "SELECT COUNT(*) FROM torrents " . ($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "") . $where;
}
else
{
	if ($where != "")
		$where = "WHERE $where AND categories.mode = '$sectiontype'";
	else $where = "WHERE categories.mode = '$sectiontype'";
	$sql = "SELECT COUNT(*), categories.mode FROM torrents LEFT JOIN categories ON category = categories.id " . ($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "") . $where." GROUP BY categories.mode";
}

$res = sql_query($sql) or die(mysql_error());
$count = 0;
while($row = mysql_fetch_array($res))
	$count += $row[0];

if ($CURUSER["torrentsperpage"])
$torrentsperpage = (int)$CURUSER["torrentsperpage"];
elseif ($torrentsperpage_main)
	$torrentsperpage = $torrentsperpage_main;
else $torrentsperpage = 50;

if ($count)
{
	if ($addparam != "")
	{
		if ($pagerlink != "")
		{
			if ($addparam{strlen($addparam)-1} != ";")
			{ // & = &amp;
				$addparam = $addparam . "&" . $pagerlink;
			}
			else
			{
				$addparam = $addparam . $pagerlink;
			}
		}
	}
	else
	{
		//stderr("in else","");
		$addparam = $pagerlink;
	}
	//stderr("addparam",$addparam);
	//echo $addparam;

	list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "?" . $addparam);
if ($allsec == 1 || $enablespecial != 'yes'){
	$query = "SELECT torrents.id, torrents.sp_state, torrents.promotion_time_type, torrents.promotion_until, torrents.banned, torrents.picktype, torrents.pos_state, torrents.category, torrents.source, torrents.medium, torrents.codec, torrents.standard, torrents.processing, torrents.team, torrents.audiocodec, torrents.leechers, torrents.seeders, torrents.name, torrents.small_descr, torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.anonymous,torrents.owner,torrents.url,torrents.cache_stamp,torrents.recommend,torrents.conservation FROM torrents ".($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "")." $where $orderby $limit";
}
else{
	$query = "SELECT torrents.id, torrents.sp_state, torrents.promotion_time_type, torrents.promotion_until, torrents.banned, torrents.picktype, torrents.pos_state, torrents.category, torrents.source, torrents.medium, torrents.codec, torrents.standard, torrents.processing, torrents.team, torrents.audiocodec, torrents.leechers, torrents.seeders, torrents.name, torrents.small_descr, torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.anonymous,torrents.owner,torrents.url,torrents.cache_stamp,torrents.recommend,torrents.conservation FROM torrents ".($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "")." LEFT JOIN categories ON torrents.category=categories.id $where $orderby $limit";
}

	$res = sql_query($query) or die(mysql_error());
}
else
	unset($res);
if (isset($searchstr))
	stdhead($lang_torrents['head_search_results_for'].$searchstr_ori);
elseif ($sectiontype == $browsecatmode)
	stdhead($lang_torrents['head_torrents']);
else stdhead($lang_torrents['head_music']);
print("<table width=\"97%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"embedded\">");


	$secs = 3 * 24 * 60 * 60;
	$dt = sqlesc(date("Y-m-d H:i:s", (TIMENOW - $secs)));
	$dt2 = sqlesc(date("Y-m-d H:i:s", (TIMENOW - $secs * 2)));
	sql_query("DELETE FROM suggest WHERE adddate <" . $dt2) or sqlerr();
	$searchres = sql_query("SELECT keywords, COUNT(DISTINCT userid) as count FROM suggest WHERE adddate >" . $dt . " GROUP BY keywords ORDER BY count DESC LIMIT 15") or sqlerr();
	$hotcount = 0;
	$hotsearch = "";
	while ($searchrow = mysql_fetch_assoc($searchres)) {
		$hotsearch .= "<li><a href=\"" . htmlspecialchars("?search=" . rawurlencode($searchrow["keywords"]) . "&notnewword=1") . "\">" . $searchrow["keywords"] . "</a></li>";
		$hotcount += mb_strlen($searchrow["keywords"], "UTF-8");
		if ($hotcount > 60)
			break;
	}


if ($allsec != 1 || $enablespecial != 'yes'){ //do not print searchbox if showing bookmarked torrents from all sections;
?>
	<style>
		td{
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 15px;
		}
	</style>

    <div class='row'>
    <div class='col-md-12 col-lg-12 col-xs-12 col-sm-12'>
    <div class='portlet light form-fit bordered'>
    <div class='portlet-title'>
        <div class='caption'>
            <h2>
                <i class='glyphicon glyphicon-search font-green-jungle'></i>
                <span class='caption-subject font-green-jungle sbold uppercase'>搜索 </span>
            </h2>
        </div>
    </div>
    <div class='portlet-body form'>
    <form class='form-horizontal form-bordered' method=get name='searchbox' action=?>
        <div class='form-body'>
            <div class='form-group'>
                <label class='control-label col-md-3'>搜索 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                        <span class='input-group-addon'><span class='glyphicon glyphicon-search'></span></span>
                        <input id="searchinput" class="form-control " name="search" type="text" value="<?php echo $searchstr_ori ?>" autocomplete="off"/>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-md-3'>种子筛选 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                        <select class="form-control" name="incldead">
                            <option value="0"><?php echo $lang_torrents['select_including_dead'] ?></option>
                            <option value="1"<?php print($include_dead == 1 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_active'] ?> </option>
                            <option value="2"<?php print($include_dead == 2 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_dead'] ?></option>
                            <option value="3"<?php print($include_dead == 3 ? " selected=\"selected\"" : ""); ?>>保种</option>
                            <option value="4"<?php print($include_dead == 4 ? " selected=\"selected\"" : ""); ?>>推荐</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-md-3'>促销种子 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                        <select class="form-control" name="spstate">
                            <option value="0"><?php echo $lang_torrents['select_all'] ?></option>
		                    <?php echo promotion_selection($special_state, 0) ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-md-3'>热搜 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                        <ul class="pagination">
                           <?php
                                if ($hotsearch){
                                    echo $hotsearch;
                                }
                           ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-md-3'>分类 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                           <?php
                           function printcat($name, $listarray, $cbname, $wherelistina, $btname, $showimg = false)
                           {
	                           global $catpadding, $catsperrow, $lang_torrents, $CURUSER, $CURLANGDIR, $catimgurl;
	                           $i = 0;
	                           foreach ($listarray as $list) {

//		                           print(($showimg ? return_search_category_image($list[id], "?") : "<a class='catePic' id='cat$list[id]' title=\"" . $list[name] . "\" href=\"?" . $cbname . "=" . $list[id] . "\">" . $list[name] . "</a>"));
		                           print(return_search_category_image($list[id], "?")."<a class='catePic' id='cat$list[id]' title=\"" . $list[name] . "\" href=\"?" . $cbname . "=" . $list[id] . "\"></a>");
		                           $i++;

//		                           echo "
////		                           <script>
////		                            $('#cate$list[id]').hover(
////		                                function() {
//////   		                                    alert($('#cate$list[id]').attr('id'));
////   		                                    var id=$list[id];
////                                            $.post('secondcategory.php',
////                                                {
////                                                    cate_id:id
////                                                },
////                                                function (response,status,xhr) {
//////                                                 alert(response);
//////                                                $('#secondcategory').children().remove();
////                                                    var arr=response.split(',');
////                                                    var str='';
////                                                    for (var i=0;i<arr.length-1;i+=2){
////                                                        str+='<li><a href='+'torrents.php?cat2id='+arr[i]+'>'+arr[i+1]+'</a></li>';
////                                                    }
////                                                    $('#cat$list[id]').append(str);
////                                            });
////                                        },function() {
////                                            $('#cat$list[id]').children().remove();
////                                        });
////                                    </script>
//		                           ";
	                           }
                           }

                           printcat($lang_torrents['text_category'], $cats, "cat", $wherecatina, "cat_check", true);
                           ?>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-md-3'>二级分类 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#moive" data-toggle="tab">电影</a></li>
                            <li class=""><a href="#tvshow" data-toggle="tab">电视剧</a></li>
                            <li class=""><a href="#zongyi" data-toggle="tab">综艺</a></li>
                            <li class=""><a href="#edu" data-toggle="tab">教育</a></li>
                            <li class=""><a href="#commic" data-toggle="tab">动漫</a></li>
                            <li class=""><a href="#software" data-toggle="tab">软件</a></li>
                            <li class=""><a href="#sport" data-toggle="tab">体育</a></li>
                            <li class=""><a href="#music" data-toggle="tab">音乐</a></li>
                            <li class=""><a href="#jilu" data-toggle="tab">纪录片</a></li>
                            <li class=""><a href="#game" data-toggle="tab">游戏</a></li>
                            <li class=""><a href="#other" data-toggle="tab">其他</a></li>
                        </ul>

                        <div class="tab-content" style="padding: 5px">
                            <div class="tab-pane active" id="moive">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4011">华语</a></li>
                                    <li><a href="torrents.php?cate2id=4012">欧美</a></li>
                                    <li><a href="torrents.php?cate2id=4013">日韩</a></li>
                                    <li><a href="torrents.php?cate2id=4014">合集</a></li>
                                    <li><a href="torrents.php?cate2id=4015">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="tvshow">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4021">大陆</a></li>
                                    <li><a href="torrents.php?cate2id=4022">港台</a></li>
                                    <li><a href="torrents.php?cate2id=4023">美剧</a></li>
                                    <li><a href="torrents.php?cate2id=4024">英剧</a></li>
                                    <li><a href="torrents.php?cate2id=4025">日韩</a></li>
                                    <li><a href="torrents.php?cate2id=4026">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="zongyi">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4031">娱乐</a></li>
                                    <li><a href="torrents.php?cate2id=4032">综合</a></li>
                                    <li><a href="torrents.php?cate2id=4033">晚会</a></li>
                                    <li><a href="torrents.php?cate2id=4034">典礼</a></li>
                                    <li><a href="torrents.php?cate2id=4035">科教</a></li>
                                    <li><a href="torrents.php?cate2id=4036">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="edu">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4041">考研</a></li>
                                    <li><a href="torrents.php?cate2id=4042">外语</a></li>
                                    <li><a href="torrents.php?cate2id=4043">学科专业</a></li>
                                    <li><a href="torrents.php?cate2id=4044">讲座</a></li>
                                    <li><a href="torrents.php?cate2id=4045">公开课</a></li>
                                    <li><a href="torrents.php?cate2id=4046">素材模板</a></li>
                                    <li><a href="torrents.php?cate2id=4047">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="commic">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4051">动画</a></li>
                                    <li><a href="torrents.php?cate2id=4052">漫画</a></li>
                                    <li><a href="torrents.php?cate2id=4053">音乐</a></li>
                                    <li><a href="torrents.php?cate2id=4054">周边</a></li>
                                    <li><a href="torrents.php?cate2id=4055">特摄</a></li>
                                    <li><a href="torrents.php?cate2id=4056">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="software">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4061">Windows</a></li>
                                    <li><a href="torrents.php?cate2id=4062">Linux</a></li>
                                    <li><a href="torrents.php?cate2id=4063">MacOS</a></li>
                                    <li><a href="torrents.php?cate2id=4064">Android</a></li>
                                    <li><a href="torrents.php?cate2id=4065">iOS</a></li>
                                    <li><a href="torrents.php?cate2id=4066">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="sport">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4071">篮球</a></li>
                                    <li><a href="torrents.php?cate2id=4072">足球</a></li>
                                    <li><a href="torrents.php?cate2id=4073">排球</a></li>
                                    <li><a href="torrents.php?cate2id=4074">羽毛球</a></li>
                                    <li><a href="torrents.php?cate2id=4075">乒乓球</a></li>
                                    <li><a href="torrents.php?cate2id=4076">网球</a></li>
                                    <li><a href="torrents.php?cate2id=4077">F1</a></li>
                                    <li><a href="torrents.php?cate2id=4078">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="music">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4081">华语</a></li>
                                    <li><a href="torrents.php?cate2id=4082">欧美</a></li>
                                    <li><a href="torrents.php?cate2id=4083">日韩</a></li>
                                    <li><a href="torrents.php?cate2id=4084">乐器古典</a></li>
                                    <li><a href="torrents.php?cate2id=4085">演唱会</a></li>
                                    <li><a href="torrents.php?cate2id=4086">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="jilu">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4101">地理</a></li>
                                    <li><a href="torrents.php?cate2id=4102">探索</a></li>
                                    <li><a href="torrents.php?cate2id=4103">历史</a></li>
                                    <li><a href="torrents.php?cate2id=4104">CCTV</a></li>
                                    <li><a href="torrents.php?cate2id=4105">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="game">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4111">PC</a></li>
                                    <li><a href="torrents.php?cate2id=4112">主机</a></li>
                                    <li><a href="torrents.php?cate2id=4113">移动</a></li>
                                    <li><a href="torrents.php?cate2id=4114">视频</a></li>
                                    <li><a href="torrents.php?cate2id=4115">英雄联盟</a></li>
                                    <li><a href="torrents.php?cate2id=4116">其他</a></li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="other">
                                <ul class="pagination">
                                    <li><a href="torrents.php?cate2id=4091">其他</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-md-3'>检索 </label>
                <div class=col-md-9>
                    <div class='input-group'>
                        <input type=submit class='btn btn-success' value='开始检索'>
                    </div>
                </div>
            </div>
    </form>
    </div>
    </div>
    </div>
    </div>
</div>




<?php
}
if($inclbookmarked == 1)
{
	print("<h1 align=\"center\">" . get_username($CURUSER['id']) . $lang_torrents['text_s_bookmarked_torrent'] . "</h1>");
}
elseif($inclbookmarked == 2)
{
	print("<h1 align=\"center\">" . get_username($CURUSER['id']) . $lang_torrents['text_s_not_bookmarked_torrent'] . "</h1>");
}

if ($count) {
	if ($sectiontype == $browsecatmode)
		torrenttable($res, "torrents");
	elseif ($sectiontype == $specialcatmode)
		torrenttable($res, "music");
	else torrenttable($res, "bookmarks");
	print($pagerbottom);
}
else {
	if (isset($searchstr)) {
		print("<br />");
		stdmsg($lang_torrents['std_search_results_for'] . $searchstr_ori . "\"",$lang_torrents['std_try_again']);
	}
	else {
		stdmsg($lang_torrents['std_nothing_found'],$lang_torrents['std_no_active_torrents']);
	}
}
if ($CURUSER){
	if ($sectiontype == $browsecatmode)
		$USERUPDATESET[] = "last_browse = ".TIMENOW;
	else	$USERUPDATESET[] = "last_music = ".TIMENOW;
}
print("</td></tr></table>");
stdfoot();
