<?php

//finance_remove2.php
//Перенос средств

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finance['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
				//var_dump($client);
			
				if ($client != 0){
				
					$year = $_GET['y'];
					$month = $_GET['m'];
					
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
								<h2>Перенос средств за '.$monthsName[$month].' '.$year.'</h2>
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
										<a href="client.php?id='.$_GET['client'].'" class="ahref">'.$client[0]['full_name'].'</a>
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft" style="font-size: 80%">Сумма переноса <i class="fa fa-rub"></i></div>
									<div class="cellRight">
										<input type="text" size="50" name="summrem" id="summrem" placeholder="0" value="'.$_GET['summ'].'" autocomplete="off">
										<label id="summrem_error" class="error"></label>
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Месяц</div>
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
					/*echo '
											<option value="'.($year-1).'">'.($year-1).'</option>';*/
					echo '
											<option value="'.$year.'" selected>'.$year.'</option>';
					echo '
											<option value="'.($year+1).'">'.($year+1).'</option>';
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
							echo "<option value='".$filials[$i]['id']."' ", $client[0]['filial'] == $filials[$i]['id'] ? 'selected' : '' ,">".$filials[$i]['name']."</option>";
						}
					}
					echo '
										</select>
									</div>
								</div>';
										
					echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2">Перенос средств за '.$monthsName[$month].' '.$year.' на другой месяц</textarea></div>
								</div>';
					echo '
								<!--</form>-->
								<br>';	
					
					/*echo '
							</div>';*/
									
					echo '	
								<div id="errror"></div>				
								<input type="button" class="b" value="Применить" onclick="Ajax_remove_finance2()">
							</form>';	
					echo '
							
							</div>
							</div>';
								
					
					//Фунция JS
					
					echo '
							<script type="text/javascript">  
								function Ajax_remove_finance2() {
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
											summrem:document.getElementById("summrem").value,
										},
										// тип передачи данных
										dataType: "json",
										// действие, при ответе с сервера
										success: function(data){
											// в случае, когда пришло success. Отработало без ошибок
											if(data.result == \'success\'){   
												//alert(\'форма корректно заполнена\');
												ajax({
													url:"finance_remove2_f.php",
													statbox:"errrror",
													method:"POST",
													data:
													{
														client: '.$_GET['client'].',
														
														filial:document.getElementById("filial").value,
														
														summrem: document.getElementById("summrem").value,
														
														comment: document.getElementById("comment").value,
														last_month: "'.$month.'",
														month: document.getElementById("month").value,
														last_year: "'.$year.'",
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