<?php

//finance_remove.php
//Перенос средств

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finance['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$finance_j = SelDataFromDB('journal_finance', $_GET['id'], 'id');
			//var_dump($_SESSION);
			
			if ($finance_j !=0){
				
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
							<h2>Перенос средств из платежа <a href="finance.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							<div style="color: red; font-size: 70%;">Это действие создаст новый платёж, а в старом платеже изменится сумма</div>
						</header>';

					echo '
						<div id="data">';
					echo '
						<div id="errrror"></div>';
					echo '
						<form action="finance_edit_f.php">
							
							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">
									<a href="client.php?id='.$finance_j[0]['client'].'" class="ahref">'.WriteSearchUser('spr_clients', $finance_j[0]['client'], 'user_full').'</a>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма переноса <i class="fa fa-rub"></i></div>
								<div class="cellRight">
									<input type="text" size="50" name="summ" id="summ" placeholder="0" value="'.$finance_j[0]['summ'].'" autocomplete="off">
									<label id="summ_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Месяц</div>
								<div class="cellRight">
									<select name="month" id="month">';
										foreach ($monthsName as $key => $value){
											echo '<option value="'.$key.'" ', ($finance_j[0]['month'] === $key) ? 'selected' : '' ,'>'.$value.'</option>';
										}
					echo '
									</select>
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Год</div>
								<div class="cellRight">
									<select name="year" id="year">';
					echo '
										<option value="'.($year-1).'" ', ($finance_j[0]['year'] === ($year-1)) ? 'selected' : '' ,'>'.($year-1).'</option>';
					echo '
										<option value="'.$year.'" ', ($finance_j[0]['year'] === ($year)) ? 'selected' : '' ,'>'.$year.'</option>';
					echo '
										<option value="'.($year+1).'"', ($finance_j[0]['year'] === ($year+1)) ? 'selected' : '' ,'>'.($year+1).'</option>';
					echo '
									</select>
									<label id="age_error" class="error"></label>
								</div>
							</div>';
							
					$filials = SelDataFromDB('spr_office', '', '');
					echo '				
									<div class="cellsBlock2">
										<div class="cellLeft">
											Филиал
										</div>
										<div class="cellRight">
											<select name="filial" id="filial">
												<option value="0" selected>Выберите филиал</option>';
					if ($filials !=0){
						for ($i=0;$i<count($filials);$i++){
							echo "<option value='".$filials[$i]['id']."' ", $finance_j[0]['filial'] == $filials[$i]['id'] ? 'selected' : '' ,">".$filials[$i]['name']."</option>";
						}
					}
					echo '
											</select>
										</div>
									</div>';
									
					echo '					
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2">Перенос средств из платежа #'.$_GET['id'].' от '.date('d.m.y', $finance_j[0]['create_time']).' на другой месяц</textarea></div>
							</div>
							';
			echo '
						</form>
						<br>';	
				
			echo '
					</div>';
							
					echo '
						<span style="font-size: 80%; color: #999;">
							Платёж создан '.date('d.m.y H:i', $finance_j[0]['create_time']).' пользователем 
							<a href="user.php?id='.$finance_j[0]['create_person'].'" class="ahref">'.WriteSearchUser('spr_workers', $finance_j[0]['create_person'], 'user').'</a>
						</span>';
						
					if ($finance_j[0]['last_edit_time'] != 0){
						echo '
						<br>
						<span style="font-size: 80%; color: #999;">
							Платёж редактировался '.date('d.m.y H:i', $finance_j[0]['last_edit_time']).' пользователем 
							<a href="user.php?id='.$finance_j[0]['last_edit_person'].'" class="ahref">'.WriteSearchUser('spr_workers', $finance_j[0]['last_edit_person'], 'user').'</a>
						</span>';
					}
					

					echo '
					<br><br>';
								
				echo '	
						<div id="errror"></div>				
						<input type="button" class="b" value="Применить" onclick="Ajax_remove_finance()">
							</form>';	
						echo '
						
						</div>
					</div>';
							
				
					//Фунция JS
					
					echo '
						<script type="text/javascript">  
							function Ajax_remove_finance() {
								// убираем класс ошибок с инпутов
								$(\'input\').each(function(){
									$(this).removeClass(\'error_input\');
								});
								// прячем текст ошибок
								$(\'.error\').hide();
								 
								$.ajax({
									// метод отправки 
									type: "POST",
									// путь до скрипта-обработчика
									url: "ajax_test.php",
									// какие данные будут переданы
									data: {
										summ:document.getElementById("summ").value,
									},
									// тип передачи данных
									dataType: "json",
									// действие, при ответе с сервера
									success: function(data){
										// в случае, когда пришло success. Отработало без ошибок
										if(data.result == \'success\'){   
											//alert(\'форма корректно заполнена\');
											ajax({
												url:"finance_remove_f.php",
												statbox:"errrror",
												method:"POST",
												data:
												{
													id: '.$_GET['id'].',
													
													filial:document.getElementById("filial").value,
													
													summ: document.getElementById("summ").value,
													
													//type: '.$finance_j[0]['type'].',
													
													comment: document.getElementById("comment").value,
													month: document.getElementById("month").value,
													year: document.getElementById("year").value,
													
													session_id:'.$_SESSION['id'].',
												},
												success:function(data){document.getElementById("errrror").innerHTML=data;}
											})
										// в случае ошибок в форме
										}else{
											// перебираем массив с ошибками
											for(var errorField in data.text_error){
												// выводим текст ошибок 
												$(\'#\'+errorField+\'_error\').html(data.text_error[errorField]);
												// показываем текст ошибок
												$(\'#\'+errorField+\'_error\').show();
												// обводим инпуты красным цветом
											   // $(\'#\'+errorField).addClass(\'error_input\');                      
											}
											document.getElementById("errror").innerHTML=\'<span style="color: red">Ошибка, что-то заполнено не так.</span>\'
										}
									}
								});
							};  
							  
						</script> ';	
						
									
						echo '
							<script type="text/javascript">
								$(document).ready(function(){
									$(\'.delFinanceItem\').on(\'click\', function(data){
										var rys = confirm("Вы хотите удалить платёж. \nЕго невозможно будет восстановить. \n\nВы уверены?");
										if (rys){
											var id = $(this).attr(\'clientid\');
											ajax({
												url: "del_FinanceItem_f.php",
												method: "POST",
												
												data:
												{
													id: '.$_GET['id'].'
												},
												success: function(req){
													//document.getElementById("request").innerHTML = req;
													alert(req);
													window.location.replace("finances.php?m='.$finance_j[0]['month'].'&y='.$finance_j[0]['year'].'");
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