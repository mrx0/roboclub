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
						
			if ($journal_groups != 0){
				//var_dump ($journal_groups);
				
				$closed_groups = '';
							
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
									<a href="journal.php?id='.$journal_groups[$i]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: green" title="Журнал группы"><i class="fa fa-calendar"></i></a>
									<a href="filial.php?id='.$filials[0]['id'].'" id="4filter" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.$filial.'</a>
									<a href="filial_shed.php?id='.$filials[0]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgb(182, 82, 227);" title="Расписание филиала"><i class="fa fa-clock-o"></i></a>
									<div class="cellName" style="text-align: center; '.$bg_color.'">'.$age.'</div>
									<a href="user.php?id='.$journal_groups[$i]['worker'].'" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.WriteSearchUser('spr_workers', $journal_groups[$i]['worker'], 'user').'</a>
									<div class="cellName" style="text-align: center; '.$bg_color.'">';
					//Сколько участников есть в группе
					$uch_arr = SelDataFromDB('spr_clients', $journal_groups[$i]['id'], 'client_group');
					if ($uch_arr != 0) $result_html .= count($uch_arr);
					else $result_html .= 0;
					$result_html .= '	
									</div>
									<div class="cellText" style="text-align: left;'.$bg_color.'">'.$journal_groups[$i]['comment'].'</div>';
					if (($groups['edit'] == 1) || $god_mode){
						$result_html .= '
									<div class="cellCosmAct" style="text-align: center"><a href="edit_group.php?id='.$journal_groups[$i]['id'].'"><img src="img/edit.png" title="Редактировать"></a></div>
									<div class="cellCosmAct" style="text-align: center"><a href="close_group.php?id='.$journal_groups[$i]['id'].'&close=1">'.$cls_img.'</a></div>';
					}
					echo '
								</li>';
								
					if ($journal_groups[$i]['close'] == 0){
						echo $result_html;
					}else{
						$closed_groups .= $result_html;
					}
				}
				echo $closed_groups;
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
			
			echo '
						</ul>
					</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>