<?php
//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$user = SelDataFromDB('spr_workers', $_GET['id'], 'user');
			//var_dump($user);
			//$arr_orgs = SelDataFromDB('spr_org', '', '');
			//var_dump($orgs);
			$arr_permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump($permissions);
			$permissions = SearchInArray($arr_permissions, $user[0]['permissions'], 'name');

			
			echo '
				<div id="status">
					<header>
						<h2>Карточка пользователя</h2>
					</header>';
			if ($user[0]['fired'] == '1'){
				echo '<span style="color:#EF172F;font-weight:bold;">УВОЛЕН</span>';
			}

			echo '
					<div id="data">';

			echo '

							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">'.$user[0]['full_name'].'</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Должность</div>
								<div class="cellRight">';
			echo $permissions;
			echo '				
								</div>
							</div>';

			echo '	
							
							<div class="cellsBlock2">
								<div class="cellLeft">Логин</div>
								<div class="cellRight">'.$user[0]['login'].'</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Контакты</div>
								<div class="cellRight">'.$user[0]['contacts'].'</div>
							</div>
							<br>';

			
			//if (($finance['see_all'] == 1) || ($finance['see_own'] == 1) || $god_mode){
			if (($finance['see_all'] == 1) || $god_mode){
				echo '
								<a href="worker_finance.php?worker='.$_GET['id'].'" class="b">История <i class="fa fa-rub"></i></a><br><br>';
			}
			
			if (($workers['edit'] == 1) || $god_mode){
				echo '
								<a href="user_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a><br><br>';
			}
			
			if ($user[0]['permissions'] == 3){
				//Показать группы тренера
				$journal_groups = 0;
				$journal_groups = SelDataFromDB('journal_groups', $_GET['id'], 'worker');
				
				echo '
								<ul style="margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: auto; padding: 7px;">';
								
				echo '				
									<li class="cellsBlock" style="width: auto; text-align: right; font-size: 80%; color: #777; margin-bottom: 10px;">
										Группы, к которым прикреплён тренер
									</li>';
									
				if ($journal_groups != 0){
					//var_dump ($journal_groups);
					
					echo '
									<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellPriority" style="text-align: center"></div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Название</div>
										<div class="cellCosmAct" style="text-align: center" title="Журнал группы">-</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Филиал</div>
										<div class="cellCosmAct" style="text-align: center" title="Расписание филиала">-</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Возраст</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Участников</div>
										<div class="cellText" style="text-align: center">Комментарий</div>';
					if (($groups['edit'] == 1) || $god_mode){
						echo '
										<div class="cellCosmAct" style="text-align: center">-</div>
										<div class="cellCosmAct" style="text-align: center">-</div>';
					}
					
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
										<a href="journal_new.php?id='.$journal_groups[$i]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: green" title="Журнал группы"><i class="fa fa-calendar"></i></a>
										<a href="filial.php?id='.$filials[0]['id'].'" id="4filter" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.$filial.'</a>
										<a href="filial_shed.php?id='.$filials[0]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgb(182, 82, 227);" title="Расписание филиала"><i class="fa fa-clock-o"></i></a>
										<div class="cellName" style="text-align: center; '.$bg_color.'">'.$age.'</div>';
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
						}
					}
					echo $closed_groups;
				}else{
					echo '<h1>У тренера нет групп</h1>';
				}
				echo '
									</li>
								</ul>';		




				//Подмены тренера
				$replacements = SelDataFromDB('journal_replacement', $_GET['id'], 'worker');

				echo '
								<ul style="margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: auto; padding: 7px;">';
								
				echo '				
									<li class="cellsBlock" style="width: auto; text-align: right; font-size: 80%; color: #777; margin-bottom: 10px;">
										Группы, которым тренер назначен на подмену
									</li>';
									
				if ($replacements != 0){
					//var_dump ($replacements);
					
					echo '
									<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellPriority" style="text-align: center"></div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Название</div>
										<div class="cellCosmAct" style="text-align: center" title="Журнал группы">-</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Филиал</div>
										<div class="cellCosmAct" style="text-align: center" title="Расписание филиала">-</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Возраст</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Участников</div>
										<div class="cellText" style="text-align: center">Комментарий</div>';
					if (($groups['edit'] == 1) || $god_mode){
						echo '
										<div class="cellCosmAct" style="text-align: center">-</div>
										<div class="cellCosmAct" style="text-align: center">-</div>';
					}
					
					$closed_groups = '';
								
					foreach ($replacements as $replacement_value){
						
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
											<div class="cellName" style="text-align: center; '.$bg_color.'">'.$age.'</div>';
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
							if (($groups['edit'] == 1) || $god_mode){
								$result_html .= '
											<div class="cellCosmAct" style="text-align: center"><a href="edit_group.php?id='.$j_group[0]['id'].'"><img src="img/edit.png" title="Редактировать"></a></div>
											<div class="cellCosmAct" style="text-align: center"><a href="close_group.php?id='.$j_group[0]['id'].'&close=1">'.$cls_img.'</a></div>';
							}
							$result_html .= '
										</li>';
										
							if ($j_group[0]['close'] == 0){
								echo $result_html;
							}else{
								$closed_groups .= $result_html;
							}
						}
					}
					echo $closed_groups;
				}else{
					echo '<h1>Нет подмен</h1>';
				}

				echo '
									</li>
								</ul>';		
								
			}
			
			
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>