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
echo "正在清理...<br /><br />";
ob_flush();
flush();
if ($_GET['forceall']) {
    $forceall = 1;
    echo "当前模式：强制清理模式<br />";
} else {
    $forceall = 0;
    echo "当前模式：非强制清理模式<br />";
    echo "<button type='button'  onclick='doForce()'>执行强制清除模式</button><br />";
    echo "<script>function doForce(){location.href='docleanup.php?forceall=1'}</script>";
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

 
 
 