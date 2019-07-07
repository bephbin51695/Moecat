<?php
ob_start();
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
if (get_user_class() < UC_SYSOP) {
    die('forbidden');
}
echo "<html><head><title>Do Clean-up</title></head><body>";
echo "<p>";
echo "正在清理...请稍候<br />";
ob_flush();
flush();
if ($_GET['forceall']) {
    $forceall = 1;
} else {
    $forceall = 0;
    echo "可以通过将参数'<a  href='docleanup.php?forceall=1'>forceall=1</a>'添加到url来强制完全清除<br />";
}
echo "</p>";
$tstart = getmicrotime();
require_once("include/cleanup.php");
print("<p>" . docleanup($forceall, 1) . "</p>");
$tend = getmicrotime();
$totaltime = ($tend - $tstart);
printf("消耗时间:  %f sec<br />", $totaltime);

echo "O98K<br />";
echo "</body></html>";
 
 
 