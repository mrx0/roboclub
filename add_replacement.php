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
							<h2>Добавить подмену</h2>
						</header>';
				if ($j_group != 0){
					if (($groups['see_all'] == 1) || (($groups['see_own'] == 1) && ($j_group[0]['worker'] == $_SESSION['id'])) || $god_mode){
						if ($j_group[0]['close'] == '1'){
							echo '<span style="color:#EF172F;font-weight:bold;">ЗАКРЫТА</span>';
						}

						echo '
								<div id="data">';

						echo '
									<form action="add_replacement_f.php">
										<div class="cellsBlock2">
											<div class="cellLeft">Название группы</div>
											<div class="cellRight" style="background-color: '.$j_group[0]['color'].';">
												<a href="group.php?id='.$j_group[0]['id'].'" class="ahref">'.$j_group[0]['name'].'</a>
											</div>
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
							echo 'Не указан';
						}	
						echo '
											</div>
										</div>
										<div class="cellsBlock2">
											<div class="cellLeft">Тренер</div>
											<div class="cellRight">';
						//Если в группе нет тренера
						if ($j_group[0]['worker'] == 0){
							echo '<span style="text-align: center; background-color: rgba(255, 0, 99, 0.52);">не указан</span>';
						}else{
							echo '<a href="user.php?id='.$j_group[0]['worker'].'" class="ahref" style="text-align: center;">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</a>';
						}
						echo '
											</div>
										</div>';
										
						$replacements = SelDataFromDB('journal_replacement', $_GET['id'], 'replacement');
						//var_dump($replacements);
						
						if ($replacements != 0){
							echo '
										<div class="cellsBlock2">
											<div class="cellLeft">Тренер на подмену</div>
											<div class="cellRight">';
											
							foreach ($replacements as $rep_value){
								echo '<a href="user.php?id='.$rep_value['user_id'].'" class="ahref" style="text-align: center;">'.WriteSearchUser('spr_workers', $rep_value['user_id'], 'user').'</a><br>';
							}
							
							if (($groups['edit'] == 1) || $god_mode){
								echo '
											<a href="replacements.php" class="b">Подмены</a>';
							}
							echo '
											</div>
										</div>';
						}
						
						echo '				
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
										<br>';
						
						if ($j_group[0]['close'] == '1'){
						}else{							
							//Добавление подмены
							$j_workers = SelDataFromDB('spr_workers', '', '');
							
							echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Тренер на подмену</div>
										<div class="cellRight">';
								echo '
												<select name="worker" id="worker">
													<option value="0" selected>Не выбрано</option>';
								if ($j_workers != 0){
									for ($i=0;$i<count($j_workers);$i++){
										echo '<option value="'.$j_workers[$i]['id'].'">'.$j_workers[$i]['name'].'</option>';
									}
								}
								echo '
											</select>
											<label id="worker_error" class="error">';
						
							echo '
											</div>
										</div>';
											
											
									echo '
										<br>';
							if (($groups['edit'] == 1) || $god_mode){
								echo '
										<div id="errror"></div>
									<input type="button" class="b" value="Добавить подмену" onclick="Ajax_add_replacement()">
									<script>  
										function Ajax_add_replacement() {
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
													worker:document.getElementById("worker").value,
												},
												// тип передачи данных
												dataType: "json",
												// действие, при ответе с сервера
												success: function(data){
													// в случае, когда пришло success. Отработало без ошибок
													if(data.result == \'success\'){   
														//alert(\'форма корректно заполнена\');
														ajax({
															url:"add_replacement_f.php",
															statbox:"status",
															method:"POST",
															data:
															{
																group_id:'.$j_group[0]['id'].',
																
																worker:document.getElementById("worker").value,

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
						
							}
							
							echo '
								</form>';
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