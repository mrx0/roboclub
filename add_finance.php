<?php

//add_finance.php
//Добавить платёж

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			if (($finance['add_new'] == 1) || $god_mode){
			
				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
			
				//var_dump($user);
				if ($client != 0){
					
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
								<h2>Добавляем платёж</h2>
								Заполните поля
							</header>';

					echo '
					
						<style>
						.label_desc{
							display: block;
						}
						.error{
							display: none;
						}
						.error_input{
							border: 2px solid #FF0000; 
						}
						</style>	
					
					
							<div id="data">';

					echo '
						<form action="add_finance_f.php">
							
							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">
									<a href="client.php?id='.$client[0]['id'].'" class="ahref">'.$client[0]['full_name'].'</a>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Сумма <i class="fa fa-rub"></i></div>
								<div class="cellRight">
									<input type="text" size="50" name="summ" id="summ" placeholder="0" value="" autocomplete="off">
									<label id="summ_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Тип</div>
								<div class="cellRight">
									<select name="type" id="type">
										<option value="1" selected>Платёж</option>
										<option value="2">Аморт. взнос</option>
									</select>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">За какой месяц</div>
								<div class="cellRight">
									<select name="month" id="month">';
										foreach ($monthsName as $key => $value){
											echo '<option value="'.$key.'" ', ($month === $key) ? 'selected' : '' ,'>'.$value.'</option>';
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
										<option value="'.($year-1).'">'.($year-1).'</option>';
					echo '
										<option value="'.$year.'" selected>'.$year.'</option>';
					echo '
										<option value="'.($year+1).'">'.($year+1).'</option>';
					echo '
									</select>
									<label id="age_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2"></textarea></div>
							</div>
							';
					
			echo '
							<div id="errror"></div>
							<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_finance()>
						</form>';	
				
			echo '
					</div>
				</div>';
				
			//Фунция JS
			
			echo '
				<script type="text/javascript">  
				
				
					function Ajax_add_finance() {
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
										url:"add_finance_f.php",
										statbox:"status",
										method:"POST",
										data:
										{
											client:'.$client[0]['id'].',
											filial:'.$client[0]['filial'].',
											summ:document.getElementById("summ").value,
											type:document.getElementById("type").value,
											
											month:document.getElementById("month").value,
											year:document.getElementById("year").value,
											
											comment:document.getElementById("comment").value,
											
											session_id:'.$_SESSION['id'].',
										},
										success:function(data){
											document.getElementById("status").innerHTML=data;
										}
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

				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
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