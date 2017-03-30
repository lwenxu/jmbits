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
begin_main_frame();
main_content_start();
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
$last_uploaded=get_last_site_date("upload",$lid);
$last_downloaded=get_last_site_date("download",$lid);
static $count=0;



//获得最后一条记录
$sql=sql_query("SELECT * FROM site_data ORDER BY date DESC LIMIT 2");
while($last[]=mysql_fetch_assoc($sql));


//判断时间，从而判断是否插入
$today_date=strtotime(date('Y-m-d'));
if (isset($_POST['hits'])){
    if ($today_date==$last[0]['date']){
        $count++;
    }else{
        $count=0;
        $count++;
    }
}
$pv=$count;

echo($today_date!=$last[0]['date']);

if ($today_date!=$last[0]['date']){
    $user=intval($register);
    $totalonlinetoday=intval($totalonlinetoday);
    sql_query("INSERT INTO site_data(user,ip,date,pv,upload,download) VALUES ($user,$totalonlinetoday,$today_date,$pv,$all_uploaded,$all_download)") or sqlerr();
}
$sql=sql_query("SELECT * FROM site_data ORDER BY date DESC LIMIT 2");
while($last[]=mysql_fetch_assoc($sql));
//更新本天的数据
$t_upload=$all_uploaded-$last[1]['upload'];
$t_download=$all_download-last[1]['download'];
sql_query("UPDATE site_data  SET t_upload=$t_upload ,t_download=$t_download ORDER BY date DESC LIMIT 1");
$sql=sql_query("SELECT t_upload,t_download FROM site_data ORDER BY date DESC LIMIT 30");
while($var=mysql_fetch_row($sql)){
    $up_down[]=$var;
}
$up_down=array_reverse($up_down);
foreach ($up_down as $item){
    $ups[]=$item[0];
    $downs[]=$item[1];
}
$new_user=$last[0]['user']-$last[1]['user'];

dump($ups);
dump($downs);
echo $new_user;




echo "
    <div class=\"portlet light bordered \">
    <div class=\"portlet-title\">
        <div class=\"caption font - purple - plum\">
        <h3><i class=\"icon-comments\"></i>
            <span class=\"caption-subject font-red-sunglo bold uppercase\"> 站点数据 </span></h3>
        </div>
    </div>
    ";
?>

<table class="table table-bordered">
    <tr>
        <td>今日上传</td>
        <td>今日下载</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
</table>

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