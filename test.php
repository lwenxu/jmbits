<?php
/**
 * Created by PhpStorm.
 * User: lwen
 * Date: 2016/10/21
 * Time: 18:36
 */
//<div class="bubble bubble-right bubble-shoutbox bubble-right-shoutbox">
//    <blockquote class="quote">引用: <span class="nowrap"><a href="userdetails.php?id=40216" class="Helper_Name username" data-userid="40216"><b><span class="fa fa-shield" style="padding-right:2px;padding-top:2px;"></span>Lowphy</b></a></span><br><blockquote class="quote">引用: 游客*62.51<br>只能在校园网里注册蒲公英吗</blockquote>只要有ipv6环境就可以</blockquote>现在是不是不能注册了。。</div>


//echo date('Ym');
//
//$arr=explode("=","https://hdchina.club/torrents.php?cat=401");

//var_dump($arr);


//function getClientIP()
//{
//	global $ip;
//	if (getenv("HTTP_CLIENT_IP"))
//		$ip = getenv("HTTP_CLIENT_IP");
//	else if (getenv("HTTP_X_FORWARDED_FOR"))
//		$ip = getenv("HTTP_X_FORWARDED_FOR");
//	else if (getenv("REMOTE_ADDR"))
//		$ip = getenv("REMOTE_ADDR");
//	else $ip = "Unknow";
//	return $ip;
//}
//
//echo getClientIP();
//$r = sql_query("SELECT SUM(upload) FROM users");
//$a=mysql_fetch_assoc($r);
//dump($a);
$date=date('Y-m-d');

echo strtotime($date);