<?php

//add_group.php
//Добавить группу

	require_once 'header.php';
	require_once 'header_tags.php';
	
	if ($enter_ok){
		if (($groups['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			$j_filials = SelDataFromDB('spr_office', '', '');
			$j_workers = SelDataFromDB('spr_workers', '', '');
			$j_age = SelDataFromDB('spr_ages', '', '');

			/*var_dump($j_filials);
			var_dump($j_workers);
			var_dump($j_age);*/
				
			echo '
				<div id="status">
					<header>
						<h2>Добавляем новую группу</h2>
						Заполните поля
					</header>';

			echo '

					<div id="data">';

			echo '
						<form action="add_group_f.php">
							
							<div class="cellsBlock2">
								<div class="cellLeft">Название группы</div>
								<div class="cellRight">
									<input type="text" name="name" id="name" value="Группа">
									<label id="name_error" class="error">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight">
									<select name="filial" id="filial" onchange="changeName();">
										<option value="0" selected>Выберите филиал</option>';
									if ($j_filials != 0){
										for ($i=0;$i<count($j_filials);$i++){
											echo '<option value="'.$j_filials[$i]['id'].'">'.$j_filials[$i]['name'].'</option>';
										}
									}
									echo '
									</select>
									<label id="filial_error" class="error">
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Возраст</div>
								<div class="cellRight">
									<select name="age" id="age" onchange="changeName();">
										<option value="0" selected>Выберите возраст</option>';
									if ($j_age != 0){
										for ($i=0;$i<count($j_age);$i++){
											echo '<option value="'.$j_age[$i]['id'].'">'.$j_age[$i]['from_age'].'-'.$j_age[$i]['to_age'].' лет</option>';
										}
									}
									echo '
									</select>
									<label id="age_error" class="error">
								</div>
							</div>


							<div class="cellsBlock2">
								<div class="cellLeft">Тренер</div>
								<div class="cellRight">
									<select name="worker" id="worker">
										<option value="0" selected>Выберите тренера</option>';
									if ($j_workers != 0){
										for ($i=0;$i<count($j_workers);$i++){
											echo '<option value="'.$j_workers[$i]['id'].'">'.$j_workers[$i]['name'].'</option>';
										}
									}
									echo '
									</select>
									<label id="worker_error" class="error">
								</div>
							</div>	

							<div class="cellsBlock2">
								<div class="cellLeft">Цвет</div>
								<div class="cellRight"><input id="color" class="jscolor" value="FFFFFF"></div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="5"></textarea></div>
							</div>';
					
			echo '
							<div id="errror"></div>
							<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_group()>
						</form>';	
				
			echo '
					</div>
				</div>';
				
			//Фунция JS
			
			echo '
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
				
				
					function Ajax_add_group() {
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
										url:"add_group_f.php",
										statbox:"status",
										method:"POST",
										data:
										{
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

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>