<?php
require_once("include/bittorrent.php");
dbconn();
$id = (int)$_GET["id"];
if (!$id)
	httperr();
$passkey = $_GET['passkey'];
if ($passkey){
	$res = sql_query("SELECT * FROM users WHERE passkey=". sqlesc($passkey)." LIMIT 1");
	$user = mysql_fetch_array($res);
	if (!$user)
		die("invalid passkey");
	elseif ($user['enabled'] == 'no' || $user['parked'] == 'yes')
		die("account disabed or parked");
	$oldip = $user['ip'];
	$user['ip'] = getip();
	$CURUSER = $user;
}
else
{
	loggedinorreturn(false);
	parked();
	$letdown = $_GET['letdown'];
	if (!$letdown && $CURUSER['showdlnotice'] == 2)
	{
		header("Location: " . get_protocol_prefix() . "$BASEURL/bakatest.php");
	}elseif (!$letdown && $CURUSER['showdlnotice'] == 1)
	{
		header("Location: " . get_protocol_prefix() . "$BASEURL/bakatest.php");
		//header("Location: " . get_protocol_prefix() . "$BASEURL/downloadnotice.php?torrentid=".$id."&type=firsttimebaka");
	}
	elseif (!$letdown && $CURUSER['showclienterror'] == 'yes')
	{
		header("Location: " . get_protocol_prefix() . "$BASEURL/downloadnotice.php?torrentid=".$id."&type=client");
	}
	elseif (!$letdown && $CURUSER['leechwarn'] == 'yes')
	{
		header("Location: " . get_protocol_prefix() . "$BASEURL/downloadnotice.php?torrentid=".$id."&type=ratio");
	}
}
//User may choose to download torrent from RSS. So log ip changes when downloading torrents.
if ($iplog1 == "yes") {
	if (($oldip != $CURUSER["ip"]) && $CURUSER["ip"])
	sql_query("INSERT INTO iplog (ip, userid, access) VALUES (" . sqlesc($CURUSER['ip']) . ", " . $CURUSER['id'] . ", '" . $CURUSER['last_access'] . "')   ON DUPLICATE KEY update access=values(access) ");

}
//User may choose to download torrent from RSS. So update his last_access and ip when downloading torrents.
sql_query("UPDATE users SET last_access = ".sqlesc(date("Y-m-d H:i:s")).", ip = ".sqlesc($CURUSER['ip'])."  WHERE id = ".sqlesc($CURUSER['id']));

/*
@ini_set('zlib.output_compression', 'Off');
@set_time_limit(0);

if (@ini_get('output_handler') == 'ob_gzhandler' AND @ob_get_length() !== false)
{	// if output_handler = ob_gzhandler, turn it off and remove the header sent by PHP
	@ob_end_clean();
	header('Content-Encoding:');
}
*/
if ($_COOKIE["c_secure_tracker_ssl"] == base64("yeah") or (int)$_GET["https"] == 1)
$tracker_ssl = true;
else
$tracker_ssl = false;
if ($tracker_ssl == true){
	$ssl_torrent = "https://";
	if ($https_announce_urls[0] != "")
		$base_announce_url = $https_announce_urls[0];
	else
		$base_announce_url = $announce_urls[0];
}
else{
	$ssl_torrent = "http://";
	$base_announce_url = $announce_urls[0];
}
$res = sql_query("SELECT torrents.id,torrents.name, filename, save_as,  size, owner,banned,categories.mode FROM torrents LEFT JOIN categories ON category = categories.id WHERE torrents.id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);
$fn = "$torrent_dir/$id.torrent";
if ($CURUSER['downloadpos']=="no")
	permissiondenied();
if (!$row || !is_file($fn) || !is_readable($fn))
	{httperr();
	stderr("111");}
if (($row['banned'] == 'yes'||$row['mode']!=$browsecatmode&&$row['owner']!=$CURUSER['id'] )&& get_user_class() < $seebanned_class)
	permissiondenied();
sql_query("UPDATE torrents SET hits = hits + 1 WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

require_once "include/benc.php";

if (strlen($CURUSER['passkey']) != 32) {
	$CURUSER['passkey'] = md5($CURUSER['username'].date("Y-m-d H:i:s").$CURUSER['passhash']);
	sql_query("UPDATE users SET passkey=".sqlesc($CURUSER[passkey])." WHERE id=".sqlesc($CURUSER[id]));
}

$dict = bdec_file($fn, $max_torrent_size);
//$dict = bdec_simple($dict);
$dict['value']['announce']["type"] = "string";
$dict['value']['announce']['value'] = $ssl_torrent . $base_announce_url . "?passkey=$CURUSER[passkey]";
$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];
$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
$dict['value']['comment']=bdec(benc_str( $torrentnameprefix."[".$id."]".$row["name"]."@".$dict['value']['info']['value']['source']['value']));
$dict['value']['website']=bdec(benc_str($SITENAME." : ".$BASEURLV4V6));
$dict['value'][md5('i-'.$CURUSER['id'].'e')]=bdec('i-'.$CURUSER['id'].'e');
if ($announce_urls[1] != "") // add multi-tracker
{
	$dict['value']['announce-list']['type'] = "list";
	$dict['value']['announce-list']['value'][0]['type'] = "list";
	$dict['value']['announce-list']['value'][0]['value'][0]["type"] = "string";
	$dict['value']['announce-list']['value'][0]['value'][0]["value"] = $ssl_torrent . $announce_urls[0] . "?passkey=$CURUSER[passkey]";
	$dict['value']['announce-list']['value'][0]['value'][0]["string"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["value"]).":".$dict['value']['announce-list']['value'][0]['value'][0]["value"];
	$dict['value']['announce-list']['value'][0]['value'][0]["strlen"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["string"]);
	$dict['value']['announce-list']['value'][0]['string'] = "l".$dict['value']['announce-list']['value'][0]['value'][0]["string"]."e";
	$dict['value']['announce-list']['value'][0]['strlen'] = strlen($dict['value']['announce-list']['value'][0]['string']);
	
	$dict['value']['announce-list']['value'][1]['type'] = "list";
	$dict['value']['announce-list']['value'][1]['value'][0]["type"] = "string";
	$dict['value']['announce-list']['value'][1]['value'][0]["value"] = $ssl_torrent . $announce_urls[1] . "?passkey=$CURUSER[passkey]";
	$dict['value']['announce-list']['value'][1]['value'][0]["string"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["value"]).":".$dict['value']['announce-list']['value'][0]['value'][0]["value"];
	$dict['value']['announce-list']['value'][1]['value'][0]["strlen"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["string"]);
	$dict['value']['announce-list']['value'][1]['string'] = "l".$dict['value']['announce-list']['value'][0]['value'][0]["string"]."e";
	$dict['value']['announce-list']['value'][1]['strlen'] = strlen($dict['value']['announce-list']['value'][0]['string']);
	if ($announce_urls[2] &&!ip2long(getip()))
	{
	$dict['value']['announce-list']['value'][2]['type'] = "list";
	$dict['value']['announce-list']['value'][2]['value'][0]["type"] = "string";
	$dict['value']['announce-list']['value'][2]['value'][0]["value"] = $ssl_torrent . $announce_urls[2];
	$dict['value']['announce-list']['value'][2]['value'][0]["string"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["value"]).":".$dict['value']['announce-list']['value'][0]['value'][0]["value"];
	$dict['value']['announce-list']['value'][2]['value'][0]["strlen"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["string"]);
	$dict['value']['announce-list']['value'][2]['string'] = "l".$dict['value']['announce-list']['value'][0]['value'][0]["string"]."e";
	$dict['value']['announce-list']['value'][2]['strlen'] = strlen($dict['value']['announce-list']['value'][0]['string']);
	}
	
	if ($announce_urls[3] &&!ip2long(getip()))
	{
	$dict['value']['announce-list']['value'][3]['type'] = "list";
	$dict['value']['announce-list']['value'][3]['value'][0]["type"] = "string";
	$dict['value']['announce-list']['value'][3]['value'][0]["value"] = $ssl_torrent . $announce_urls[3];
	$dict['value']['announce-list']['value'][3]['value'][0]["string"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["value"]).":".$dict['value']['announce-list']['value'][0]['value'][0]["value"];
	$dict['value']['announce-list']['value'][3]['value'][0]["strlen"] = strlen($dict['value']['announce-list']['value'][0]['value'][0]["string"]);
	$dict['value']['announce-list']['value'][3]['string'] = "l".$dict['value']['announce-list']['value'][0]['value'][0]["string"]."e";
	$dict['value']['announce-list']['value'][3]['strlen'] = strlen($dict['value']['announce-list']['value'][0]['string']);
	}
	
	
	
	$dict['value']['announce-list']['string'] = "l".$dict['value']['announce-list']['value'][0]['string'].$dict['value']['announce-list']['value'][1]['string']."e";
	$dict['value']['announce-list']['strlen'] = strlen($dict['value']['announce-list']['string']);
}
/*
header ("Expires: Tue, 1 Jan 1980 00:00:00 GMT");
header ("Last-Modified: ".date("D, d M Y H:i:s"));
header ("Cache-Control: no-store, no-cache, must-revalidate");
header ("Cache-Control: post-check=0, pre-check=0", false);
header ("Pragma: no-cache");
header ("X-Powered-By: ".VERSION." (c) ".date("Y")." ".$SITENAME."");
header ("Accept-Ranges: bytes");
header ("Connection: close");
header ("Content-Transfer-Encoding: binary");
*/
$row["name"]=str_replace("/",".",$row["name"]);
$row["name"]=str_replace("\\",".",$row["name"]);
$row["name"]=str_replace(":",".",$row["name"]);
//$row["name"]=mb_strcut($row["name"], 0, 50,"UTF-8");
$row["name"]=$torrentnameprefix."[".$row["id"]."]".($row["name"]).".torrent";
header("Content-Type: application/x-bittorrent");
/*
if ( str_replace("Gecko", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'])
{
	header ("Content-Disposition: attachment; filename=\"fefefefe$torrentnameprefix".$row["name"].".torrent\"; charset=utf-8");
}
else if ( str_replace("Firefox", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'] )
{
	header ("Content-Disposition: attachment; filename=\"t43t43t43$torrentnameprefix".$row["name"].".torrent\"; charset=utf-8");
}
else if ( str_replace("Opera", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'] )
{
	header ("Content-Disposition: attachment; filename=\"ewrGWreas$torrentnameprefix".$row["name"].".torrent\"; charset=utf-8");
}
else if ( str_replace("IE", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'] )
{
	header ("Content-Disposition: attachment; filename=".str_replace("+", "%20", rawurlencode("$torrentnameprefix" . $row["name"] .".torrent")));
}
else
{
	header ("Content-Disposition: attachment; filename=".str_replace("+", "%20", rawurlencode("$torrentnameprefix" . $row["name"] .".torrent")));
}
*/

if ( str_replace("Gecko", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'])
{
	header ("Content-Disposition: attachment; filename=\"{$row["name"]}\" ; charset=utf-8");
}
else if ( str_replace("Firefox", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'] )
{
	header ("Content-Disposition: attachment; filename=\"{$row["name"]}\" ; charset=utf-8");
}
else if ( str_replace("Opera", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'] )
{
	header ("Content-Disposition: attachment; filename=\"{$row["name"]}\" ; charset=utf-8");
}
else if ( str_replace("IE", "", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT'] )
{
	header ("Content-Disposition: attachment; filename=".str_replace("+", "%20", rawurlencode($row["name"])));
}
else
{
	header ("Content-Disposition: attachment; filename=".str_replace("+", "%20", rawurlencode($row["name"])));
}



//header ("Content-Disposition: attachment; filename=".$row["filename"]."");
//ob_implicit_flush(true);
print(benc($dict));
?>
