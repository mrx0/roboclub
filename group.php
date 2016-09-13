<?php

//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			if (($groups['see_all'] == 1) || ($groups['see_own'] == 1) || $god_mode){	
			
				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
				//var_dump($j_group);
			
				echo '
					<div id="status">
						<header>
							<h2>Группа</h2>
						</header>';
				if ($j_group != 0){
					if (($groups['see_all'] == 1) || (($groups['see_own'] == 1) && ($j_group[0]['worker'] == $_SESSION['id'])) || $god_mode){
						if ($j_group[0]['close'] == '1'){
							echo '<span style="color:#EF172F;font-weight:bold;">ЗАКРЫТА</span>';
						}

						echo '
								<div id="data">';

						echo '

										<div class="cellsBlock2">
											<div class="cellLeft">Название группы</div>
											<div class="cellRight" style="background-color: '.$j_group[0]['color'].';">'.$j_group[0]['name'].'</div>
										</div>

										<div class="cellsBlock2">
											<div class="cellLeft">Филиал</div>
											<div class="cellRight">';
						//Филиалы
						$j_filials = SelDataFromDB('spr_office', $j_group[0]['filial'], 'offices');
						
						if ($j_filials != 0){
							echo '<a href="filial.php?id='.$j_group[0]['filial'].'" class="ahref">'.$j_filials[0]['name'].'</a>';
							echo '<a href="filial_shed.php?id='.$j_group[0]['filial'].'" class="ahref" style="float: right; color: rgb(182, 82, 227);"><i class="fa fa-clock-o" title="Расписание филиала"></i></a>';
						}else{
							echo 'unknown';
						}
						
						echo '				
											</div>
										</div>';

						echo '	
										
										<div class="cellsBlock2">
											<div class="cellLeft">Возраст</div>
											<div class="cellRight">';
						//Возрасты
						$ages = SelDataFromDB('spr_ages', $j_group[0]['age'], 'ages');
						if ($ages != 0){
							echo $ages[0]['from_age'].' - '.$ages[0]['to_age'];
						}else{
							echo 'unknown';
						}	
						echo '
											</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Тренер</div>
											<div class="cellRight"><a href="user.php?id='.$j_group[0]['worker'].'" class="ahref">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</a></div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Кол-во чел.</div>
											<div class="cellRight">';
						//Сколько участников есть в группе
						$uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'client_group');
						if ($uch_arr != 0) echo count($uch_arr);
						else echo 0;
						echo '
											</div>
										</div>
										<div class="cellsBlock2">
											<div class="cellLeft">Комментарий</div>
											<div class="cellRight">'.$j_group[0]['comment'].'</div>
										</div>

										<br /><br />';
						if (($groups['edit'] == 1) || $god_mode){
							//если не закрыта
							//if ($j_group[0]['close'] != '1'){
								echo '
											<a href="group_client.php?id='.$_GET['id'].'" class="b">Участники</a>';
						}
						if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
								echo '
											<a href="add_shed_group.php?id='.$_GET['id'].'" class="b">Расписание</a>';
						}
						if (($scheduler['see_all'] == 1)|| ($scheduler['see_own'] == 1) || $god_mode){
								echo '
											<a href="journal.php?id='.$_GET['id'].'" class="b">Журнал</a>';
						}
								echo '
											<br /><br />';
						if (($groups['edit'] == 1) || $god_mode){
							echo '
											<a href="edit_group.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
						}
					}else{
						echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
					}
				}else{
					echo '<span style="color: #EF172F; font-weight: bold;">Такой группы нет</span>';
				}
			}else{
				echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>