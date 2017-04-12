<?php
/**
 * Created by PhpStorm.
 * User: xpf19
 * Date: 2017/3/30
 * Time: 16:06
 */
require "include/bittorrent.php";
dbconn(true);
require_once(get_langfile_path());
loggedinorreturn(true);
stdhead($lang_index['head_home']);
echo "<script src='./styles/BambooGreen/echarts.min.js'></script>";
begin_main_frame();
main_content_start();
date_default_timezone_set("Asia/Shanghai");
$peasants = number_format(get_row_count("users", "WHERE class=" . UC_PEASANT));
$users = number_format(get_row_count("users", "WHERE class=" . UC_USER));
$powerusers = number_format(get_row_count("users", "WHERE class=" . UC_POWER_USER));
$eliteusers = number_format(get_row_count("users", "WHERE class=" . UC_ELITE_USER));
$crazyusers = number_format(get_row_count("users", "WHERE class=" . UC_CRAZY_USER));
$insaneusers = number_format(get_row_count("users", "WHERE class=" . UC_INSANE_USER));
$veteranusers = number_format(get_row_count("users", "WHERE class=" . UC_VETERAN_USER));
$extremeusers = number_format(get_row_count("users", "WHERE class=" . UC_EXTREME_USER));
$ultimateusers = number_format(get_row_count("users", "WHERE class=" . UC_ULTIMATE_USER));
$nexusmasters = number_format(get_row_count("users", "WHERE class=" . UC_NEXUS_MASTER));
$registered = number_format(get_row_count("users"));
$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
$totalonlinetoday = number_format(get_row_count("users", "WHERE last_access >= " . sqlesc(date("Y-m-d H:i:s", (TIMENOW - 86400)))));
$totalonlineweek = number_format(get_row_count("users", "WHERE last_access >= " . sqlesc(date("Y-m-d H:i:s", (TIMENOW - 604800)))));
$VIP = number_format(get_row_count("users", "WHERE class=" . UC_VIP));
$donated = number_format(get_row_count("users", "WHERE donor = 'yes'"));
$warned = number_format(get_row_count("users", "WHERE warned='yes'"));
$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
$registered_male = number_format(get_row_count("users", "WHERE gender='Male'"));
$registered_female = number_format(get_row_count("users", "WHERE gender='Female'"));
$all_uploaded=get_row_sum_all("users","uploaded");
$all_download=get_row_sum_all("users","downloaded");
//获得最后一条记录的id
$result = sql_query("SELECT id FROM site_data ORDER BY id DESC LIMIT 1");
$arr = mysql_fetch_assoc($result);
$lid = $arr['id'];
$last_uploaded=get_last_site_date("upload",$lid);
$last_downloaded=get_last_site_date("download",$lid);

//获得最后一条记录
$sql=sql_query("SELECT * FROM site_data ORDER BY date DESC LIMIT 2");
while($last[]=mysql_fetch_assoc($sql));


//判断时间，从而判断是否插入
$today_date=strtotime(date('Y-m-d'));
$data=file_get_contents('./counter');
$data=json_decode($data,true);
$pv=$data[0]['count'];

if ($today_date!=$last[0]['date']){
    $totalonlinetoday=intval($totalonlinetoday);
    sql_query("INSERT INTO site_data(user,ip,date,pv,upload,download) VALUES ($registered,$totalonlinetoday,$today_date,$pv,$all_uploaded,$all_download)") or sqlerr();
}
$sql=sql_query("SELECT * FROM site_data ORDER BY date DESC LIMIT 2");
while($last[]=mysql_fetch_assoc($sql));
//更新本天的数据
$t_upload=$all_uploaded-$last[1]['upload'];
$t_download=$all_download-$last[1]['download'];
$am=sql_query("UPDATE site_data  SET user=$registered, upload=$all_uploaded,download=$all_download, t_upload=$t_upload ,t_download=$t_download,pv=$pv ORDER BY date DESC LIMIT 1") or sqlerr();
$sql=sql_query("SELECT t_upload,t_download,date FROM site_data ORDER BY date DESC LIMIT 30");
while($var=mysql_fetch_row($sql)){
    $up_down[]=$var;
}
$up_down=array_reverse($up_down);
foreach ($up_down as $item){
    $ups[]=number_format($item[0]/(1024*1024*1024),3,'.','');
    $downs[]=number_format($item[1]/(1024*1024*1024),3,'.','');
    $date[]=date('d',$item[2]);
    $date_complete[]= date('Y-m-d', $item[2]);
}
$new_user=$last[0]['user']-$last[1]['user'];
$old_user=$last[1]['user'];
$date=json_encode($date,true);
$date_complete=json_encode($date_complete,true);
$ups=json_encode($ups,true);
$downs=json_encode($downs,true);

$month_start=strtotime(date('Y-m'));
$month_end=strtotime(date('Y-m-d'));
$res=sql_query("SELECT SUM(t_upload),SUM(t_download) FROM site_data WHERE date BETWEEN $month_start AND $month_end");
$sums=mysql_fetch_row($res);
$month_upload=number_format($sums[0]/(1024*1024*1024*1024),4,'.','');
$month_download=number_format($sums[1]/(1024*1024*1024*1024),4,'.','');


//获取当天的day_id
$today_date = strtotime(date('Y-m-d'));
$result = sql_query("SELECT id FROM site_data WHERE date=$today_date");
$arr = mysql_fetch_assoc($result);
$day_id = $arr['id'];

//获得最后一条记录的upload总量
$result = sql_query("SELECT upload FROM site_data ORDER BY id DESC LIMIT 1");
$arr = mysql_fetch_assoc($result);
$upload_ye = $arr['upload'];

if (isset($_GET['hour_id'])){
	$today_date =strtotime(date("Y-m-$_GET[hour_id]"));
	$result = sql_query("SELECT id FROM site_data WHERE date=$today_date");
	$arr = mysql_fetch_assoc($result);
	$day_id = $arr['id'];
}
//取出今天的数据
$res = sql_query("SELECT * FROM hour_data WHERE day_id=$day_id ORDER BY data");
while($hour_data[] = mysql_fetch_row($res)){
}
$hours=array();
foreach ($hour_data as $keys=>$columns){
    if(is_array($columns)){
        $hours[date('H', $columns[1])]=$columns[2];
    }
}

for ($j=0;$j<24;$j++){
    $time_fmate[]=(string)$j;
}
$time_fmate=json_encode($time_fmate);

$hours_add=array();

$hour_keys=array_keys($hours);
for ($hk = 0; $hk < count($hours)-1; $hk++){
    if ($hour_keys[$hk]==0){

        $hours_add[]= number_format(($hours[$hour_keys[$hk]] - $last_uploaded) / (1024 * 1024 * 1024), 3, '.', '');
    }else{
	    $hours_add[] = number_format(($hours[$hour_keys[$hk+1]] - $hours[$hour_keys[$hk]]) / (1024 * 1024 * 1024), 3, '.', '');
    }
}
$hours_add=json_encode($hours_add);

echo "
    <div class=\"portlet light bordered \">
    <div class=\"portlet-title\">
        <div class=\"caption font - purple - plum\">
        <h3><i class=\"icon-comments\"></i>
            <span class=\"caption-subject font-red-sunglo bold uppercase\"> 站点数据 </span></h3>
        </div>
    </div>
    
    <div class='container'>
        <div class='row'>
            <div class='col-sm-12'>
                <div id='upload' style='height: 300px;width: 100%'></div>
                <div id='upload_time' style='height: 300px;width: 100%'></div>
                <div id='download' style='height: 300px;width: 100%'></div>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-4'>
                 <div id='user' style='height: 300px;width: 100%' ></div>
            </div>
            <div class='col-sm-4'>
                <div id='updown' style='height: 300px;width: 100%'></div>
            </div>
            <div class='col-sm-4'>
                <div id='user_type' style='height: 300px;width: 100%'></div>
            </div>
        </div>
    </div>
    
    <script>
        var upload=echarts.init(document.getElementById('upload'));
        var   option_up = {
                title: {
                    text: '近30天每日上传量(可点击查看每天各个小时详情)',
                 
                },
                tooltip: {
                    axisPointer:{
                        show:true,
                        type:'line'
                    },
                    formatter:'{a}:{c} GB'
                },
                xAxis: {
                    data: $date
                },
                yAxis: {},
                series: [{
                    name: '上传量',
                    type: 'line',
                    data: $ups
                }]
            }
         upload.setOption(option_up);
        upload.on('click', function (params) {
            window.open('http://127.0.0.1/nwupt/sitedata.php?hour_id='+params.name,'_self');
        });
        
        var upload_time=echarts.init(document.getElementById('upload_time'));
        var   option_up_time = {
                title: {
                    text: '每小时实时上传',
                 
                },
                tooltip: {
                    axisPointer:{
                        show:true,
                        type:'line'
                    },
                    formatter:'{a}:{c} GB'
                },
                xAxis: {
                    data: $time_fmate
                },
                yAxis: {},
                series: [{
                    name: '上传量',
                    type: 'bar',
                    data: $hours_add
                }]
            }
         upload_time.setOption(option_up_time);
        
        
        
        
        
        
        
        
        
        
        
        var download=echarts.init(document.getElementById('download'));
        var   option_down = {
                title: {
                    text: '近30天每日下载量'
                },
                tooltip: {
                    formatter:'日期:{b}<br>{a}:{c} GB'
                },
                legend: {
                    data:['销量']
                },
                xAxis: {
                    data: $date
                },
                yAxis: {},
                series: [{
                    name: '下载量',
                    type: 'line',
                    data: $downs
                }]
            }
         download.setOption(option_down);
        
        
        var user=echarts.init(document.getElementById('user'));
        var   option_user = {
                title: {
                    text: '用户分析'
                },
                tooltip: {
                },
                xAxis: {
                    data: ['老用户','新用户']
                },
                yAxis: {},
                series: [{
                    name: '用户人数',
                    type: 'bar',
                    data: [$old_user,$new_user]
                }]
            }
         user.setOption(option_user);
        
        
        var updown=echarts.init(document.getElementById('updown'));
        var   option_updown = {
                title: {
                    text: '本月数据总量分析'
                },
                tooltip: {
                    formatter:'{a}:{c} TB'
                },
                xAxis: {
                    data: ['上传','下载']
                },
                yAxis: {},
                series: [{
                    name: '数据量',
                    type: 'bar',
                    data: [$month_upload,$month_download]
                }]
            }
         updown.setOption(option_updown);
        
        
        var user_type=echarts.init(document.getElementById('user_type'));
        var   option_user_type = {
            
             title: {
                    text: '用户类型分析'
                },
                tooltip: {
                },
                xAxis: {
                  data: ['Peasant','User','Power User','Elite User','Crazy User','Insane User']

                },
                yAxis: {},
                series: [{
                    name: '用户类型',
                    type: 'bar',
                    data:[
                                $peasants,
                                $users,
                                $powerusers,
                                $eliteusers,
                                $crazyusers,
                                $insaneusers,
                                $veteranusers,
                                $extremeusers,
                                $ultimateusers,
                                $nexusmasters,
                            ]
                }]
        }
                
         user_type.setOption(option_user_type);
    </script>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    ";



?>












<table width="100%" class="table">
    <tr>
        <?php
        twotd($lang_index['row_users_active_today'], $totalonlinetoday);
        twotd($lang_index['row_users_active_this_week'], $totalonlineweek);
        ?>
    </tr>
    <tr>
        <?php
        twotd($lang_index['row_registered_users'], $registered . " / " . number_format($maxusers));
        twotd($lang_index['row_unconfirmed_users'], $unverified);
        ?>
    </tr>
    <tr>
        <?php
        twotd(get_user_class_name(UC_VIP, false, false, true), $VIP);
        twotd($lang_index['row_donors'], $donated);
        ?>
    </tr>
    <tr>
        <?php
        twotd($lang_index['row_warned_users'], $warned);
        twotd($lang_index['row_banned_users'], $disabled);
        ?>
    </tr>
    <tr>
        <?php
        twotd($lang_index['row_male_users'], $registered_male);
        twotd($lang_index['row_female_users'], $registered_female);
        ?>
    </tr>
    <?php
    ?>
    <tr>
        <td colspan="4" class="rowhead">&nbsp;</td>
    </tr>
    <?php
        $torrents = number_format(get_row_count("torrents"));
        $dead = number_format(get_row_count("torrents", "WHERE visible='no'"));
        $seeders = get_row_count("peers", "WHERE seeder='yes'");
        $leechers = get_row_count("peers", "WHERE seeder='no'");
        if ($leechers == 0)
            $ratio = 0;
        else
            $ratio = round($seeders / $leechers * 100);
        $activewebusernow = get_row_count("users", "WHERE last_access >= " . sqlesc(date("Y-m-d H:i:s", (TIMENOW - 900))));
        $activewebusernow = number_format($activewebusernow);
        $activetrackerusernow = number_format(get_single_value("peers", "COUNT(DISTINCT(userid))"));
        $peers = number_format($seeders + $leechers);
        $seeders = number_format($seeders);
        $leechers = number_format($leechers);
        $totaltorrentssize = mksize(get_row_sum("torrents", "size"));
        $totaluploaded = get_row_sum("users", "uploaded");
        $totaldownloaded = get_row_sum("users", "downloaded");
        $totaldata = $totaldownloaded + $totaluploaded;
        ?>
        <tr>
            <?php
            twotd($lang_index['row_torrents'], $torrents);
            twotd($lang_index['row_dead_torrents'], $dead);
            ?>
        </tr>
        <tr>
            <?php
            twotd($lang_index['row_seeders'], $seeders);
            twotd($lang_index['row_leechers'], $leechers);
            ?>
        </tr>
        <tr>
            <?php
            twotd($lang_index['row_peers'], $peers);
            twotd($lang_index['row_seeder_leecher_ratio'], $ratio . "%");
            ?>
        </tr>
        <tr>
            <?php
            twotd($lang_index['row_active_browsing_users'], $activewebusernow);
            twotd($lang_index['row_tracker_active_users'], $activetrackerusernow);
            ?>
        </tr>
        <tr>
            <?php
            twotd($lang_index['row_total_size_of_torrents'], $totaltorrentssize);
            twotd($lang_index['row_total_uploaded'], mksize($totaluploaded));
            ?>
        </tr>
        <tr>
            <?php
            twotd($lang_index['row_total_downloaded'], mksize($totaldownloaded));
            twotd($lang_index['row_total_data'], mksize($totaldata));
            ?>
        </tr>
    <tr>
        <td colspan="4" class="rowhead">&nbsp;</td>
    </tr>
    <?php
        ?>
        <tr>
            <?php
            twotd(get_user_class_name(UC_PEASANT, false, false, true), $peasants);
            twotd(get_user_class_name(UC_USER, false, false, true), $users);
            ?>
        </tr>
        <tr>
            <?php
            twotd(get_user_class_name(UC_POWER_USER, false, false, true), $powerusers);
            twotd(get_user_class_name(UC_ELITE_USER, false, false, true), $eliteusers);
            ?>
        </tr>
        <tr>
            <?php
            twotd(get_user_class_name(UC_CRAZY_USER, false, false, true), $crazyusers);
            twotd(get_user_class_name(UC_INSANE_USER, false, false, true), $insaneusers);
            ?>
        </tr>
        <tr>
            <?php
            twotd(get_user_class_name(UC_VETERAN_USER, false, false, true), $veteranusers);
            twotd(get_user_class_name(UC_EXTREME_USER, false, false, true), $extremeusers);
            ?>
        </tr>
        <tr>
            <?php
            twotd(get_user_class_name(UC_ULTIMATE_USER, false, false, true), $ultimateusers);
            twotd(get_user_class_name(UC_NEXUS_MASTER, false, false, true), $nexusmasters);
            ?>
        </tr>
    </td>
    </tr>
</table>
</div>