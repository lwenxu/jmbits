<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

if ($CURUSER["uploadpos"] == 'no')
	stderr($lang_upload['std_sorry'], $lang_upload['std_unauthorized_to_upload'],false);

if ($enableoffer == 'yes')
	$has_allowed_offer = get_row_count("offers","WHERE allowed='allowed' AND userid = ". sqlesc($CURUSER["id"]));
else $has_allowed_offer = 0;
$uploadfreely = user_can_upload("torrents");
$allowtorrents = ($has_allowed_offer || $uploadfreely);
$allowspecial = user_can_upload("music");

if (!$allowtorrents && !$allowspecial)
	stderr($lang_upload['std_sorry'],$lang_upload['std_please_offer'],false);
$allowtwosec = ($allowtorrents && $allowspecial);

$brsectiontype = $browsecatmode;
$spsectiontype = $specialcatmode;
$showsource = (($allowtorrents && get_searchbox_value($brsectiontype, 'showsource')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showsource'))); //whether show sources or not
$showmedium = (($allowtorrents && get_searchbox_value($brsectiontype, 'showmedium')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showmedium'))); //whether show media or not
$showcodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showcodec')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showcodec'))); //whether show codecs or not
$showstandard = (($allowtorrents && get_searchbox_value($brsectiontype, 'showstandard')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showstandard'))); //whether show standards or not
$showprocessing = (($allowtorrents && get_searchbox_value($brsectiontype, 'showprocessing')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showprocessing'))); //whether show processings or not
$showteam = (($allowtorrents && get_searchbox_value($brsectiontype, 'showteam')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showteam'))); //whether show teams or not
$showaudiocodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showaudiocodec')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showaudiocodec'))); //whether show languages or not

stdhead($lang_upload['head_upload']);
if (!is_writable($torrent_dir))
    error_msg('ATTENTION','Torrent directory isn\'t writable. Please contact the administrator about this problem!');
if (!$max_torrent_size)
    error_msg('ATTENTION','Max. Torrent Size not set. Please contact the administrator about this problem!');


?>

    <div class="row">
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
            <div class="portlet light form-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <h2>
                            <i class="glyphicon glyphicon-open font-green"></i>
                            <span class="caption-subject font-green sbold uppercase">种子发布 <span
                                        class="small font-red   ">星号部分为必填</span></span>
                        </h2>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form class="form-horizontal form-bordered" id="compose" enctype="multipart/form-data" action="takeupload.php" method="post" name="upload">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">分类 <font color="red">*</font> </label>
                                <div class="col-md-4">
                                    <?php
                                    if ($allowtorrents) {
	                                    $disablespecial = " onchange=\"disableother('browsecat','specialcat')\"";
	                                    $s = "<select class=\"form-control input-large\" name=\"type\" id=\"browsecat\" " . ($allowtwosec ? $disablespecial : "") . ">\n<option value=\"0\">" . $lang_upload['select_choose_one'] . "</option>\n";
	                                    $cats = genrelist($browsecatmode);
	                                    foreach ($cats as $row)
		                                    $s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
	                                    $s .= "</select>";
                                    } else $s = "";
                                    echo $s;
                                    ?>
                                </div>
                                <div class="col-md-5" id="secondcategory">

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">文件<font color="red"> *</font></label>
                                <div class="col-md-9">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input input-fixed input-medium"
                                                 data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                            <span class="fileinput-new"> 选择文件 </span>
                            <span class="fileinput-exists"> 修改文件 </span>
                            <input name="file" type="file" id="torrent" onchange="getname()"> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists"
                                               data-dismiss="fileinput"> 删除 </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">标题 <font color="red"> *</font></label>
                                <div class="col-md-9">
                                    <input class="form-control inline" id="name" name="name" type="text">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">副标题 </label>
                                <div class="col-md-9">
                                    <input class="form-control " name="small_descr">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">简介 <font color="red"> *</font></label>
                                <div class="col-md-9">
                                    <?php textbbcode("upload", "descr", "", false); ?>
                                </div>
                            </div>
                            <?php
                            $offerres = sql_query("SELECT id, name FROM offers WHERE userid = ".sqlesc($CURUSER[id])." AND allowed = 'allowed' ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
                                if (mysql_num_rows($offerres) > 0) {
	                                $offer = "<div class='col-md-4'><select class='form-control ' name=\"offer\"><option value=\"0\">" . $lang_upload['select_choose_one'] . "</option>";
	                                while ($offerrow = mysql_fetch_array($offerres))
		                                $offer .= "<option value=\"" . $offerrow["id"] . "\">" . htmlspecialchars($offerrow["name"]) . "</option>";
	                                $offer .= "</select></div>";
	                                echo "
	                                  <div class=\"form-group\">
                                        <label class=\"control-label col-md-3\">候选 <font color=\"red\"> *</font></label>
                                        <div class=\"col-md-9\">
                                            $offer
                                        </div>
                                    </div>
	                                ";
                                }
                            ?>

                            <?php if (get_user_class() >= $beanonymous_class) { ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3">匿名上传 </label>
                                    <div class="col-md-9">
                                        <label class="mt-checkbox">
                                            <input name="uplver" value="yes" type="checkbox"> 不要显示我的用户名。
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
	                            <?php
                                    }
                                ?>
                            <div class="toolbox" colspan="2" style="margin-left: 60%">
                                <input id="qr" type="submit" class="btn btn-success" value="<?php echo $lang_upload['submit_upload'] ?>"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
stdfoot();
