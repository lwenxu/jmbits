<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();

stdhead($lang_faq['head_faq']);
//TODO  缓存关闭修改完成后必须还原
$Cache->new_page('faq', 900, true);
//TODO    以上为注释的缓存
//TODO    完成
if (!$Cache->get_page())
{
$Cache->add_whole_row();
//make_folder("cache/" , get_langfolder_cookie());
//cache_check ('faq');
	$lang_id = get_guest_lang_id();
	$is_rulelang = get_single_value("language", "rule_lang", "WHERE id = " . sqlesc($lang_id));
	if (!$is_rulelang) {
		$lang_id = 6; //English
	}
	$res = sql_query("SELECT `id`, `link_id`, `question`, `flag` FROM `faq` WHERE `type`='categ' AND `lang_id` = " . sqlesc($lang_id) . " ORDER BY `order` ASC");
	while ($arr = mysql_fetch_array($res)) {
		$faq_categ[$arr[link_id]][title] = $arr[question];
		$faq_categ[$arr[link_id]][flag] = $arr[flag];
		$faq_categ[$arr[link_id]][link_id] = $arr[link_id];
	}

	$res = sql_query("SELECT `id`, `link_id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`='item' AND `lang_id` = " . sqlesc($lang_id) . " ORDER BY `order` ASC");
	while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
		$faq_categ[$arr[categ]][items][$arr[id]][question] = $arr[question];
		$faq_categ[$arr[categ]][items][$arr[id]][answer] = $arr[answer];
		$faq_categ[$arr[categ]][items][$arr[id]][flag] = $arr[flag];
		$faq_categ[$arr[categ]][items][$arr[id]][link_id] = $arr[link_id];
	}
	if (isset($faq_categ)) {
		begin_main_frame();
		echo "
		<div class=\"panel panel-success\" >
		     <div class=\"panel-heading\">
		           <h2 class=\"panel-title\" style='font-size: 33px;text-align: center'>". $lang_faq['text_welcome_to'] . $SITENAME."</h2>
		     </div>
		     <div class=\"panel-body\" style='margin-left:220px'>". $lang_faq['text_welcome_content_one'] . $lang_faq['text_welcome_content_two']."</div>
	    </div>
		";
		echo "<div class='faq-content-container' style='background-color: #eef1f5;margin-top: 10px'>
				<div class='row'>
					<div class='col-lg-6 col-md-6 col-sm-6 col-xs-6' style='margin-top: 20px'>
	";//-----start col-1-6--------//
		foreach ($faq_categ as $id => $temp) {
			if ($faq_categ[$id][flag] == "1" && $id<5) {
				echo "<div class=\"faq-section \">
	                                            <h2 class=\"faq-title uppercase font-blue\">" . $faq_categ[$id]['title'] . "</h2>
	                                            <div class=\"panel-group accordion faq-content\" id=".$id.">";
				if (array_key_exists("items", $faq_categ[$id])) {
					foreach ($faq_categ[$id][items] as $id2 => $temp) {
				echo "
	                                                <div class=\"panel panel-default\">
	                                                    <div class=\"panel-heading\">
	                                                        <h4 class=\"panel-title\">
	                                                            <i class=\"fa fa-circle\"></i>
	                                                            <a class=\"accordion-toggle collapsed\" data-toggle=\"collapse\" data-parent=#".$id." href=#id".$faq_categ[$id][items][$id2][link_id]. ">" . $faq_categ[$id][items][$id2][question] . "</a>";
				echo                                            "
	                                                        </h4>
	                                                    </div>
	                                                    <div id=id".$faq_categ[$id][items][$id2][link_id]." class=\"panel-collapse collapse\" aria-expanded=\"false\" style=\"height: 0px;\">
	                                                        <div class=\"panel-body\">";
						echo $faq_categ[$id][items][$id2][answer];
	                                                        echo "
															</div></div></div>
	                                                    ";
																}
	                                                            }
	                                                        echo "
	                                            </div>
	                                        </div>";
			}
		}
		echo "		</div>";//-----end col-1-6--------//


		echo "      <div class='col-lg-6 col-md-6 col-sm-6 col-xs-6' style='margin-top: 20px'>";//-----start col-2-6--------//
		foreach ($faq_categ as $id => $temp) {
			if ($faq_categ[$id][flag] == "1" && $id > 4) {
				echo "<div class=\"faq-section \">
	                                            <h2 class=\"faq-title uppercase font-blue\">" . $faq_categ[$id]['title'] . "</h2>
	                                            <div class=\"panel-group accordion faq-content\" id=" . $id . ">";
				if (array_key_exists("items", $faq_categ[$id])) {
					foreach ($faq_categ[$id][items] as $id2 => $temp) {
						echo "
	                                                <div class=\"panel panel-default\">
	                                                    <div class=\"panel-heading\">
	                                                        <h4 class=\"panel-title\">
	                                                            <i class=\"fa fa-circle\"></i>
	                                                            <a class=\"accordion-toggle collapsed\" data-toggle=\"collapse\" data-parent=#" . $id . " href=#id" . $faq_categ[$id][items][$id2][link_id] . ">" . $faq_categ[$id][items][$id2][question] . "</a>";
						echo "
	                                                        </h4>
	                                                    </div>
	                                                    <div id=id" . $faq_categ[$id][items][$id2][link_id] . " class=\"panel-collapse collapse\" aria-expanded=\"false\" style=\"height: 0px;\">
	                                                        <div class=\"panel-body\">";
						echo $faq_categ[$id][items][$id2][answer];
//	                                                            <p> Duis autem vel eum iriure dolor in hendrerit in vulputate. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut. </p>
						echo "
															</div></div></div>
	                                                    ";
					}
				}
				echo "
	                                            </div>
	                                        </div>";
			}
		}
		echo "		</div>";//-----end col-2-6--------//
		echo "  </div>";
		echo "</div>";

	end_frame();

}
end_main_frame();
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
//cache_save ('faq');
stdfoot();
?>
