<?php

//t_surface_name.php
//

	function t_surface_name ($data){
		$t_name = mb_substr($data, 1, 2);
		$t_number = mb_substr($data, 2, 1);
		$t_quarter = mb_substr($data, 1, 1);
		$t_surface = mb_substr($data, 3);
		$rezult = '';
		
		$t_quarters = array(
			1 => array(
				"surface1" => "Вестибулярная поверхность",
				"surface2" => "Медиально контактная поверхность",
				"surface3" => "Нёбная поверхность",
				"surface4" => "Дистально контактная поверхность",
				//"top1" => "Окклюзионно дистальная",
				//"top2" => "Окклюзионно медиальная",
				"top1" => "Окклюзионная",
				"top2" => "Окклюзионная",
				"top12" => "Окклюзионная",
				"root1" => "Корень 1",
				"root2" => "Корень 2",
				"root3" => "Корень 3",
			),
			2 => array(
				"surface1" => "Вестибулярная поверхность",
				"surface2" => "Дистально контактная поверхность",
				"surface3" => "Нёбная поверхность",
				"surface4" => "Медиально контактная поверхность",
				//"top1" => "Окклюзионно медиальная",
				//"top2" => "Окклюзионно дистальная",
				"top1" => "Окклюзионная",
				"top2" => "Окклюзионная",
				"top12" => "Окклюзионная",
				"root1" => "Корень 1",
				"root2" => "Корень 2",
				"root3" => "Корень 3",
			),
			3 => array(
				"surface1" => "Вестибулярная поверхность",
				"surface2" => "Медиально контактная поверхность",
				"surface3" => "Язычная поверхность",
				"surface4" => "Дистально контактная поверхность",
				//"top1" => "Окклюзионно дистальная",
				//"top2" => "Окклюзионно медиальная",
				"top1" => "Окклюзионная",
				"top2" => "Окклюзионная",
				"top12" => "Окклюзионная",
				"root1" => "Корень 1",
				"root2" => "Корень 2",
				"root3" => "Корень 3",
			),
			4 => array(
				"surface1" => "Вестибулярная поверхность",
				"surface2" => "Дистально контактная поверхность",
				"surface3" => "Язычная поверхность",
				"surface4" => "Медиально контактная поверхность",
				//"top1" => "Окклюзионно медиальная",
				//"top2" => "Окклюзионно дистальная",
				"top1" => "Окклюзионная",
				"top2" => "Окклюзионная",
				"top12" => "Окклюзионная",
				"root1" => "Корень 1",
				"root2" => "Корень 2",
				"root3" => "Корень 3",
			),
		);
		
		$rezult .= $t_name.' зуб<br />';
		if (($t_surface == 'top1') && ($t_number > 3)){
			$t_surface = 'top12';
		}
		$rezult .= $t_quarters[$t_quarter][$t_surface];
		
		return $rezult;
	}
		
?>