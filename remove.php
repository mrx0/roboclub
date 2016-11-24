<?php

//remove.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$remove_j = SelDataFromDB('journal_finance_rem', $_GET['id'], 'id');
			//var_dump($remove_j);
			
			if ($remove_j != 0){
				echo '
					<div id="status">
						<header>
							<h2>Перерасчёт #'.$remove_j[0]['id'].'</h2>
						</header>';
				if (($finance['see_all'] == 1) || $god_mode){
					
					//Массив с месяцами
					$monthsName = array(
					'01' => 'Январь',
					'02' => 'Февраль',
					'03' => 'Март',
					'04' => 'Апрель',
					'05' => 'Май',
					'06' => 'Июнь',
					'07'=> 'Июль',
					'08' => 'Август',
					'09' => 'Сентябрь',
					'10' => 'Октябрь',
					'11' => 'Ноябрь',
					'12' => 'Декабрь'
					);
					
					$backSummColor = '';
					
					echo '
						<div id="data">';
					echo '

							<div class="cellsBlock2">
								<div class="cellLeft">Клиент</div>
								<div class="cellRight">
									<a href="client.php?id='.$remove_j[0]['client'].'" class="ahref">'.WriteSearchUser('spr_clients', $remove_j[0]['client'], 'user_full').'</a>
								</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
								<div class="cellRight" style="font-weight: bold; text-align: center; '.$backSummColor.'">'.$remove_j[0]['summ'].'</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Из месяц/год</div>
								<div class="cellRight" style="text-align: right;">'.$monthsName[$remove_j[0]['last_month']].'/'.$remove_j[0]['last_year'].'</div>
							</div>	
							
								<div class="cellsBlock2">
								<div class="cellLeft">В месяц/год</div>
								<div class="cellRight" style="text-align: right;">'.$monthsName[$remove_j[0]['month']].'/'.$remove_j[0]['year'].'</div>
							</div>	
							
							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight" style="text-align: right;">';
					$filials = SelDataFromDB('spr_office', $remove_j[0]['filial'], 'offices');
					if ($filials != 0){
						echo '<a href="filial.php?id='.$filials[0]['id'].'" class="ahref">'.$filials[0]['name'].'</a>';	
					}else{
						echo 'Не указан филиал';
					}
					echo '		
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight">'.$remove_j[0]['comment'].'</div>
							</div>
							
							<br>';
							
					echo '
						<span style="font-size: 80%; color: #999;">
							Перерасчёт создан '.date('d.m.y H:i', $remove_j[0]['create_time']).' пользователем 
							<a href="user.php?id='.$remove_j[0]['create_person'].'" class="ahref">'.WriteSearchUser('spr_workers', $remove_j[0]['create_person'], 'user').'</a>
						</span>';
						
					if ($remove_j[0]['last_edit_time'] != 0){
						echo '
						<br>
						<span style="font-size: 80%; color: #999;">
							Перерасчёт редактировался '.date('d.m.y H:i', $remove_j[0]['last_edit_time']).' пользователем 
							<a href="user.php?id='.$remove_j[0]['last_edit_person'].'" class="ahref">'.WriteSearchUser('spr_workers', $remove_j[0]['last_edit_person'], 'user').'</a>
						</span>';
					}
					
					if (($finance['edit'] == 1) || $god_mode){
						echo '
								<br><br>
								<a href="remove_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
					}
					echo '
					</div>';
				}else{
					echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';
?>