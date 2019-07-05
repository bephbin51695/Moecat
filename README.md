# MoeCatPT

## MoeCatPT是一个基于NexusPHP搭建的网站，源代码修改自蚂蚁PT开源代码

## 服务器运行环境：
Nginx 1.16.0

Mysql 5.6.44

PHP 5.6.40

[Memcache](http://php.net/manual/en/book.memcache.php)

[gmp](http://php.net/manual/en/book.gmp.php)

[exec function](http://php.net/manual/en/function.exec.php)

## 因为时间太紧，我也有点懒，文件里面还有少许的蚂蚁PT的文字/图标（上线网站已删除）
## 如果你要架设网站，请务必删除，谢谢

1.注意及时修改class_cache.php与class_cache_announce.php中第195行，选择合适的缓存器。

2.并且还要修改allconfig.php中数据库连接部分。

3.验证码的话，如果开启海报验证，要等种子数目足够多才行。

如果要开启IMDB系统，请到include/functions_plus.php文件里，更换你自己的APIKEY。

H&R系统设定在include/cleanup.php。

首页聊天机器人ID设定在include/config.php文件，大约在440行。

其他的想到再写吧 2333333