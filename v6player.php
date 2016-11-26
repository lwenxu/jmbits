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
    <link rel="stylesheet" type="text/css" href="styles/BambooGreen/components.css"/>
    <link rel="stylesheet" type="text/css" href="styles/bootstrap/css/bootstrap.min.css"/>
	<title>在线点播</title></head>
<body style="text-align:center;background: #364150 !important;">

<div class="portlet-body" style="margin-top: 10%">
    <div class="note note-success" style="width: 60%;margin-left: 20%">
        <h2 class="block">下载安装播放器</h2>
        <h4>没有安装v6player比播放器?前往
            <a
               href="http://219.245.31.94/nwupt/assets/v6player.exe">这里</a>下载最新安装包
        </h4>
    </div>

    <div class="note note-warning" style="width: 60%;margin-left: 20%">
        <h2 class="block">使用前注意</h2>
        <h4>
           1.设置下载目录为较大硬盘目录<br/>2.需同意v6播放器绑定影音播放文件<br/>3.右键可下载字幕，支持拖曳，请下载安装最新版本...即将跳转
        </h4>
    </div>
</div>
<!--<p>-->
<!--    <b>没有安装v6player比播放器?前往-->
<!--        <a class="btn btn-success"-->
<!--			href="http://219.245.31.94/nwupt/assets/v6player.exe">这里</a>下载最新安装包-->
<!--    </b>-->
<!--</p>-->
<!--<p><b><font color="white">首次使用请注意：1.设置下载目录为较大硬盘目录<br/>2.需同意v6播放器绑定影音播放文件<br/>3.右键可下载字幕，支持拖曳，请下载安装最新版本...即将跳转</font></b>-->
<!--</p>-->
<?php
// 将 www.yy.org 替换为你自己站点 地址
header("Refresh: 5; url=v6player://$id&ty=1&ro=2&id=$id&ua=$u&url=127.0.0.1/nwupt");
?>
</body>
</html>