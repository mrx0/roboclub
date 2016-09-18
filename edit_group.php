<?php

//edit_group.php
//Редактирование группы

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($groups['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
				//var_dump($j_group);
				
				$j_filials = SelDataFromDB('spr_office', '', '');
				$j_workers = SelDataFromDB('spr_workers', '', '');
				$j_age = SelDataFromDB('spr_ages', '', '');
				
				echo '
					<div id="status">
						<header>
							<h2>Редактировать <a href="group.php?id='.$_GET['id'].'" class="ahref">группу</a></h2>
						</header>';
				echo '
						<div id="data">';
				
				if ($j_group !=0){
					if ($j_group[0]['close'] == 1){
						$closed = TRUE;
						echo '<div style="margin-bottom: 10px;"><span style= "background: rgba(255,39,119,0.7);">Группа закрыта</span></div>';
					}else{
						$closed = FALSE;
					}
					echo '
							<form action="edit_group_f.php">
								
								<div class="cellsBlock2">
									<div class="cellLeft">Название группы</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<input type="text" name="name" id="name" value="'.$j_group[0]['name'].'">';
					}else{
						echo $j_group[0]['name'];
					}
					echo '
										<label id="name_error" class="error">
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
									<select name="filial" id="filial" onchange="changeName();">
										<option value="0">Выберите филиал</option>';
						if ($j_filials != 0){
							for ($i=0;$i<count($j_filials);$i++){
								echo '
										<option value="'.$j_filials[$i]['id'].'" ', $j_filials[$i]['id'] == $j_group[0]['filial'] ? ' selected ' : '' ,'>'.$j_filials[$i]['name'].'</option>';
							}
						}else{
							echo 'unknown';
						}
						echo '
									</select>
									<label id="filial_error" class="error">';
					}else{
						$jj_filials = SelDataFromDB('spr_office', $j_group[0]['filial'], 'offices');
						if ($jj_filials != 0){
							echo $j_filials[0]['name'];
						}else{
							echo 'unknown';
						}
					}
					echo '
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Возраст</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<select name="age" id="age" onchange="changeName();">
											<option value="0">Выберите возраст</option>';
						if ($j_age != 0){
							for ($i=0;$i<count($j_age);$i++){
								echo '<option value="'.$j_age[$i]['id'].'"', $j_age[$i]['id'] == $j_group[0]['age'] ? ' selected ' : '' ,'>'.$j_age[$i]['from_age'].'-'.$j_age[$i]['to_age'].' лет</option>';
							}
						}
						echo '
										</select>
										<label id="age_error" class="error">';
					}else{
						$jj_age = SelDataFromDB('spr_ages', $j_group[0]['age'], 'ages');
						if ($jj_age != 0){
							echo $jj_age[0]['from_age'].'-'.$jj_age[0]['to_age'];
						}else{
							echo 'unknown';
						}
					}
					echo '
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Тренер</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<select name="worker" id="worker">
											<option value="0">Нет тренера</option>';
						if ($j_workers != 0){
							for ($i=0;$i<count($j_workers);$i++){
								echo '<option value="'.$j_workers[$i]['id'].'"', $j_workers[$i]['id'] == $j_group[0]['worker'] ? ' selected ' : '' ,'>'.$j_workers[$i]['name'].'</option>';
							}
						}
						echo '
										</select>
										<label id="worker_error" class="error">';
					}else{
						WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user');
					}
					echo '
									</div>
								</div>';
								

								
					echo '			
								<div class="cellsBlock2">
									<div class="cellLeft">Цвет</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<input id="color" class="jscolor" value="'.$j_group[0]['color'].'">';
					}else{
						echo '
										<span style="background-color: '.$j_group[0]['color'].';">'.$j_group[0]['color'].'<span>';
					}
					echo '
									</div>
								</div>';
							
					echo '		
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<textarea name="comment" id="comment" cols="35" rows="5">'.$j_group[0]['comment'].'</textarea>';
					}else{
						echo $j_group[0]['comment'];
					}
					echo '
									</div>
								</div>';
							
					echo '			
								<div class="cellsBlock2">
									<div class="cellLeft">Закрыть</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<a href="close_group.php?id='.$j_group[0]['id'].'&close=1" style="float: right;"><img src="img/delete.png" title="Закрыть"></a>';
					}else{
						echo '
										<a href="close_group.php?id='.$j_group[0]['id'].'&close=1" style="float: right;"><img src="img/reset.png" title="Открыть"></a>';
					}
					echo '
									</div>
								</div>';
								
					if (!$closed){	
						echo '
								<input type=\'button\' class="b" value=\'Редактировать\' onclick=Ajax_edit_group()>
								<script>  
									function changeName(){
										//alert(1);
										//var name = document.getElementById("name").value,
										var filial = document.getElementById("filial").value;
										var age = document.getElementById("age").value;
										
										//alert(filial+age);
										
										var req = "Гр. "+filial+"/"+age;
										
										document.getElementById("name").value = req;
									}
								
								
									function Ajax_edit_group() {
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
												name:document.getElementById("name").value,
												filial:document.getElementById("filial").value,
												age:document.getElementById("age").value,
											},
											// тип передачи данных
											dataType: "json",
											// действие, при ответе с сервера
											success: function(data){
												// в случае, когда пришло success. Отработало без ошибок
												if(data.result == \'success\'){   
													//alert(\'форма корректно заполнена\');
													ajax({
														url:"edit_group_f.php",
														statbox:"status",
														method:"POST",
														data:
														{
															id:'.$j_group[0]['id'].',
															
															name:document.getElementById("name").value,
															filial:document.getElementById("filial").value,
															age:document.getElementById("age").value,
															
															worker:document.getElementById("worker").value,
															
															color:document.getElementById("color").value,
															
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
									  
								</script> 
						
						';
					}
					echo '
							</form>';	
		
				}else{
					echo '<h1>Какая-то ошибка</h1>';
				}
				echo '
						</div>
					</div>';
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