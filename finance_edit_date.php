<?php

//finance_edit_date.php
//редактируем дату внесения платежа

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

				echo '
					<div id="status">
						<header>
							<h2>Изменение даты платежа <a href="finance.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							<div style="color: red; font-size: 70%;">Изменяйте дату очень внимательно</div>
						</header>';
				echo '
						<div id="errrror"></div>';
				echo '
						<div id="data">';
				echo '
						<form action="finance_edit_date_f.php">

							<div class="cellsBlock2">
								<div class="cellLeft">Дата</div>
								<div class="cellRight">';
				if ($finance_j[0]['create_time'] != 0){
					//print_r  (getdate($finance_j[0]['create_time']));
					$bdate = getdate($finance_j[0]['create_time']);
				}else{
					$bdate = 0;
				}
				echo '<select name="sel_date" id="sel_date">
						<option value="00">00</option>';
				$i = 1;
				while ($i <= 31) {
					echo "<option value='" . $i . "'", $bdate['mday'] == $i ? ' selected':'' ,">$i</option>";
					$i++;
				}
				echo "</select>";
				// Месяц
				echo '
					<select name="sel_month" id="sel_month">
						<option value="00">---</option>';
				$month = array(
					"Январь",
					"Февраль",
					"Март",
					"Апрель",
					"Май",
					"Июнь",
					"Июль",
					"Август",
					"Сентябрь",
					"Октябрь",
					"Ноябрь",
					"Декабрь"
				);
				foreach ($month as $m => $n) {
					echo "<option value='" . ($m + 1) . "'", ($bdate['mon'] == ($m + 1)) ? ' selected':'' ,">$n</option>";
				}
				echo '</select>';
				// Год
				echo '
					<select name="sel_year" id="sel_year">';
				echo '
						<option value="'.($bdate['year']-1).'">'.($bdate['year']-1).'</option>';
				echo '
						<option value="'.$bdate['year'].'" selected>'.$bdate['year'].'</option>';
				echo '
						<option value="'.($bdate['year']+1).'">'.($bdate['year']+1).'</option>';
				echo '</select> ';

				echo '
									<label id="sel_date_error" class="error"></label>
									<label id="sel_month_error" class="error"></label>
									<label id="sel_year_error" class="error"></label>
								</div>
							</div>
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
						<input type="button" class="b" value="Редактировать" onclick="Ajax_edit_date_finance()">
							</form>';	
						echo '
						
						</div>
					</div>';
							
				
					//Фунция JS
					
					echo '
						<script type="text/javascript">  
							function Ajax_edit_date_finance() {
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
										sel_date:document.getElementById("sel_date").value,
										sel_month:document.getElementById("sel_month").value,
										sel_year:document.getElementById("sel_year").value,
									},
									// тип передачи данных
									dataType: "json",
									// действие, при ответе с сервера
									success: function(data){
										// в случае, когда пришло success. Отработало без ошибок
										if(data.result == \'success\'){   
											//alert(\'форма корректно заполнена\');
											ajax({
												url:"finance_edit_date_f.php",
												statbox:"errrror",
												method:"POST",
												data:
												{
													id: '.$_GET['id'].',
													
													sel_date:document.getElementById("sel_date").value,
													sel_month:document.getElementById("sel_month").value,
													sel_year:document.getElementById("sel_year").value,
													
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