<?php

//client_edit_fio.php
//Редактирование карточки клиента ФИО

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode  || ($clients['edit'] == 1)){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
				//var_dump($_SESSION);
				if ($client !=0){
					echo '
						<div id="status">
							<header>
								<h2>Редактировать ФИО клиента</h2>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errrror"></div>';
					echo '
								<form action="client_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">
											Фамилия
										</div>
										<div class="cellRight">
											<input type="text" name="f" id="f" value="'.$client[0]['f'].'">
											<label id="fname_error" class="error"></label>
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Имя
										</div>
										<div class="cellRight">
											<input type="text" name="i" id="i" value="'.$client[0]['i'].'">
											<label id="iname_error" class="error"></label>
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Отчество
										</div>
										<div class="cellRight">
											<input type="text" name="o" id="o" value="'.$client[0]['o'].'">
											<label id="oname_error" class="error"></label>
										</div>
									</div>';
					echo '					
						
												<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
												<div id="errror"></div>
												<input type="button" class="b" value="Редактировать" onclick="Ajax_edit_fio_client()">
											</form>
									</div>
								</div>';
								
			//Фунция JS
			
			echo '
				<script type="text/javascript">  
				
				
					function Ajax_edit_fio_client() {
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
								fname:document.getElementById("f").value,
								iname:document.getElementById("i").value,
								oname:document.getElementById("o").value
							},
							// тип передачи данных
							dataType: "json",
							// действие, при ответе с сервера
							success: function(data){
								// в случае, когда пришло success. Отработало без ошибок
								if(data.result == \'success\'){   
									//alert(\'форма корректно заполнена\');
									ajax({
										url:"client_edit_fio_f.php",
										statbox:"errrror",
										method:"POST",
										data:
										{
											id:'.$_GET['id'].',
											
											f:document.getElementById("f").value,
											i:document.getElementById("i").value,
											o:document.getElementById("o").value,
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
									document.getElementById("errror").innerHTML=\'<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>\'
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