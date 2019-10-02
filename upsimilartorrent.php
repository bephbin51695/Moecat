<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path("edit.php"));
loggedinorreturn();

$id = 0 + $_GET['id'];
if (!$id)
    stderr("错误！","未填写种子ID",1);

$res = sql_query("SELECT torrents.*, categories.mode as cat_mode FROM torrents LEFT JOIN categories ON category = categories.id WHERE torrents.id = $id");
$row = mysql_fetch_array($res);
if (!$row) stderr("错误！","种子ID不存在",1);

if ($enablespecial == 'yes' && (get_user_class() >= $movetorrent_class||$CURUSER["picker"] == 'yes'))
    $allowmove = true; //enable moving torrent to other section
else {$allowmove = false;
}

if ($enablespecial2 != 'yes'|| get_user_class() < $movetorrent_class)$specialcatmode2=0;


$sectionmode = $row['cat_mode'];

if ($sectionmode == $browsecatmode)
{
    $othermode = $specialcatmode;
    $othermode2 = $specialcatmode2;
    $movenote = $lang_edit['text_move_to_special'];
}
elseif($sectionmode == $specialcatmode)
{
    $othermode = $browsecatmode;
    $othermode2 = $specialcatmode2;
    $movenote = $lang_edit['text_move_to_browse'];
}
else
{
    $othermode = $browsecatmode;
    $othermode2 = $specialcatmode;
    $movenote = $lang_edit['text_move_to_browse'];
}

$showsource = (get_searchbox_value($sectionmode, 'showsource') || ($allowmove && (get_searchbox_value($othermode, 'showsource')||get_searchbox_value($othermode2, 'showsource')))); //whether show sources or not
$showmedium = (get_searchbox_value($sectionmode, 'showmedium') || ($allowmove &&(get_searchbox_value($othermode, 'showmedium')||get_searchbox_value($othermode2, 'showmedium')))); //whether show media or not
$showcodec = (get_searchbox_value($sectionmode, 'showcodec') || ($allowmove && (get_searchbox_value($othermode, 'showcodec')||get_searchbox_value($othermode2, 'showcodec')))); //whether show codecs or not
$showstandard = (get_searchbox_value($sectionmode, 'showstandard') || $allowmove && (get_searchbox_value($othermode, 'showstandard')||get_searchbox_value($othermode2, 'showstandard'))); //whether show standards or not
$showprocessing = (get_searchbox_value($sectionmode, 'showprocessing') || $allowmove && (get_searchbox_value($othermode, 'showprocessing')||get_searchbox_value($othermode2, 'showprocessing'))); //whether show processings or not
$showteam = (get_searchbox_value($sectionmode, 'showteam') || ($allowmove && (get_searchbox_value($othermode, 'showteam')||get_searchbox_value($othermode2, 'showteam')))); //whether show teams or not
$showaudiocodec = (get_searchbox_value($sectionmode, 'showaudiocodec') || $allowmove &&(get_searchbox_value($othermode, 'showaudiocodec')||get_searchbox_value($othermode, 'showaudiocodec'))); //whether show audio codecs or not

stdhead("referred from torrent: " . $_GET['id']);
?> <script type="text/javascript" src="common.php<?php $cssupdatedate=($cssdate_tweak ? "?".htmlspecialchars($cssdate_tweak) : "");echo $cssupdatedate?>"></script> <?

if (!isset($CURUSER) || $CURUSER["uploadpos"] == 'no' || !user_can_upload()) {
    print("<h1 align=\"center\">".$lang_edit['text_cannot_edit_torrent']."</h1>");
    print("<p>".$lang_edit['text_cannot_edit_torrent_note']."</p>");
}
else{
    print("<form method=\"post\" id=\"compose\" name=\"edittorrent\" action=\"takeupload.php\" enctype=\"multipart/form-data\">");
    print("<input type=\"hidden\" name=\"id\" value=\"$id\" />");
    if (isset($_GET["returnto"]))
        print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />");
    print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"98%\">\n");
    print("<tr>
					<td class='colhead' colspan='2' align='center'>
					<font color=\"red\">请按照规则要求发布资源</font>
					</td>
				</tr>
				<tr>
					<td class='colhead' colspan='2' align='left'>"
						.$lang_upload['text_tracker_url']."<b>填不填写无所谓啦,填写什么也无所谓啦,填写本站首页地址也无所谓啦 ╮(╯_╰)╭</b>
					</td>
				</tr>");
    tr("种子文件<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" />\n", 1);
    $s = "<select name=\"type\" id=\"browsecat\" onchange=\"javascript:secondtype();notechange()\" >";
    $cats = genrelist($sectionmode);
    foreach ($cats as $subrow) {
        $s .= "<option value=\"" . $subrow["id"] . "\"";
        if ($subrow["id"] == $row["category"])
            $s .= " selected=\"selected\"";
        $s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
    }

    if ($allowmove){


        $cats2 = genrelist($othermode);
        foreach ($cats2 as $subrow) {
            $s .= "<option value=\"" . $subrow["id"] . "\"";
            if ($subrow["id"] == $row["category"])
                $s .= " selected=\"selected\"";
            $s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
        }

        $cats3 = genrelist($othermode2);
        foreach ($cats3 as $subrow) {
            $s .= "<option value=\"" . $subrow["id"] . "\"";
            if ($subrow["id"] == $row["category"])
                $s .= " selected=\"selected\"";
            $s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
        }





    }

    $s .= "</select>\n";
    if ($allowmove){
        $s2 = "<select name=\"type\" id=newcat disabled>\n";
        $cats2 = genrelist($othermode);
        foreach ($cats2 as $subrow) {
            $s2 .= "<option value=\"" . $subrow["id"] . "\"";
            if ($subrow["id"] == $row["category"])
                $s2 .= " selected=\"selected\"";
            $s2 .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
        }
        $s2 .= "</select>\n";
        $movecheckbox = "<input type=\"checkbox\" id=movecheck name=\"movecheck\" value=\"1\" onclick=\"disableother2('oricat','newcat')\" />";
    }
    //tr($lang_edit['row_type']."<font color=\"red\">*</font>", $s.($allowmove ? "&nbsp;&nbsp;".$movecheckbox.$movenote.$s2 : ""), 1);
    //if ($showsource || $showmedium || $showcodec || $showaudiocodec || $showstandard || $showprocessing)
    {





        if ($showsource){
            $source_select = torrent_selection($lang_edit['text_source'],"source_sel","sources",$row["source"]);
        }
        else $source_select = "";

        if ($showmedium){
            $medium_select = torrent_selection($lang_edit['text_medium'],"medium_sel","media",$row["medium"]);
        }
        else $medium_select = "";

        if ($showcodec){
            $codec_select = torrent_selection($lang_edit['text_codec'],"codec_sel","codecs",$row["codec"]);
        }
        else $codec_select = "";

        if ($showaudiocodec){
            $audiocodec_select = torrent_selection("","audiocodec_sel","audiocodecs",$row["audiocodec"],true);
        }
        else $audiocodec_select = "";

        if ($showstandard){
            $standard_select = torrent_selection($lang_edit['text_standard'],"standard_sel","standards",$row["standard"]);
        }
        else $standard_select = "";

        if ($showprocessing){
            $processing_select = torrent_selection($lang_edit['text_processing'],"processing_sel","processings",$row["processing"]);
        }
        else $processing_select = "";

        if ($showteam){
            $team_select = torrent_selection($lang_edit['text_team'],"team_sel","teams",$row["team"]);
        }
        else $showteam = "";

        tr($lang_edit['row_quality']."<font color=\"red\">*</font>","<b>".$lang_edit['row_type'].":&nbsp;</b>". $s.$audiocodec_select."<b><font class=\"medium\" id=\"texttorrentsecondnote\" ></font></b><br />" .$source_select . $medium_select . $codec_select .  $standard_select .$team_select. $processing_select, 1);

    }
    tr($lang_edit['row_torrent_name']."<font color=\"red\">*</font>", "<input type=\"text\" style=\"width: 650px;\" name=\"name\" id=\"name\" value=\"" . htmlspecialchars($row["name"]) . "\" /><br><b><font class=\"medium\" id=\"texttorrentnamenote\" ></font></b>", 1);
    if ($smalldescription_main == 'yes')
        tr($lang_edit['row_small_description'], "<input type=\"text\" style=\"width: 650px;\" name=\"small_descr\" value=\"" . htmlspecialchars($row["small_descr"]) . "\" /><br><b><font class=\"medium\" id=\"texttorrentsmaillnamenote\"></font></b>", 1);

    get_external_tr($row["url"],$row["urltype"]);

    if ($enablenfo_main=='yes')
        tr($lang_edit['row_nfo_file'], "<font class=\"medium\">
	<input type=\"radio\" name=\"nfoaction\" value=\"remove\" />".$lang_edit['radio_remove'].
            "<input id=\"nfoupdate\" type=\"radio\" name=\"nfoaction\" value=\"update\" checked=\"checked\" />".$lang_edit['radio_update']."
	
	</font><input type=\"file\" name=\"nfo\" onchange=\"document.getElementById('nfoupdate').checked=true\" />", 1);


    print("<tr><td class=\"rowhead\">".$lang_edit['row_description']."<font color=\"red\">*</font></td><td class=\"rowfollow\">");
    textbbcode("edittorrent","descr",($row["descr"]), false);
    print("</td></tr>");


    /*if ($showteam){
        if ($showteam){
            $team_select = torrent_selection($lang_edit['text_team'],"team_sel","teams",$row["team"]);
        }
        else $showteam = "";

        tr($lang_edit['row_content'],$team_select,1);
    }*/
    tr($lang_edit['row_check'], "<input type=\"checkbox\" name=\"visible\"" . ($row["visible"] == "yes" ? " checked=\"checked\"" : "" ) . " value=\"1\" /> ".$lang_edit['checkbox_visible']."&nbsp;&nbsp;&nbsp;".(get_user_class() >= $beanonymous_class || get_user_class() >= $torrentmanage_class ? "<input type=\"checkbox\" name=\"anonymous\"" . ($row["anonymous"] == "yes" ? " checked=\"checked\"" : "" ) . " value=\"1\" />".$lang_edit['checkbox_anonymous_note']."&nbsp;&nbsp;&nbsp;" : "").(get_user_class() >= $torrentmanage_class ? "<input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"yes\" /> ".$lang_edit['checkbox_banned'] : ""), 1);
    $y=1;
    for ($x=1; $x<=$row["tags"]; $x++){
        if (($x & $row["tags"]) == $x){
            $tag_check[$y]="checked";
            $y++;
        }
        else{
            $tag_check[$y]="";
            $y++;
        }
    }
    tr($lang_edit['row_tags'],"<input type=\"checkbox\" name=\"tags[]\"" . ($tag_check[1] == "checked" ? " checked=\"checked\"" : "" ) ."value=\"1\" />禁转 <input type=\"checkbox\" name=\"tags[]\" " . ($tag_check[2] == "checked" ? " checked=\"checked\"" : "" ) ."value=\"2\" />首发 <input type=\"checkbox\" name=\"tags[]\" " . ($tag_check[4] == "checked" ? " checked=\"checked\"" : "" ) ."value=\"4\" />官方 <input type=\"checkbox\" name=\"tags[]\" " . ($tag_check[8] == "checked" ? " checked=\"checked\"" : "" ) ."value=\"8\" />自制 <input type=\"checkbox\" name=\"tags[]\" " . ($tag_check[16] == "checked" ? " checked=\"checked\"" : "" ) ."value=\"16\" />国语 <input type=\"checkbox\" name=\"tags[]\" " . ($tag_check[32] == "checked" ? " checked=\"checked\"" : "" ) ."value=\"32\" />中字",1);
    print("<tr><td class=\"toolbox\" colspan=\"2\" align=\"center\"><input id=\"qr\" type=\"submit\" value=\"发布\" /> </td></tr>\n");
    print("</table>\n");
    print("</form>\n");

}
print("<script>javascript:secondtype();notechange();</script> ");
stdfoot();
?>
