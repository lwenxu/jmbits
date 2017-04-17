<?php
//function d($info){
//    echo "<pre>";
//    print_r($info);
//    echo "<pre>";
//}
//date_default_timezone_set("Asia/Shanghai");
//$hour_data[0][0]=24;
//$hour_data[0][1]=1492444800;
//$hour_data[0][2]=28826913446160;
//$hour_data[0][3]=24;
//$hour_data[0][4]=9;
//$hour_data[1][0]=25;
//$hour_data[1][1]=1492444800;
//$hour_data[1][2]=28826913876160;
//$hour_data[1][3]=24;
//$hour_data[1][4]=10;
//$hour_data[2][0]=26;
//$hour_data[2][1]=1492444800;
//$hour_data[2][2]=28826913996160;
//$hour_data[2][3]=24;
//$hour_data[2][4]=11;
//
//foreach ($hour_data as $columns){
//    if(is_array($columns)){
//        $hours[$columns[4]]=$columns[2];
//    }
//}
//
//$hours_all=array();
//
//for ($i = 0; $i < 25; $i++) {
//    $hours_all[$i]=0;
//}
//foreach ($hours as $k=>$downbit) {
//    for ($i = 0; $i < 24; $i++) {
//        if ($i == $k) {
//            $hours_all[$k] = $downbit;
//        }
//    }
//}
//d($hours_all);
//
//for ($j = 0; $j < 24; $j++){
//    if ($hours_all[$j]!=0){
//        $hours_add[$j]=$hours_all[$j]-$hours_all[$j-1];
//    }else{
//        $hours_add[$j]=0;
//    }
//}
//d($hours_add);

date_default_timezone_set("Asia/Shanghai");
require "include/bittorrent.php";
dbconn(true);

$re=sql_query("select count(*) from site_data");
$k=mysql_fetch_row($re);
print_r($k);
for ($i=76;$i>=1;$i--){
    $time= date('Y-m-d',time()-3600*24*$i);
    sql_query("update site_date set date=$time WHERE id=$i");
}