<?php
require_once("include/bittorrent.php");
dbconn(true);
require_once(get_langfile_path("torrents.php"));
loggedinorreturn();
parked();

if ($showextinfo['imdb'] == 'yes')
	require_once ("imdb/imdb.class.php");
//check searchbox
function printcatnocheck($name, $listarray, $cbname, $wherelistina, $btname, $showimg = false)
{
	global $catpadding,$catsperrow,$lang_torrents,$CURUSER,$CURLANGDIR,$catimgurl;

	print("<tr>");
	$i = 0;
	foreach($listarray as $list){
		if ($i && $i % $catsperrow == 0){
			print("</tr><tr>");
		}
	print("<td align=\"center\" class=\"bottom\" width=\"60\" style=\"padding-bottom: 4px; padding-left: ".$catpadding."px;\">".($showimg ? return_category_image($list[id], "?") : "<a title=\"" .$list[name] . "\" href=\"?".$cbname."=".$list[id]."\">".$list[name]."</a>")."</td>\n");
		
		$i++;
	}
	print("</tr>");
	}
function printcat($name, $listarray, $cbname, $wherelistina, $btname, $showimg = false)
{
	global $catpadding,$catsperrow,$lang_torrents,$CURUSER,$CURLANGDIR,$catimgurl;
	print("<tr><td class=\"embedded\" colspan=\"".$catsperrow."\" align=\"left\"><b>".$name."</b></td></tr><tr>");
	$i = 0;
	foreach($listarray as $list){
		if ($i && $i % $catsperrow == 0){
			print("</tr><tr>");
		}
	print("<td align=\"left\" class=\"bottom\" style=\"white-space: nowrap;padding-bottom: 4px; padding-left:".$catpadding."px;\"><input type=\"checkbox\" id=\"".$cbname.$list[id]."\" name=\"".$cbname.$list[id]."\"" . (in_array($list[id],$wherelistina) ? " checked=\"checked\"" : "") . " value=\"1\" />".($showimg ? return_category_image($list[id], "?") : "<a title=\"" .$list[name] . "\" href=\"?".$cbname."=".$list[id]."\">".$list[name]."</a>")."</td>\n");
		
		$i++;
	}
	$checker = "<input name=\"".$btname."\" value='" .  $lang_torrents['input_check_all'] . "' class=\"btn medium\" type=\"button\" onclick=\"javascript:SetChecked('".$cbname."','".$btname."','". $lang_torrents['input_check_all'] ."','" . $lang_torrents['input_uncheck_all'] . "',-1,10)\" />";
	print("<td colspan=\"2\" class=\"bottom\" align=\"left\" style=\"padding-left: 15px\">".$checker."</td>\n");
	print("</tr>");
}
function printcatone($nameid, $listarray, $cbname)
{
	global $catpadding,$catsperrow,$lang_torrents,$CURUSER,$CURLANGDIR,$catimgurl;

	//print("<tr><td class=\"embedded\" colspan=\"".$catsperrow."\" align=\"left\"><b>".$name."</b></td></tr><tr bgcolor=#00AA55>");
	print("<h2></h2><table><tr>");
	$i = 0;
	foreach($listarray as $list){
			if ($i && $i % $catsperrow == 0){
					print("</tr><tr>");
			}
			print("<td align=\"center\" class=\"bottom\" style=\"padding-bottom: 4px; padding-left: ".$catpadding."px;\">". "&nbsp<a title=\"" .$list[name] . "\" href=\"?".$cbname."=".$list[id]."\" ".($nameid==$list[id]?"class=\"faqlink\"":"")."><b>".$list[name]."</b></a>&nbsp</td>\n");
			$i++;
	}
	print("</tr></table>");
}
						
$sectiontype = $browsecatmode;
$sourcelid = (int)$_GET['cat'];
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
	if ($showaudiocodec) $audiocodecs = searchbox_item_list("audiocodecs",$sourcelid);
}

$searchstr_ori = htmlspecialchars(trim($_GET["search"]));
$searchstr = mysql_real_escape_string(trim($_GET["search"]));
if (empty($searchstr))
	unset($searchstr);


//print(join(",", $buyid));
$wherea = array();

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
		case '10': $column = "times_uncompleted"; break;
		case '11': $column = "rating";$wherea[]="rating > 0"; break;
		default: $column = "id"; break;
	}

	switch($_GET['type']) {
		case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
		case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
		default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
	}

	

	
	if($column == "owner")
	{
		$orderby = "pos_state DESC, havenoseed DESC, torrents.anonymous, users.username " . $ascdesc;
	}
	else
	{
		$orderby = "pos_state DESC, havenoseed DESC, torrents." . $column . " " . $ascdesc;
	}

	$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";

} else {

	$orderby = "pos_state DESC, havenoseed DESC , torrents.id DESC";
	$orderbyinclbookmarked = "pos_state DESC, havenoseed DESC, bookmarks.time DESC";
	$pagerlink = "";

}

 


$addparam = "";

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
if ($_GET["inclbookmarked"])
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
	//$wherea[] = "bookmarks.time is not null";
	$wherea[] = "torrents.id IN (SELECT torrentid FROM bookmarks WHERE userid=" . $CURUSER['id'] . ")";
}
elseif ($inclbookmarked == 2)		//not bookmarked
{
	$addparam .= "inclbookmarked=2&";
	if(isset($CURUSER))
	//$wherea[] = " bookmarks.time is null ";
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

if (!in_array($include_dead,array(0,1,2,3))&&(get_user_class() < $seebanned_class)||(get_user_class() >= $seebanned_class)&&!in_array($include_dead,array(0,1,2,3,4)))
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
}
elseif ($include_dead == 3)		//dead
{
	$addparam .= "incldead=3&";
	$wherea[] = "url = ''";
}
elseif ($include_dead == 4)		//dead
{
	$addparam .= "incldead=4&";
	$wherea[] = "banned = 'yes'";
}
// ----------------- end include dead ---------------------//
// ----------------- start picktype  ---------------------//
if (isset($_GET["picktype"]))


{

$picktype = 0 + $_GET["picktype"];



if (!in_array($picktype,array(0,1,2,3,4)))
{
	write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking picktype field in" . $_SERVER['SCRIPT_NAME'], 'mod');
}
elseif ($picktype == 1)  //all(active,dead)
{
	$addparam .= "picktype=1&";
	$wherea[] = "picktype = 'hot'";
}
elseif ($picktype == 2)		//active
{
	$addparam .= "picktype=2&";
	$wherea[] = "picktype = 'classic'";
}
elseif ($picktype == 3)		//dead
{
	$addparam .= "picktype=3&";
	$wherea[] = "picktype = 'recommended'";
}
elseif ($picktype == 4)		//dead
{
	$addparam .= "picktype=4&";
	$wherea[] = "picktype = 'normal'";
}

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
			elseif (strpos($CURUSER['notifs'], "[spstate=8]") !== false)
		$special_state = 8;
}
else $special_state = 0;

if (!in_array($special_state,array(0,1,2,3,4,5,6,7,8)))
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
		$wherea[] = "(sp_state = 2 or sp_state = 4)";
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
		$wherea[] = "(sp_state = 3 or sp_state = 4 or sp_state = 6)";
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
		$wherea[] = "(sp_state = 5 or sp_state = 6)";
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
elseif ($special_state == 8)	//
{
	$addparam .= "spstate=8&";

	if(get_global_sp_state() == 1)	//only sp state
	{
		$wherea[] = "sp_state !=1";
	}
	else if(get_global_sp_state() != 1)	//all
	{
		;
	}
}

$category_get = 0 + $_GET["cat"];
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
			$findme  = '[med'.$medium['id'].']';
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
	elseif ($category_get&&$audiocodec_get==0)
	{
		int_check($category_get,true,true,true);
		$wherecatina[] = $category_get;
		$addparam .= "cat=$category_get&";
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

if (isset($searchstr)||$_GET["search_area"]==7)
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

	/*if ($search_area == 4) {
		$searchstr = (int)parse_imdb_id($searchstr);
	}*/
	$like_expression_array =array();
	unset($like_expression_array);

	switch ($search_mode)
	{
		case 0:	// AND, OR
		case 1	:
			{
				//$searchstr = str_replace(".", " ", $searchstr);
				$searchstr_exploded = explode(" ", $searchstr);
				$searchstr_exploded_count= 0;
				foreach ($searchstr_exploded as $searchstr_element)
				{	
				
				if ($search_area == 5)$searchstr_element = (int)parse_imdb_id($searchstr_element);
					$searchstr_element = trim($searchstr_element);	// furthur trim to ensure that multi space seperated words still work
					if ($searchstr_exploded_count > 10)	// maximum 11 keywords
					break;
					if(!$searchstr_element)continue;
					$searchstr_exploded_count++;
					
					if(preg_match("/^!(.*)/",$searchstr_element, $matches))		
					$like_expression_array[] = " NOT LIKE '%" . $matches[1]. "%'";
					ELSE $like_expression_array[] = " LIKE '%" . $searchstr_element. "%'";
				}
				if($searchstr_exploded_count==0)$like_expression_array[] = " LIKE '%'";
				break;
			}
		case 2	:	// exact
		{	if ($search_area == 5)$searchstr =(int)parse_imdb_id($searchstr);

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
			if(preg_match( '/NOT LIKE/i',$like_expression_array_element))
			$like_expression_array_element = "(torrents.name" . $like_expression_array_element." AND torrents.small_descr". $like_expression_array_element.")";
			else
			$like_expression_array_element = "(torrents.name" . $like_expression_array_element." OR torrents.small_descr". $like_expression_array_element.")";
			
			
			$wherea[]="(".implode($ANDOR, $like_expression_array).")";
			
			break;
		}
		/*case 1	:	// torrent description
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element = "torrents.descr". $like_expression_array_element;
			$wherea[]="(".implode($ANDOR, $like_expression_array).")";
			break;
		}
		case 2	:	// torrent small description
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element =  "torrents.small_descr". $like_expression_array_element;
			$wherea[]="(".implode($ANDOR, $like_expression_array).")";
			break;
		}*/
		case 3	:	// torrent uploader
		{
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element =  "users.username". $like_expression_array_element;

			if(!isset($CURUSER))	// not registered user, only show not anonymous torrents
			{
				$wherea[] =  "((".implode($ANDOR, $like_expression_array) .")". " AND torrents.anonymous = 'no')";
			}
			else
			{
				if(get_user_class() > $torrentmanage_class)	// moderator or above, show all
				{
					$wherea[] = "(". implode($ANDOR, $like_expression_array).")";
				}
				else // only show normal torrents and anonymous torrents from hiself
				{
					$wherea[] =  "(". "( (" . implode($ANDOR, $like_expression_array) . ") AND torrents.anonymous = 'no') OR ( (" . implode($ANDOR, $like_expression_array). " ) AND torrents.anonymous = 'yes' AND users.id=" . $CURUSER["id"] . ") ".")";
				}
			}
			break;
		}
		case 4  :  //imdb url
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element = "imdbinfo.info". $like_expression_array_element;
			//$wherea =  "select imdb from imdbinfo where (".implode($ANDOR,  $like_expression_array).")";
			$wherea[] =  "torrents.url in (select imdb from imdbinfo where (".implode($ANDOR,  $like_expression_array)."))";
			break;
		case 5  :  //imdb url
			foreach ($like_expression_array as &$like_expression_array_element)
			$like_expression_array_element = "torrents.url". $like_expression_array_element;
			if($like_expression_array)$wherea[] =  "(".implode($ANDOR,  $like_expression_array).")";
			break;
		case 6  :  //hash
			$wherea[]="torrents.info_hash like UNHEX('".($searchstr)."')";
			$search_mode=2;
			$_GET["search_mode"] = 2;
			break;
		case 7  :  //peers
			$wherea[]="torrents.name like '%" . $searchstr . "%'";
			$wherea[]="(torrents.id in  (SELECT DISTINCT torrent FROM peers WHERE  userid =" . $CURUSER['id'] . ") or torrents.id in  (SELECT DISTINCT torrentid FROM snatched WHERE finished='yes' and  userid =" . $CURUSER['id'] . ") )";
			$search_mode=2;
			$_GET["search_mode"] = 2;
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

/*if ($where != ""){
		if($searchstr)$buywhere="WHERE $where";
		else $buywhere="WHERE $where AND categories.mode = '$sectiontype'";
		}
else $buywhere="WHERE 1 ";*/

if ($allsec == 1 || $enablespecial != 'yes')
{
	if ($where != "")
		$where = "WHERE $where ";
	else $where = "";
	$sql = "SELECT COUNT(*) FROM torrents   " . ($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "") . $where;
	
	
}
else
{
	if ($where != "")
		{
		//if($searchstr)$where = "WHERE $where";
		//else 
		$where = "WHERE $where AND categories.mode = '$sectiontype'";
		
		}
	else $where = "WHERE categories.mode = '$sectiontype'";
	$sql = "SELECT COUNT(*), categories.mode FROM torrents LEFT JOIN categories ON category = categories.id " . ($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "") . $where." GROUP BY categories.mode";
}




if (!$count = $Cache->get_value('torrent_count_'.hash('md5',$sql))){
$res = sql_query($sql) or die(mysql_error());
$count = 0;
while($row = mysql_fetch_array($res))
	$count += $row[0];
$Cache->cache_value('torrent_count_'.hash('md5',$sql),$count,60);
}	
	

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

	
	
	
/*$buyid[]=1;	
$buyres = sql_query("SELECT  torrents.id  from buysp LEFT JOIN torrents  ON buysp.torrent=torrents.id ".($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "")."LEFT JOIN categories ON category = categories.id $buywhere  and seeders > 0  and buysp.until > NOW() and pos_state ='normal'  and torrents.id>0 ORDER BY buysp.money DESC LIMIT 5") ;
while ($row = mysql_fetch_assoc($buyres))
{$buyid[] = $row["id"];$resbuy[$row["id"]]='buy';}
$buyid=array_filter($buyid);
$orderby2="ORDER BY  CASE WHEN    torrents.id in (".join(",", $buyid).")  THEN pos_state  END desc ,";*/
/*
SELECT torrents.id, torrents.sp_state, torrents.promotion_time_type, torrents.promotion_until, torrents.banned, torrents.picktype, torrents.pos_state, torrents.category, torrents.source, torrents.medium, torrents.codec, torrents.standard, torrents.processing, torrents.team, torrents.audiocodec, torrents.leechers, torrents.seeders, torrents.name, torrents.small_descr, torrents.times_completed, torrents.size, torrents.added ,torrents.last_action,torrents.comments,torrents.anonymous,torrents.owner,torrents.url,torrents.urltype,torrents.cache_stamp FROM torrents LEFT JOIN categories ON torrents.category=categories.id WHERE visible = 'yes' AND categories.mode = '7' ORDER BY CASE WHEN torrents.id in (1) THEN pos_state END desc , case when (seeders <= 0 and pos_state = 'normal') then torrents.id end ,pos_state DESC , torrents.id DESC LIMIT 0,50*/

$orderby2="ORDER BY nobuymoney DESC , ";


	list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "?" . $addparam);
	
if ($allsec == 1 || $enablespecial != 'yes'){

if($inclbookmarked == 1&&isset($orderbyinclbookmarked)){		//bookmarked
$orderby2="ORDER BY ";
$query = "SELECT bookmarks.time,torrents.id, torrents.sp_state, torrents.promotion_time_type, torrents.promotion_until, torrents.banned, torrents.picktype, torrents.pos_state, torrents.category, torrents.source, torrents.medium, torrents.codec, torrents.standard, torrents.processing, torrents.team, torrents.audiocodec, torrents.leechers, torrents.seeders, torrents.name, torrents.small_descr, torrents.tags, torrents.times_completed, torrents.subcount,torrents.times_uncompleted, torrents.size, torrents.added,torrents.last_action,torrents.comments,torrents.anonymous,torrents.owner,torrents.url,torrents.urltype,torrents.cache_stamp,torrents.nobuymoney,torrents.visible,torrents.rating FROM torrents  LEFT JOIN (SELECT torrentid,time FROM bookmarks WHERE userid=" . $CURUSER['id'] . ") as bookmarks  ON torrents.id = bookmarks.torrentid  $where $orderby2 ".($orderbyinclbookmarked)."  $limit";
}else
	$query = "SELECT  torrents.id, torrents.sp_state, torrents.promotion_time_type, torrents.promotion_until, torrents.banned, torrents.picktype, torrents.pos_state, torrents.category, torrents.source, torrents.medium, torrents.codec, torrents.standard, torrents.processing, torrents.team, torrents.audiocodec, torrents.leechers, torrents.seeders, torrents.name, torrents.small_descr, torrents.tags, torrents.times_completed, torrents.subcount,torrents.times_uncompleted, torrents.size, torrents.added,torrents.last_action,torrents.comments,torrents.anonymous,torrents.owner,torrents.url,torrents.urltype,torrents.cache_stamp,torrents.nobuymoney,torrents.visible,torrents.rating FROM torrents ".($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "")." $where  $orderby2 $orderby  $limit";
}else{

	$query = "SELECT  torrents.id, torrents.sp_state, torrents.promotion_time_type, torrents.promotion_until, torrents.banned, torrents.picktype, torrents.pos_state, torrents.category, torrents.source, torrents.medium, torrents.codec, torrents.standard, torrents.processing, torrents.team, torrents.audiocodec, torrents.leechers, torrents.seeders, torrents.name, torrents.small_descr, torrents.tags, torrents.times_completed, torrents.subcount,torrents.times_uncompleted, torrents.size, torrents.added ,torrents.last_action,torrents.comments,torrents.anonymous,torrents.owner,torrents.url,torrents.urltype,torrents.cache_stamp ,torrents.nobuymoney,torrents.visible,torrents.rating FROM torrents  ".($search_area == 3 || $column == "owner" ? "LEFT JOIN users ON torrents.owner = users.id " : "")." LEFT JOIN categories ON torrents.category=categories.id $where $orderby2 $orderby $limit";
}
	
	

if (!$res = $Cache->get_value('torrent_query_'.hash('md5',$query))){
$ress = sql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($ress))
$res[] = $row;
$Cache->cache_value('torrent_query_'.hash('md5',$query),$res,15);
}

//$res = sql_query($query) or die(mysql_error());	
	
	

}
else
	unset($res);
	
if (isset($searchstr))
	stdhead($lang_torrents['head_search_results_for'].$searchstr_ori);
elseif ($sectiontype == $browsecatmode)
	stdhead($lang_torrents['head_torrents']);
else stdhead($lang_torrents['head_music']);


//print("<table width=\"940\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"embedded\">");
print("<table width=\"98%\" cellpadding=\"4\"  class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"  ><tr><td class=\"embedded\">");
if ($allsec != 1 || $enablespecial != 'yes'){ //do not print searchbox if showing bookmarked torrents from all sections;
?>
<form method="get" name="searchbox" action="<?echo $_SERVER['PHP_SELF']?>">
	<table border="1" class="searchbox" cellspacing="0" cellpadding="5" width="100%">
		<tbody>
		<tr>
		<td class="colhead" align="center" colspan="2"><a href="javascript: klappe_search('searchboxmain')"><img  class="plus" src="pic/trans.gif" id="picsearchboxmain" alt="Show/Hide" /><?php echo $lang_torrents['text_search_box'] ?><img  class="plus" src="pic/trans.gif" id="pic2searchboxmain" alt="Show/Hide" /></a></td>
		</tr></tbody>
		
		
				
				<tbody id="k2searchboxmain" <?php echo (isset($_GET["search_area"])?"style=\"display:none\"":"") ?> >
		<tr>
			<td class="rowfollow"  align="center" colspan="2" >
				<table>
					<?php
						
							//$checker = "<input name=\"".$btname."\" value='" .  $lang_torrents['input_check_all'] . "' class=\"btn medium\" type=\"button\" onclick=\"javascript:SetChecked('".$cbname."','".$btname."','". $lang_torrents['input_check_all'] ."','" . $lang_torrents['input_uncheck_all'] . "',-1,10)\" />";
							//print("<td colspan=\"2\" class=\"bottom\" align=\"left\" style=\"padding-left: 15px\">".$checker."</td>\n");
							//print("</tr>");
						
												
					printcatnocheck($lang_torrents['text_category'],$cats,"cat",$wherecatina,"cat_check",true);
					
		
					?>
					
				</table>
				<?php if(searchbox_item_list("audiocodecs",$sourcelid)&&$sourcelid)printcatone($audiocodec_get, searchbox_item_list("audiocodecs",$sourcelid),"cat=".$sourcelid."&audiocodec");?>
			</td>
			
						
			
		</tr>
		</tbody>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	<tbody id="ksearchboxmain" <?php echo (isset($_GET["search_area"])?"":"style=\"display:none\"") ?>>
		<tr>
			<td class="rowfollow" align="left">
				<table>
					<?php
					
					printcat($lang_torrents['text_category'],$cats,"cat",$wherecatina,"cat_check",true);

					if ($showsubcat){
					
						if ($showaudiocodec&&searchbox_item_list("audiocodecs",$sourcelid)&&$sourcelid)
							printcat("二级分类", searchbox_item_list("audiocodecs",$sourcelid), "audiocodec", $whereaudiocodecina, "audiocodec_check");
						
						if ($showsource)
							printcat($lang_torrents['text_source'], $sources, "source", $wheresourceina, "source_check");
						if ($showmedium)
							printcat($lang_torrents['text_medium'], $media, "medium", $wheremediumina, "medium_check");
						if ($showcodec)
							printcat($lang_torrents['text_codec'], $codecs, "codec", $wherecodecina, "codec_check");
						
						

						
						
						if ($showstandard)
							printcat($lang_torrents['text_standard'], $standards, "standard", $wherestandardina, "standard_check");
						if ($showprocessing)
							printcat($lang_torrents['text_processing'], $processings, "processing", $whereprocessingina, "processing_check");
						if ($showteam)
							printcat($lang_torrents['text_team'], $teams, "team", $whereteamina, "team_check");
					}
					?>
				</table>
			</td>
			
			<td class="rowfollow" valign="middle">
				<table>
					<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<font class="medium"><?php echo $lang_torrents['text_show_dead_active'] ?></font>
						</td>
				 	</tr>				
					<tr>
						<td class="bottom" type ="hide" style="padding: 1px;padding-left: 10px">
							<select class="med" name="incldead" style="width: 100px;" >
								<option value="0"><?php echo $lang_torrents['select_including_dead'] ?></option>
								<option value="1"<?php print($include_dead == 1 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_active'] ?> </option>
								<option value="2"<?php print($include_dead == 2 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_dead'] ?></option>
								<option value="3"<?php print($include_dead == 3 ? " selected=\"selected\"" : ""); ?>>无IMDB</option>
								<?php if (get_user_class() > $seebanned_class) {?>
								<option value="4"<?php print($include_dead == 4 ? " selected=\"selected\"" : ""); ?>>禁止种子</option>
								<?php } ?>
							</select>
						</td>
				 	</tr>
					
				 	<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<br />
						</td>
				 	</tr>
					<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<font class="medium"><?php echo $lang_torrents['text_show_special_torrents'] ?></font>
						</td>
				 	</tr>
				 	<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<select class="med" name="spstate" style="width: 100px;">
								<option value="0"><?php echo $lang_torrents['select_all'] ?></option>
<?php echo promotion_selectionsearch($special_state, 0)?>
							</select>
						</td>
					</tr>
					
					
					
					
					<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<br />
						</td>
				 	</tr>
					<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<font class="medium"><?php echo $lang_torrents['text_show_picktype_torrents'] ?></font>
						</td>
				 	</tr>
				 	<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<select class="med" name="picktype" style="width: 100px;">
								<option value="0"><?php echo $lang_torrents['select_all'] ?></option>
								<option value="1"<?php print($picktype == 1 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_hot'] ?></option>
								<option value="2"<?php print($picktype == 2 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_classic'] ?></option>
								<option value="3"<?php print($picktype == 3 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_recommended'] ?></option>
								<option value="4"<?php print($picktype == 4 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_normal'] ?></option>
							</select>
						</td>
					</tr>
									
					
				 	<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<br />
						</td>
					</tr>
					<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<font class="medium"><?php echo $lang_torrents['text_show_bookmarked'] ?></font>
						</td>
				 	</tr>
				 	<tr>
						<td class="bottom" style="padding: 1px;padding-left: 10px">
							<select class="med" name="inclbookmarked" style="width: 100px;">
								<option value="0"><?php echo $lang_torrents['select_all'] ?></option>
								<option value="1"<?php print($inclbookmarked == 1 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_bookmarked'] ?></option>
								<option value="2"<?php print($inclbookmarked == 2 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_bookmarked_exclude'] ?></option>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</tbody>
		
		
		
		
		
		

		
		
		
		
		
		<tbody>
		<tr>
			<td class="rowfollow" align="center">
				<table>
					<tr>
						<td class="embedded">
							<?php echo $lang_torrents['text_search'] ?>&nbsp;&nbsp;
						</td>
						<td class="embedded">
							<table>
								<tr>
									<td class="embedded">
										<input id="searchinput" name="search" type="text" value="<?php echo  $searchstr_ori ?>" autocomplete="off" style="width: 300px" ondblclick="suggest(event.keyCode,this.value);" onkeyup="suggest(event.keyCode,this.value);" onkeypress="return noenter(event.keyCode);" x-webkit-speech />
										<script src="javascript/suggest.js" type="text/javascript"></script>
										<div id="suggcontainer" style="text-align: left; width:300px;  display: none;">
											<div id="suggestions" style="width:300px; border: 1px solid rgb(119, 119, 119); cursor: default; position: absolute; color: rgb(0,0,0); background-color: rgb(255, 255, 255);"></div>
										</div>
									</td>
								</tr>
							</table>
						</td>
						<td class="embedded">
							<?php echo "&nbsp;" . $lang_torrents['text_in'] ?>

							<select name="search_area">
								<option value="0"><?php echo $lang_torrents['select_title'] ?></option>
								
								<?php
								/*
								//上移<option value="1"<?php print($_GET["search_area"] == 1 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_description'] ?></option>
								if ($smalldescription_main == 'yes'){
								?>
								<option value="2"<?php print($_GET["search_area"] == 2 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_small_description'] ?></option>
								<?php
								}
								//下移
								
								*/

								?>
								<option value="3"<?php print($_GET["search_area"] == 3 ? " selected=\"selected\"" : ""); ?>><?php echo $lang_torrents['select_uploader'] ?></option>
								<option value="4"<?php print($_GET["search_area"] == 4 ? " selected=\"selected\"" : ""); ?>>电影信息</option>
								<option value="5"<?php print($_GET["search_area"] == 5 ? " selected=\"selected\"" : ""); ?>>影片编号</option>
								<option value="6"<?php print($_GET["search_area"] == 6 ? " selected=\"selected\"" : ""); ?>>种子HASH</option>
								<option value="7"<?php print($_GET["search_area"] == 7 ? " selected=\"selected\"" : ""); ?>>活动种子</option>
								
							</select>


							
							
														
														
														<?php echo $lang_torrents['text_with'] ?>

							<select name="search_mode" style="width: 60px;">
								<option value="0"><?php echo $lang_torrents['select_and'] ?></option>
								<option value="1"<?php echo $_GET["search_mode"] == 1 ? " selected=\"selected\"" : "" ?>><?php echo $lang_torrents['select_or'] ?></option>
								<option value="2"<?php echo $_GET["search_mode"] == 2 ? " selected=\"selected\"" : "" ?>><?php echo $lang_torrents['select_exact'] ?></option>
							</select>
							
							
							<?php echo $lang_torrents['text_mode'] ?>
						</td>
					</tr>
<?php
/*
$Cache->new_page('hot_search', 3670, true);
if (!$Cache->get_page()){
	$secs = 3*24*60*60;
	$dt = sqlesc(date("Y-m-d H:i:s",(TIMENOW - $secs)));
	$dt2 = sqlesc(date("Y-m-d H:i:s",(TIMENOW - $secs*2)));
	sql_query("DELETE FROM suggest WHERE adddate <" . $dt2) or sqlerr();
	$searchres = sql_query("SELECT keywords, COUNT(DISTINCT userid) as count FROM suggest WHERE adddate >" . $dt . " GROUP BY keywords ORDER BY count DESC LIMIT 10") or sqlerr();
	$hotcount = 0;
	$hotsearch = "";
	while ($searchrow = mysql_fetch_assoc($searchres))
	{
		$hotsearch .= "<a href=\"".htmlspecialchars("?search=" . rawurlencode($searchrow["keywords"]) . "&notnewword=1")."\"><u>" . $searchrow["keywords"] . "</u></a>&nbsp;&nbsp;";
		$hotcount += mb_strlen($searchrow["keywords"],"UTF-8");
		if ($hotcount > 60)
			break;
	}
	$Cache->add_whole_row();
	if ($hotsearch)
	print("<tr><td class=\"embedded\" colspan=\"3\">&nbsp;&nbsp;".$hotsearch."</td></tr>");
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
*/

?>				</table>
			</td>
			<td class="rowfollow" align="center">
				<input type="submit" class="btn" value="<?php echo $lang_torrents['submit_go'] ?>" />

			</td>
		</tr>
		</tbody>
	</table>
	</form>
<?php
}

	if ($Advertisement->enable_ad()){
			$belowsearchboxad = $Advertisement->get_ad('belowsearchbox');
			echo "<div align=\"center\" style=\"margin-top: 10px\" id=\"ad_belowsearchbox\">".$belowsearchboxad[0]."</div>";
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
	print($pagertop);
	//print("<p align=\"center\"> ".$lang_functions['text_promoted_torrents_note']."</p>\n");
	




	
if (!$resseedleech = $Cache->get_value('torrent_seedleechlist_'.$CURUSER[id]))
{	
	if (!$resseedleech = $Cache->get_value('torrent_snachedlist_'.$CURUSER[id])){
	
	$resressnatch = sql_query("SELECT torrentid FROM snatched WHERE finished='no' and userid =". $CURUSER["id"]." order  by  torrentid desc");
	while ($rowressnatch= mysql_fetch_assoc($resressnatch))$resseedleech[$rowressnatch["torrentid"]]= 'unsnatched';
	
	$resressnatch = sql_query("SELECT torrentid FROM snatched WHERE finished='yes' and userid =". $CURUSER["id"]." order  by  torrentid desc");
	while ($rowressnatch= mysql_fetch_assoc($resressnatch))$resseedleech[$rowressnatch["torrentid"]]= 'snatched';	
	
	$Cache->cache_value('torrent_snachedlist_'.$CURUSER[id], $resseedleech, 3600);
	}


	$resresseedleech = sql_query("SELECT torrent , seeder FROM peers WHERE userid =". $CURUSER["id"]." order  by  seeder desc");
	while ($rowresseedleech = mysql_fetch_assoc($resresseedleech)) {
		if ($rowresseedleech["seeder"] == "yes")
		$key = "seeder";
		else
		$key = "leecher";
		$resseedleech[$rowresseedleech["torrent"]]= $key;
	}
$Cache->cache_value('torrent_seedleechlist_'.$CURUSER[id], $resseedleech,600);
}
	
	$torrentmanagephp=get_user_class() >= $torrentonpromotion_class&&get_user_class() >= $torrentsticky_class;
	if ($sectiontype == $browsecatmode)
		torrenttable($res, "torrents",$resseedleech,$torrentmanagephp);
	elseif ($sectiontype == $specialcatmode) 
		torrenttable($res, "music",$resseedleech,$torrentmanagephp);
	else torrenttable($res, "bookmarks",$resseedleech,$torrentmanagephp);
	print("<p align=\"center\"> ".$lang_functions['text_promoted_torrents_note']."</p>\n");
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
