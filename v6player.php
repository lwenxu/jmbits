<?php
/**
 * Created by PhpStorm.
 * User: lwenxu
 * Date: 2016/11/19
 * Time: 17:26
 */
require "include/bittorrent.php"; dbconn(); loggedinorreturn(); if (!isset($_GET['id']) || !isset($_GET['u'])) {
	exit;
} $id = $_GET['id']; $u = $_GET['u']; ?> <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>v6-在线点播</title></head>
<body style="text-align:center;background: #87CEEB;"><p><b>没有安装最新v6player?前往 <a
			href="http://www.v6player.org/v6player/">官网!</a></b></p>
<p><b>或者点击<a href="v6player1.02_ipv4.exe">这里</a>下载最新安装包</b></p>
<p><b><font color="red">首次使用请注意：1.设置下载目录为较大硬盘目录<br/>2.需同意v6播放器绑定影音播放文件<br/>3.右键可下载字幕，支持拖曳，请下载安装最新版本...即将跳转</font></b>
</p>
<p><a href="http://www.v6player.org/update/v6player1.02_ipv4.exe"></a>
</p> <?php
// 将 www.yy.org 替换为你自己站点 地址
header("Refresh: 5; url=v6player://$id&ty=1&ro=2&id=$id&ua=$u&url=127.0.0.1/nwupt");
?>
</body>
</html>