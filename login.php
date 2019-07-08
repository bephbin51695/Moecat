<?php
require_once("include/bittorrent.php");
dbconn(true);
$langid = 0 + $_GET['sitelanguage'];
if ($langid) {
    $lang_folder = validlang($langid);
    if (get_langfolder_cookie() != $lang_folder) {
        set_langfolder_cookie($lang_folder);
        header("Location:" . $_SERVER['PHP_SELF']);
    }
}
require_once(get_langfile_path("", false, $CURLANGDIR));
setcookie("c_secure_AssWeCan", 'Yes');
failedloginscheck();
cur_user_check();
stdhead($lang_login['head_login']);
unset($returnto);
if (!empty($_GET["returnto"])) {
    $returnto = $_GET["returnto"];
    if (!$_GET["nowarn"]) {
        print("<h1>" . $lang_login['h1_not_logged_in'] . "</h1>\n");
        print("<p><b>" . $lang_login['p_error'] . "</b> " . $lang_login['p_after_logged_in'] . "</p>\n");
    }
}

?>
    <style>
        body{
            overflow-y: hidden;
        }
        #toppic{
            padding: 1rem 1rem 9rem 1rem;
            background: rgba(0,0,0,0.3);
        }
        .mainouter{
            width: 70%;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04);
            border-radius: 0.4rem;
        }
        #nav_block{
            border: none;
        }
    </style>
    <form method="post" action="takelogin.php">
        <p><?php echo $lang_login['p_you_have'] ?>
            <b><?php echo remaining(); ?></b> <?php echo $lang_login['p_remaining_tries'] ?></p>
        <table border="0" cellpadding="5">
            <tr>
                <td class="rowhead">
                    <select name="logintype">
                        <option value="username">用户名称</option>
                        <option value="uid">用户编号</option>
                        <option value="email">用户邮箱</option>
                    </select>
                </td>
                <td class="rowfollow" align="left"><input type="text" name="username" value='<?
                    echo(cookietureuserid(true)) ?>' style="width: 180px; border: 1px solid gray"/></td>
            </tr>
            <tr>
                <td class="rowhead"><?php echo $lang_login['rowhead_password'] ?></td>
                <td class="rowfollow" align="left"><input type="password" name="password"
                                                          style="width: 180px; border: 1px solid gray"/></td>
            </tr>
            <?php
            if ($securelogin == "yes")
                $sec = "checked=\"checked\" disabled=\"disabled\"";
            elseif ($securelogin == "no")
                $sec = "disabled=\"disabled\"";
            elseif ($securelogin == "op")
                $sec = "";
            if ($securetracker == "yes")
                $sectra = "checked=\"checked\" disabled=\"disabled\"";
            elseif ($securetracker == "no")
                $sectra = "disabled=\"disabled\"";
            elseif ($securetracker == "op")
                $sectra = "";
            if ($_COOKIE["c_secure_thispagewidth"] != base64("nope"))
                $thispagewidthscreenckeck = "checked=\"checked\" ";
            ?>
            <tr>
                <td class="rowhead">其他选项</td>
                <td class="rowfollow" align="left">
                    <input class="checkbox" type="checkbox" name="thispagewidth" value="yes"
                        <? echo $thispagewidthscreenckeck; ?>/> 使用宽屏模式
                </td>
            </tr>
            <tr>
                <td class="rowhead">记住密码</td>
                <td class="rowfollow">
                    <select name="logout" style="width: 100%">
                        <option value="24">记住一天</option>
                        <option value="168">记住7天</option>
                        <option value="720">记住30天</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="toolbox" colspan="2" align="right"><input type="submit"
                                                                     value="<?php echo $lang_login['button_login'] ?>"
                                                                     class="btn"/>
                    <input type="reset" value="<?php echo $lang_login['button_reset'] ?>" class="btn"/></td>
            </tr>
        </table>
        <p>
            <?php
            if (isset($returnto))
                print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");
            ?>
    </form>
<?php
if ($smtptype != 'none') {
    ?>
    <p><?php echo $lang_login['p_forget_pass_recover'] . $lang_login['p_resend_confirm'] ?>
    <?php
}
echo $lang_login['p_no_account_signup'] . "</p>";
echo $lang_login['login_dibu'] . "<p>";
if ($showhelpbox_main != 'no' && !isset($returnto)) {
    ?>
    <table width="700" class="main" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td class="embedded">
    <h2><?php echo $lang_login['text_helpbox'] ?>&nbsp;-&nbsp;<font
                class="small"><?php echo $lang_login['text_helpbox_note'] ?></font>
        <font class="small" id="countdown"></font><font class="small">秒后自动刷新</font>
    </h2>
    <?php
    print("<table width='100%' border='1' cellspacing='0' cellpadding='1'><tr><td class=\"text\" align=\"center\">\n");
    print("<iframe src='shoutbox.php?type=helpbox' width='680' height='140' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
    print("<form action='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php' id='helpbox' method='post' target='sbox' name='shbox'>\n");
    print($lang_login['text_message'] . "<input type='text' id=\"hbtext\" name='shbox_text' autocomplete='off' style='width: 500px; border: 1px solid gray' ><input type='submit' id='hbsubmit' class='btn' name='shout' value=\"" . $lang_login['sumbit_shout'] . "\" /><input type='reset' class='btn' value=" . $lang_login['submit_clear'] . " /> <input type='hidden' name='sent' value='yes'><input type='hidden' name='type' value='helpbox' />\n");
    print("<div id=sbword style=\"display: none\">" . $lang_login['sumbit_shout'] . "</div>");
    print(smile_row("shbox", "shbox_text"));
    print("</form></td></tr></table></td></tr></table>");
}
stdfoot();
