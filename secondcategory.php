<?php
/**
 * Created by PhpStorm.
 * User: xpf19
 * Date: 2017/2/9
 * Time: 11:21
 */
require_once("include/bittorrent.php");
if (!empty($_POST)){
	dbconn();
	$res=mysql_query('SELECT id FROM categories');
	$cateids=mysql_fetch_assoc($res);
	foreach ($cateids as $id) {
		if ($_POST['cate_id'] == $id) {
			$res = mysql_query("SELECT id,name FROM categoriessecond WHERE id LIKE '$id%'");
			while($name = mysql_fetch_assoc($res)){
//				var_dump($name);
//				var_dump($name[0]);
				foreach ($name as $key=>$value){
//					echo $key;
					if ($key=='id'){
						echo $value.',';
					}
					if($key == 'name'){
						echo $value.',';
					}
				}
			}
		}
	}
}