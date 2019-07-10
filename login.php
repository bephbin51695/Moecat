<!DOCTYPE html>
<html>
<head>
    <title>登录 :: MoeCat</title>
    <meta charSet="utf-8" class="next-head" />
    <meta name="viewport" content="initial-scale=1.0, width=device-width" class="next-head" />
    <meta name="description" content="MoeCat-reserved-land">
    <meta name="keywords" content="MoeCat-reserved-land">
    <link href="/favicon.ico" rel="icon" type="image/x-icon" class="next-head" />
    <link rel="stylesheet" href="//freessl.cn/_next/static/style.css" class="next-head" />
    <link rel="stylesheet" href="/public/static/layui-v2.5.4/css/layui.css">
    <script src="/public/static/vue-2.6.1/vue.min.js"></script>
     <script src="/public/static/jquery-3.4.1/jquery.min.js"></script>
    <script src="/public/static/layui-v2.5.4/layui.js"></script>
    <style>
        body{
            background-image: url(/styles/hatsune_miku/top.jpg);
            background-size: cover;
            background-repeat: no-repeat;
        }
        .bg-11 {
            background-image: none;
        }
    </style>
</head>
<body>
<div id="__next">
    <div>
        <div class="fix-top-nav">
            <div class="container" style="padding-top:8px">
                <div class="ant-row-flex ant-row-flex-start">
                    <div class="ant-col ant-col-xs-8 ant-col-sm-6 ant-col-md-6 ant-col-lg-8 ant-col-xl-9">
                        <div class="brand-logo">
                            <a href="/" style="font-size: 30px;color:#fff;">MoeCat</a>
                        </div>
                    </div>
                    <div class="ant-col ant-col-xs-16 ant-col-sm-18 ant-col-md-18 ant-col-lg-16 ant-col-xl-15">
                        <ul class="ant-menu ant-menu-horizontal mainNav  ant-menu-light ant-menu-root ant-menu-vertical"
                            role="menu">
                            <li class="ant-menu-item" role="menuitem"><a href="/">首页</a></li>
                            <li class="ant-menu-item login-btn" role="menuitem"><a href="/login.php">登录</a></li>
                            <li class="ant-menu-item register-btn" role="menuitem"><a href="/signup.php">注册</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="user-bg bg-11">
            <div class="user-contain">
                <div class="user-table-cell">
                    <div class="container">
                        <div style="width:100%;max-width:400px" class="ant-card user-card ant-card-bordered">
                            <div class="ant-card-head">
                                <div class="ant-card-head-wrapper">
                                    <div class="ant-card-head-title">
                                        <p>
                                            <i aria-label="icon: login" class="anticon anticon-login">
                                                <svg
                                                        viewBox="64 64 896 896" class="" data-icon="login" width="1em"
                                                        height="1em" fill="currentColor" aria-hidden="true"
                                                        focusable="false">
                                                    <path d="M521.7 82c-152.5-.4-286.7 78.5-363.4 197.7-3.4 5.3.4 12.3 6.7 12.3h70.3c4.8 0 9.3-2.1 12.3-5.8 7-8.5 14.5-16.7 22.4-24.5 32.6-32.5 70.5-58.1 112.7-75.9 43.6-18.4 90-27.8 137.9-27.8 47.9 0 94.3 9.3 137.9 27.8 42.2 17.8 80.1 43.4 112.7 75.9 32.6 32.5 58.1 70.4 76 112.5C865.7 417.8 875 464.1 875 512c0 47.9-9.4 94.2-27.8 137.8-17.8 42.1-43.4 80-76 112.5s-70.5 58.1-112.7 75.9A352.8 352.8 0 0 1 520.6 866c-47.9 0-94.3-9.4-137.9-27.8A353.84 353.84 0 0 1 270 762.3c-7.9-7.9-15.3-16.1-22.4-24.5-3-3.7-7.6-5.8-12.3-5.8H165c-6.3 0-10.2 7-6.7 12.3C234.9 863.2 368.5 942 520.6 942c236.2 0 428-190.1 430.4-425.6C953.4 277.1 761.3 82.6 521.7 82zM395.02 624v-76h-314c-4.4 0-8-3.6-8-8v-56c0-4.4 3.6-8 8-8h314v-76c0-6.7 7.8-10.5 13-6.3l141.9 112a8 8 0 0 1 0 12.6l-141.9 112c-5.2 4.1-13 .4-13-6.3z">
                                                    </path>
                                                </svg>
                                            </i>
                                            登录
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ant-card-body">
                                <form class="ant-form ant-form-horizontal">
                                    <div class="ant-row ant-form-item">
                                        <div class="ant-col ant-form-item-control-wrapper">
                                            <div class="ant-form-item-control">
                                                    <span class="ant-form-item-children">
                                                        <span class="ant-input-affix-wrapper">
                                                            <span class="ant-input-prefix">
                                                                <i aria-label="icon: user" style="color:rgba(0,0,0,.25)" class="anticon anticon-user">
                                                                    <svg
                                                                            viewBox="64 64 896 896" class=""
                                                                            data-icon="user" width="1em" height="1em"
                                                                            fill="currentColor" aria-hidden="true"
                                                                            focusable="false">
                                                                        <path
                                                                                d="M858.5 763.6a374 374 0 0 0-80.6-119.5 375.63 375.63 0 0 0-119.5-80.6c-.4-.2-.8-.3-1.2-.5C719.5 518 760 444.7 760 362c0-137-111-248-248-248S264 225 264 362c0 82.7 40.5 156 102.8 201.1-.4.2-.8.3-1.2.5-44.8 18.9-85 46-119.5 80.6a375.63 375.63 0 0 0-80.6 119.5A371.7 371.7 0 0 0 136 901.8a8 8 0 0 0 8 8.2h60c4.4 0 7.9-3.5 8-7.8 2-77.2 33-149.5 87.8-204.3 56.7-56.7 132-87.9 212.2-87.9s155.5 31.2 212.2 87.9C779 752.7 810 825 812 902.2c.1 4.4 3.6 7.8 8 7.8h60a8 8 0 0 0 8-8.2c-1-47.8-10.9-94.3-29.5-138.2zM512 534c-45.9 0-89.1-17.9-121.6-50.4S340 407.9 340 362c0-45.9 17.9-89.1 50.4-121.6S466.1 190 512 190s89.1 17.9 121.6 50.4S684 316.1 684 362c0 45.9-17.9 89.1-50.4 121.6S557.9 534 512 534z">
                                                                        </path>
                                                                    </svg>
                                                                </i>
                                                            </span>
                                                            <input type="text" v-model="username" placeholder="邮箱/用户名" spellcheck="false" value=""  class="ant-input" />
                                                        </span>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ant-row ant-form-item">
                                        <div class="ant-col ant-form-item-control-wrapper">
                                            <div class="ant-form-item-control">
                                                    <span class="ant-form-item-children">
                                                        <span class="ant-input-affix-wrapper">
                                                            <span class="ant-input-prefix">
                                                                <i aria-label="icon: lock"
                                                                   style="color:rgba(0,0,0,.25)"
                                                                   class="anticon anticon-lock">
                                                                    <svg
                                                                            viewBox="64 64 896 896" class=""
                                                                            data-icon="lock" width="1em" height="1em"
                                                                            fill="currentColor" aria-hidden="true"
                                                                            focusable="false">
                                                                        <path
                                                                                d="M832 464h-68V240c0-70.7-57.3-128-128-128H388c-70.7 0-128 57.3-128 128v224h-68c-17.7 0-32 14.3-32 32v384c0 17.7 14.3 32 32 32h640c17.7 0 32-14.3 32-32V496c0-17.7-14.3-32-32-32zM332 240c0-30.9 25.1-56 56-56h248c30.9 0 56 25.1 56 56v224H332V240zm460 600H232V536h560v304zM484 701v53c0 4.4 3.6 8 8 8h40c4.4 0 8-3.6 8-8v-53a48.01 48.01 0 1 0-56 0z">
                                                                        </path>
                                                                    </svg>
                                                                </i>
                                                            </span>
                                                            <input type="password" v-model="password" placeholder="密码" spellcheck="false" value=""  class="ant-input" />
                                                        </span>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ant-row ant-form-item">
                                        <div class="ant-col ant-form-item-control-wrapper">
                                            <div class="ant-form-item-control">
                                                    <span class="ant-form-item-children">
                                                        <button @click="formSubmit"  type="button" class="ant-btn btn-block ant-btn-info">
                                                            <span>登录</span>
                                                        </button>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ant-row ant-form-item clearfix" style="margin-bottom:0">
                                        <div class="ant-col ant-form-item-control-wrapper">
                                            <div class="ant-form-item-control">
                                                    <span class="ant-form-item-children">
                                                        <a class="pull-right link-color" href="/recover.php">通过邮件找回密码</a>
                                                        <a class="pull-right link-color" href="/confirm_resend.php">重新发送验证邮件&nbsp;&nbsp;</a>
                                                        <a class="pull-right link-color" href="/signup.php">还没有账号? 马上注册！&nbsp;&nbsp;</a>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-info">
            <div class="text-info">
                <p>
                    <a href="/faq.php" target="_blank">常见问题</a>
                    <a href="/rules.php" target="_blank">用户规则</a>
                    <a href="mailto:moecat@shabimail.com" target="_blank">联系我们</a>
                    <a href="/punishment.php" target="_blank">封禁查询</a>
                    <br />©  MoeCat 2019 Powered by NexusPHP
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    new Vue({
        el:"#__next",
        data:{
            username:"",
            password:"" ,
        },
        created() {
            layui.use('layer', function(){});
        },
        methods: {
            formSubmit:function(){
                if(!this.username.length){
                    return layer.msg('用户名或者邮箱不能为空', {
                        icon: 2
                    });
                }
                if(!this.password.length){
                    return layer.msg('密码不能为空', {
                        icon: 2
                    });
                }
                $.post("/takelogin.php",{
                    username: this.username,
                    password: this.password,
                },((res)=>{
                    res = JSON.parse(res);
                    if(!res.code){
                        return layer.alert(res.msg, {
                            icon: 2
                        })
                    }else{
                        layer.msg(res.msg, {
                            icon: 1
                        });
                        setTimeout(()=>{
                            location.href = res.url
                        },1000)
                    }

                }))
            }
        },
    })
</script>