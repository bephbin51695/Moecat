<?php
require_once("include/bittorrent.php");
require_once(get_langfile_path("", false, get_langfolder_cookie()));
dbconn();
//header("Content-Type: text/html; charset=utf-8");
//failedloginscheck ();
//cur_user_check () ;
//if (!mkglobal("username:password"))
//	die();
//if($_COOKIE["c_secure_AssWeCan"]!= 'Yes')
//stderr("错误",'当前浏览器不支持COOKIE<br />请更改浏览器设置或清空浏览器缓存',false);
//if(!($username&&$password))
//stderr("错误",'请输入用户名和密码');
//function bark($text = "")
//{
//	global $lang_takelogin;
//	$text =  ($text == "" ? $lang_takelogin['std_login_fail_note'] : $text);
//	stderr($lang_takelogin['std_login_fail'], $text,false);
//}
global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
$dbms='mysql';
$host=$mysql_host;
$dbName=$mysql_db;
$user=$mysql_user;
$pass=$mysql_pass;
$dsn="$dbms:host=$host;dbname=$dbName";

$resData['code'] = 0;
$username = trim($_POST['username'])?:false;
$password = trim($_POST['password'])?:false;
if(!$username){
	$resData['msg'] = "用户名或者邮箱不能为空";
	die(json_encode($resData));
}
if(!$password){
	$resData['msg'] = "密码不能为空";
	die(json_encode($resData));
}

$sql = "SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE email = :username OR username = :username";

try {
	$dbh = new PDO($dsn, $user, $pass);
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(':username',$username,PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	$resData['msg'] = $e->getMessage();
	die(json_encode($resData));
}

//if($_POST['logintype']=='uid')
//$res = sql_query("SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE email = ". $username."OR username  = " . $username);
//elseif($_POST['logintype']=='email')
//$res = sql_query("SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE email='".mysql_real_escape_string($username)."");
//else
//$res = sql_query("SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE username = " . sqlesc($username));

if (!$row){
	$resData['msg'] = $lang_takelogin['std_account_invalid'];
	die(json_encode($resData));
}
if ($row['status'] == 'pending'){
	$resData['msg'] = $lang_takelogin['std_user_account_unconfirmed'];
	die(json_encode($resData));
}
if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"])){
	$resData['msg'] = $lang_takelogin['std_password_invalid'];
	die(json_encode($resData));

}
if ($row["enabled"] == "no"){
	$resData['msg'] = $lang_takelogin['std_account_disabled'];
	die(json_encode($resData));
}
if( TIMENOW <= $row['logouttime'] ){
	$timeNow = TIMENOW;
	$sql = "UPDATE users SET logouttime = :timenow WHERE id = :user_id";
	$stmt->bindParam(':timenow',$timeNow,PDO::PARAM_INT);
	$stmt->bindParam(':user_id',$row['id'],PDO::PARAM_INT);
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$row['logouttime']=TIMENOW;
}
	
if ($_POST["securelogin"] == "yes")
{
	$securelogin_indentity_cookie = true;
	$passh = md5($row["logouttime"].$row["passhash"].$_SERVER["REMOTE_ADDR"]);
}
else
{
	$securelogin_indentity_cookie = false;
	$passh = md5($row["logouttime"].$row["passhash"]);
}

if ($securelogin=='yes' || $_POST["ssl"] == "yes")
{
	$pprefix = "https://";
	$ssl = true;
}
else
{
	$pprefix = "http://";
	$ssl = false;
}
if ($securetracker=='yes' || $_POST["trackerssl"] == "yes")
{
	$trackerssl = true;
}
else
{
	$trackerssl = false;
}

if ($_POST["thispagewidth"] == "yes")$thispagewidth=true;
else $thispagewidth=false;

//if ($_POST["logout"] == "yes")
//{
$dbh = null;
logincookie($row["id"], $passh,1,24*3600*30,$securelogin_indentity_cookie, $ssl, $trackerssl,$thispagewidth);
//}
//
setcookie("AssWeCan",'');

if (!empty($_POST["returnto"]))
	redirect("$_POST[returnto]");
else
{
	$resData['code'] = 1;
	$resData['msg'] = "登录成功";
	$resData['url'] = "/index.php";
	die(json_encode($resData));
}
?>
