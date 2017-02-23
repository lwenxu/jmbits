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
	while ($cateids[] = mysql_fetch_assoc($res)){

	}
//	$cateids = mysql_fetch_assoc($res);
	foreach ($cateids as $id) {
//		var_dump($_POST['cate_id']);
		if ($_POST['cate_id'] == $id['id']) {
			$res = mysql_query("SELECT id,name FROM categoriessecond WHERE id LIKE '$id[id]%'");
			while($name = mysql_fetch_assoc($res)){
//				var_dump($id);
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