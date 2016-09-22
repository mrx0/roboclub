<?php

//add_group.php
//Добавить группу

	require_once 'header.php';
	require_once 'header_tags.php';
	
	if ($enter_ok){
		if ($god_mode){
			include_once 'DBWork.php';
				
			echo '
				<div id="status">
					<header>
						<h2>Добавляем новый шаблон расписания</h2>
						Заполните поля
					</header>';
			echo '
			
					<div id="data">';
			echo '
						<form action="add_shed_templs_f.php">
							
							<div class="cellsBlock2" style="margin-bottom: 20px;">
								<div class="cellLeft">Название шаблона</div>
								<div class="cellRight">
									<input type="text" name="name" id="name" value="Шаблон">
									<label id="name_error" class="error">
								</div>
							</div>
							
							<div style="margin-bottom: 20px;">
								<div class="cellsBlock">
									<div class="cellTime" style="text-align: center; background-color:#CCC; min-width: 80px;">
										<!--<div id="addShedTimes" class="addShedTimes" style="text-align: center">
											<i class="fa fa-plus" style="color: green; cursor: pointer;"></i>
										</div>-->
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">ПН</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">ВТ</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">СР</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">ЧТ</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">ПТ</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">СБ</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">ВС</div>
								</div>';
							
							
			$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
			
			if ($spr_shed_times != 0){

				for ($i = 0; $i < count($spr_shed_times); $i++) {
					echo '
								<div class="cellsBlock">
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										'.$spr_shed_times[$i]['from_time'].' - '.$spr_shed_times[$i]['to_time'].'
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
									<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;">
										<input type="checkbox" class="changeColor">
									</div>
								</div>';
				}
			
				echo '		
							</div>
							<div id="errror"></div>
							<input type="button" class="b" value="Добавить" onclick=Ajax_add_shed_templs()>';
						
			}else{
				echo '<h3>Не заполнен график времени.</h3>';
			}
				
			echo '
						</form>';	
				
			echo '
					</div>
				</div>';
				
			//Фунция JS
			
			echo '
				<script type="text/javascript">
					$(document).ready(function(){
						$(\'.addShedTimes\').on(\'click\', function(data){
							
						})
						
						$(\'.changeColor\').on(\'click\', function(data){
							if (this.parentNode.style.background == ""){
								this.parentNode.style.background = "rgba(0, 255, 0, 0.5)";
							}else{
								this.parentNode.style.background = "";
							}
						})
						
						$(\'.delClientFromGroup\').on(\'click\', function(data){
							var rys = confirm("Вы уверены?");
							if (rys){
								var id = $(this).attr(\'clientid\');
								ajax({
						url: "del_ClientFromGroup_f.php",
						method: "POST",
						
						data:
						{
							id: id,
							session_id: '.$_SESSION['id'].'
						},
						success: function(req){
							//document.getElementById("request").innerHTML = req;
							alert(req);
							location.reload(true);
						}
								})
							}
						})
					});
				</script>';			

			echo '
				<script type="text/javascript">  
				
					function Ajax_add_shed_templs() {
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