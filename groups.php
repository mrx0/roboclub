<?php

//groups.php
//Группы

    require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);
		if (($groups['see_all'] == 1) || ($groups['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Группы</h1>';
					
			if (($groups['edit'] == 1) || $god_mode){	
				echo '
						<div id="closedGroups" style="display: none; color: #999;"></div>';
				echo '						
						<div style="margin-bottom: 15px;"><a href="replacements.php" style="border-bottom: 1px dashed #000080; text-decoration: none; color: #999; background-color: rgba(252, 252, 0, 0.3);">Подмены</a></div>';
				/*echo '						
						<div style="margin-bottom: 15px;"><a href="notingroup.php" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: #999; background-color: rgba(252, 252, 0, 0.3);">Кто не в группе</a></div>';*/
			}		
			if (($groups['add_new'] == 1) || $god_mode){

				echo '
						<a href="add_group.php" class="b">Добавить группу</a>';
			}
			
			$journal_groups = 0;
			
			if (($groups['see_all'] == 1) || $god_mode){
				$journal_groups = SelDataFromDB('journal_groups', '', '');
			}elseif ($groups['see_own'] == 1){
				$journal_groups = SelDataFromDB('journal_groups', $_SESSION['id'], 'worker');
			}
			
			echo '
				</header>';
			
			if (($groups['see_all'] == 1) || $god_mode){	
				echo '
					<p style="margin: 5px 0; padding: 1px; font-size:80%;">
						Быстрый поиск: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>';
			}		
			echo '
				<div id="data">
					<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
						<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Название</div>
							<div class="cellCosmAct" style="text-align: center" title="Журнал группы">-</div>
							<div class="cellCosmAct" style="text-align: center" title="Участники группы">-</div>
							<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Филиал</div>
							<div class="cellCosmAct" style="text-align: center" title="Расписание филиала">-</div>
							<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Возраст</div>
							<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Тренер</div>
							<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Участников</div>
							<div class="cellText" style="text-align: center">Комментарий</div>';
			if (($groups['edit'] == 1) || $god_mode){
				echo '
							<div class="cellCosmAct" style="text-align: center">-</div>
							<div class="cellCosmAct" style="text-align: center">-</div>';
			}
			echo '
						</li>';
				
			$closed_groups = '';
			$closed_groups_count = 0;						
			
			if ($journal_groups != 0){
				//var_dump ($journal_groups);
							
				for ($i = 0; $i < count($journal_groups); $i++) {
					$result_html = '';

					//Если закрыта группа
					if ($journal_groups[$i]['close'] == '1'){
						$bg_color = ' background-color: rgba(161,161,161,1);';
						$cls_img = '<img src="img/reset.png" title="Открыть">';
					}else{
						$bg_color = '';
						$cls_img = '<img src="img/delete.png" title="Закрыть">';						
					}
					
					//Если в группе нет тренера
					if ($journal_groups[$i]['worker'] == 0){
						$trenerValue = '<div class="cellName" style="text-align: center; background-color: rgba(222, 8, 8, 0.51);">не указан</div>';
					}else{
						$trenerValue = '<a href="user.php?id='.$journal_groups[$i]['worker'].'" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.WriteSearchUser('spr_workers', $journal_groups[$i]['worker'], 'user').'</a>';
					}
					
					//Филиалы
					$filials = SelDataFromDB('spr_office', $journal_groups[$i]['filial'], 'offices');
					if ($filials != 0){
						$filial = $filials[0]['name'];
						$filialColor = $filials[0]['color'];
					}else{
						$filial = 'unknown';
						$filialColor = '#FFF';
					}
					
					//Возрасты
					$ages = SelDataFromDB('spr_ages', $journal_groups[$i]['age'], 'ages');
					if ($ages != 0){
						$age = $ages[0]['from_age'].' - '.$ages[0]['to_age'];
					}else{
						$age = 'unknown';
					}
					
					//временная переменная
					$result_html .= '
								<li class="cellsBlock cellsBlockHover">
									<div class="cellPriority" style="text-align: center; background-color: '.$journal_groups[$i]['color'].';"></div>
									<a href="group.php?id='.$journal_groups[$i]['id'].'" class="cellName ahref" style="background-color: '.$filialColor.';">'.$journal_groups[$i]['name'].'</a>
									<a href="journal_new.php?id='.$journal_groups[$i]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: green" title="Журнал группы"><i class="fa fa-calendar"></i></a>
									<a href="group_client.php?id='.$journal_groups[$i]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgba(47, 47, 47, 0.93);" title="Участники группы"><i class="fa fa-users"></i></a>
									<a href="filial.php?id='.$filials[0]['id'].'" id="4filter" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.$filial.'</a>
									<a href="filial_shed.php?id='.$filials[0]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgb(182, 82, 227);" title="Расписание филиала"><i class="fa fa-clock-o"></i></a>
									<div class="cellName" style="text-align: center; '.$bg_color.'">'.$age.'</div>
									'.$trenerValue.'';
					//Сколько участников есть в группе
					$uch_arr = SelDataFromDB('spr_clients', $journal_groups[$i]['id'], 'client_group');
					if ($uch_arr != 0) {
						$result_html .= '
									<div class="cellName" style="text-align: center; '.$bg_color.'">'.count($uch_arr);
					}else{
						$result_html .= '
						<div class="cellName" style="text-align: center;  background-color: rgba(222, 8, 8, 0.51);">0';
					}
					$result_html .= '	
									</div>
									<div class="cellText" style="text-align: left;'.$bg_color.'">'.$journal_groups[$i]['comment'].'</div>';
					if (($groups['edit'] == 1) || $god_mode){
						$result_html .= '
									<div class="cellCosmAct" style="text-align: center"><a href="edit_group.php?id='.$journal_groups[$i]['id'].'"><img src="img/edit.png" title="Редактировать"></a></div>
									<div class="cellCosmAct" style="text-align: center"><a href="close_group.php?id='.$journal_groups[$i]['id'].'&close=1">'.$cls_img.'</a></div>';
					}
					$result_html .= '
								</li>';
								
					if ($journal_groups[$i]['close'] == 0){
						echo $result_html;
					}else{
						$closed_groups .= $result_html;
						$closed_groups_count++;
					}
				}
				echo $closed_groups;
			}else{
				echo '<h1>Нечего показывать.</h1>';
			}

			echo '
					</ul>';

			
			if ($closed_groups_count > 0){
				echo '
					<script type="text/javascript">
						$(document).ready(function(){
							document.getElementById("closedGroups").innerHTML = \'Закрытые группы: '.$closed_groups_count.' шт.\';
							$("#closedGroups").show();
						});
					</script>';
			}
			
			//Подмены
			if (($groups['see_own'] == 1) && !$god_mode && ($groups['see_all'] != 1)){	
				$replacements = SelDataFromDB('journal_replacement', $_SESSION['id'], 'worker');
				if ($replacements != 0){
				
					echo '
						<header style="margin-bottom: 0px;">
							<h2>Подмены</h2>
						</header>';

					echo '
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
								<div class="cellPriority" style="text-align: center"></div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Название</div>
								<div class="cellCosmAct" style="text-align: center" title="Журнал группы">-</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Филиал</div>
								<div class="cellCosmAct" style="text-align: center" title="Расписание филиала">-</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Возраст</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Тренер</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Участников</div>
								<div class="cellText" style="text-align: center">Комментарий</div>';
					echo '
							</li>';
					
					foreach ($replacements as $replacement_value){
						//var_dump ( $replacement_value);
						
						$j_group = SelDataFromDB('journal_groups', $replacement_value['group_id'], 'group');
					
						if ($j_group != 0){
							//var_dump ($j_group);

							$result_html = '';

							//Если закрыта группа
							if ($j_group[0]['close'] == '1'){
								$bg_color = ' background-color: rgba(161,161,161,1);';
								$cls_img = '<img src="img/reset.png" title="Открыть">';
							}else{
								$bg_color = '';
								$cls_img = '<img src="img/delete.png" title="Закрыть">';						
							}
							
							//Если в группе нет тренера
							if ($j_group[0]['worker'] == 0){
								$trenerValue = '<div class="cellName" style="text-align: center; background-color: rgba(222, 8, 8, 0.51);">не указан</div>';
							}else{
								$trenerValue = '<a href="user.php?id='.$j_group[0]['worker'].'" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</a>';
							}
							
							//Филиалы
							$filials = SelDataFromDB('spr_office', $j_group[0]['filial'], 'offices');
							if ($filials != 0){
								$filial = $filials[0]['name'];
								$filialColor = $filials[0]['color'];
							}else{
								$filial = 'unknown';
								$filialColor = '#FFF';
							}
							
							//Возрасты
							$ages = SelDataFromDB('spr_ages', $j_group[0]['age'], 'ages');
							if ($ages != 0){
								$age = $ages[0]['from_age'].' - '.$ages[0]['to_age'];
							}else{
								$age = 'unknown';
							}
							
							//временная переменная
							$result_html .= '
										<li class="cellsBlock cellsBlockHover">
											<div class="cellPriority" style="text-align: center; background-color: '.$j_group[0]['color'].';"></div>
											<a href="group.php?id='.$j_group[0]['id'].'" class="cellName ahref" style="background-color: '.$filialColor.';">'.$j_group[0]['name'].'</a>
											<a href="journal_new.php?id='.$j_group[0]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: green" title="Журнал группы"><i class="fa fa-calendar"></i></a>
											<a href="filial.php?id='.$filials[0]['id'].'" id="4filter" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.$filial.'</a>
											<a href="filial_shed.php?id='.$filials[0]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgb(182, 82, 227);" title="Расписание филиала"><i class="fa fa-clock-o"></i></a>
											<div class="cellName" style="text-align: center; '.$bg_color.'">'.$age.'</div>
											'.$trenerValue.'';
							//Сколько участников есть в группе
							$uch_arr = SelDataFromDB('spr_clients', $j_group[0]['id'], 'client_group');
							if ($uch_arr != 0) {
								$result_html .= '
											<div class="cellName" style="text-align: center; '.$bg_color.'">'.count($uch_arr);
							}else{
								$result_html .= '
								<div class="cellName" style="text-align: center;  background-color: rgba(222, 8, 8, 0.51);">0';
							}
							$result_html .= '	
											</div>
											<div class="cellText" style="text-align: left;'.$bg_color.'">'.$j_group[0]['comment'].'</div>';

							$result_html .= '
										</li>';
										
							if ($j_group[0]['close'] == 0){
								echo $result_html;
							}else{
								
							}
						}
					}
					echo '
					</ul>';
				}
			}
			echo '
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>