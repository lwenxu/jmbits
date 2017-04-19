<?php
/**
 * Created by PhpStorm.
 * User: xpf19
 * Date: 2017/4/19
 * Time: 19:04
 */
require "include/bittorrent.php";
dbconn(true);
set_time_limit(0);
ignore_user_abort(1);
while(true) {
    date_default_timezone_set("Asia/Shanghai");
    $year = date('Y');
    $month = date('m');
    $day = date('d');
    $hour = date('H');
    $upload = get_row_sum_all("users", "uploaded");
    $download = get_row_sum_all("users", "downloaded");
//假如数据库中没有此刻的数据则插入否则更新
    $res = sql_query("SELECT * FROM site ORDER BY id DESC LIMIT 1");
    $result = mysql_fetch_assoc($res);
    $last_id = $result['id'];
    if ($result['hour'] != $hour) {
        $res = sql_query("INSERT INTO site(year,month,day,hour,upload,download) VALUES($year,$month,$day,$hour,$upload,$download)");
    } else {
        $res = sql_query("UPDATE site SET upload=$upload,download=$download WHERE id=$last_id");
    }
    sleep(60*10);
}