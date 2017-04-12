<?php
require "include/bittorrent.php";
dbconn(true);
date_default_timezone_set("Asia/Shanghai");

set_time_limit(0);
ignore_user_abort(1);


for ($i=0;$i<10;$i++){
	$date = date('H');
	//获取当天的day_id
	$today_date = strtotime(date('Y-m-d'));
	$result = sql_query("SELECT id FROM site_data WHERE date=$today_date");
	$arr = mysql_fetch_assoc($result);
	$day_id = $arr['id'];

	$today_stamp = strtotime(date('Y-m-d-H'));
	echo $today_stamp;
	//当前的upload总量
	$all_uploaded = get_row_sum_all("users", "uploaded");

	if (mysql_num_rows(sql_query("SELECT * FROM hour_data WHERE data=$today_stamp"))) {
		sql_query("UPDATE hour_data SET upload=$all_uploaded WHERE data=$today_stamp");
	} else {
		sql_query("INSERT INTO hour_data(data,upload,day_id) VALUES ($today_stamp,$all_uploaded,$day_id)") or sqlerr(__FILE__, __LINE__);
	}
	sleep(10*60);
}
ob_flush();
flush();