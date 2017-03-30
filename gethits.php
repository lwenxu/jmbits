<?php
/**
 * Created by PhpStorm.
 * User: xpf19
 * Date: 2017/3/30
 * Time: 20:01
 */
if (isset($_POST['hits'])){
$data=file_get_contents('./counter');
$data=json_decode($data,true);
$today_date=strtotime(date('Y-m-d'));
    if ($today_date==$data[0]['date']){
        $data[0]['count']++;
    }else{
        $data[0]['count']=0;
        $data[0]['count']++;
        $data[0]['date']=$today_date;
    }
    $data=json_encode($data);
    file_put_contents('./counter',$data);
}

