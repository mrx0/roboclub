<?php

//index.php
//Главная

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump ($offices);
		echo '
			<header>
				<h1>Координаты филиалов</h1>
			</header>';
			
		if (($workers['add_new'] == 1) || $god_mode){
			echo '
				<a href="add_filial.php" class="b">Добавить</a>
			</header>';			
		}
		echo '
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
		echo '
					<li class="cellsBlock" style="font-weight:bold;">	
						<div class="cellPriority" style="text-align: center"></div>
						<div class="cellOffice" style="text-align: center">Филиал</div>
						<div class="cellAddress" style="text-align: center">Адрес</div>
						<div class="cellText" style="text-align: center">Контакты</div>';
		if (($offices['edit'] == 1) || $god_mode){
			echo '
						<div class="cellCosmAct" style="text-align: center">-</div>
						<div class="cellCosmAct" style="text-align: center">-</div>';
		}
		echo '
					</li>';
		
		include_once 'DBWork.php';
		$filials = SelDataFromDB('spr_office', '', '');
		//var_dump ($filials);
		
		$closed_filials = '';
		
		if ($filials !=0){
			for ($i = 0; $i < count($filials); $i++) {
				$result_html = '';
				
				if ($filials[$i]['close'] == 0){
					$bg_color = '';
					$cls_img = '<img src="img/delete.png" title="Закрыть">';
				}else{
					$bg_color = 'background-color: rgba(161,161,161,1);';
					$cls_img = '<img src="img/reset.png" title="Открыть">';
				}
				
				$result_html .= '
							<li class="cellsBlock">
								<div class="cellPriority" style="text-align: center; background-color: '.$filials[$i]['color'].';"></div>
								<div class="cellOffice" style="text-align: center;'.$bg_color.'" id="4filter"><a href="filial.php?id='.$filials[$i]['id'].'" class="ahref">'.$filials[$i]['name'].'</a></div>
								<div class="cellAddress" style="text-align: left;'.$bg_color.'">'.$filials[$i]['address'].'</div>
								<div class="cellText" style="text-align: left;'.$bg_color.'">'.$filials[$i]['contacts'].'</div>';
				if (($offices['edit'] == 1) || $god_mode){
					$result_html .= '
								<div class="cellCosmAct" style="text-align: center"><a href="edit_filial.php?id='.$filials[$i]['id'].'"><img src="img/edit.png" title="Редактировать"></a></div>
								<div class="cellCosmAct" style="text-align: center"><a href="close_filial.php?id='.$filials[$i]['id'].'&close=1">'.$cls_img.'</a></div>';
				}
				$result_html .= '
							</li>';
							
				if ($filials[$i]['close'] == 0){
					echo $result_html;
				}else{
					$closed_filials .= $result_html;
				}
			}
			echo $closed_filials;
		}

		echo '
				</ul>
			</div>';
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>