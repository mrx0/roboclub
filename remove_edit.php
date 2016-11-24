<?php

//finance_edit.php
//Редактирование карточки клиента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finance['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$remove_j = SelDataFromDB('journal_finance_rem', $_GET['id'], 'id');
			//var_dump($_SESSION);
			
			if ($remove_j !=0){
				
				$year = date("Y");
				$month = date("m");
				
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
				
				echo '
					<div id="status">
						<header>
							<h2>Редактировать перерасчет <a href="remove.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							<!--<a href="finance_edit_date.php?id='.$_GET['id'].'" class="" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: #999; background-color: rgba(252, 252, 0, 0.3);">Изменить дату внесения</a>-->
						</header>';

					echo '
						<div id="data">';
					echo '
						<form action="finance_edit_f.php">
							
							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">
									<a href="client.php?id='.$remove_j[0]['client'].'" class="ahref">'.WriteSearchUser('spr_clients', $remove_j[0]['client'], 'user_full').'</a>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
								<div class="cellRight">'.$remove_j[0]['summ'].'
								</div>
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
							</div>';
									
					echo '					
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight">'.$remove_j[0]['comment'].'</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Удалить перерасчет</div>
								<div class="cellRight">
									<div style="float: right;" class="delFinanceRemItem"><img src="img/delete.png" title="Удалить"></div>
								</div>
							</div>
							
							';
			echo '
						</form>
						<br>';	
				
			echo '
					</div>';
							
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
					

					echo '
					<br><br>';
								
				echo '	
						<div id="errror"></div>				
						<!--<input type="button" class="b" value="Редактировать" onclick="Ajax_edit_finance()">-->
							</form>';	
						echo '
						
						</div>
					</div>';
							
				
					//Фунция JS
					
									
						echo '
							<script type="text/javascript">
								$(document).ready(function(){
									$(\'.delFinanceRemItem\').on(\'click\', function(data){
										var rys = confirm("Вы хотите удалить перерасчёт. \nЕго невозможно будет восстановить. \n\nВы уверены?");
										if (rys){
											var id = $(this).attr(\'clientid\');
											ajax({
												url: "del_FinanceRemItem_f.php",
												method: "POST",
												
												data:
												{
													id: '.$_GET['id'].'
												},
												success: function(req){
													//document.getElementById("request").innerHTML = req;
													alert(req);
													window.location.replace("balance.php?m='.$remove_j[0]['month'].'&y='.$remove_j[0]['year'].'");
												}
											})
										}
									})
								});
							</script>';
						
						
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>