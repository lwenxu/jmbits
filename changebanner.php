<?php
/**
 * Created by PhpStorm.
 * User: lwen
 * Date: 17/5/17
 * Time: 06:57
 */
if (!!($_POST['hashCode']==md5(sha1($_POST['time'])."GHacdsnjlJKHad2317498"))){
    $banner=$_FILES['banner'];
    if (!empty($banner)){
        $time=date('Ymdhis');
        if (file_exists("./styles/BambooGreen/banner/banner.jpg")){
            rename("./styles/BambooGreen/banner/banner.jpg","./styles/BambooGreen/banner/banner".$time.".jpg");
        }
        var_dump($banner);
        move_uploaded_file($banner['tmp_name'],"./styles/BambooGreen/banner/banner.jpg");
        if ($banner['error']==0){
            echo "
                        <div class=\"panel panel-success\" >
            	  <div class=\"panel-heading\" >
            			<h3 class=\"panel-title\" > 上传成功！ </h3 >
            	  </div >
            	  <div class=\"panel-body\" >
                         上传成功！
            	  </div >
            </div >
            ";
            header("http://jm.nwu.edu.cn");
        }else{
            echo '
            <div class="panel panel - danger">
            	  <div class="panel - heading">
            			<h3 class="panel - title">上传失败！</h3>
            	  </div>
            	  <div class="panel - body">
            			上传失败！
            	  </div>
            </div>
            ';
        }
    }
}
