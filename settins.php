<?php

//client_edit.php
//Редактирование карточки клиента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){
			
			$settings = SelDataFromDB('spr_settings', '', '');
			//var_dump($settings);
			
			if ($settings !=0){
				echo '
					<div id="status">
						<header>
							<h2>Некоторые общие настройки</h2>
							<span style="color: red; font-size: 90%;">Меняйте эти настройки очень внимательно.</span>
						</header>';

				echo '
						<div id="data">
							<form action="settins_edit_f.php">';

				for ($i = count($settings)-1; $i >= 0; $i--){
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft" style="font-size: 77%; font-weight:bold;">
										'.$settings[$i]['name_ru'].'
										<input type="hidden" class="settValRuName" name="'.$settings[$i]['name'].'" id="'.$settings[$i]['name'].'" value="'.$settings[$i]['name_ru'].'">
									</div>
									<div class="cellRight">
										<input type="text" size="20" class="settVal" name="'.$settings[$i]['name'].'" id="'.$settings[$i]['name'].'" placeholder="0" value="'.$settings[$i]['value'].'" autocomplete="off">
										<label id="'.$settings[$i]['name'].'_error" class="error"></label><br>
										<span style="font-size: 70%;">Действие с <b style="color: rgba(0, 72, 245, 0.78)">'.date('d.m.y H:i', $settings[$i]['time']).'</b></span>
									</div>
								</div>';
				}


								
				echo '
								<div id="errror"></div>				
								<input type="button" class="b" value="Добавить" onclick="Ajax_edit_settings ()">
							</form>';	
						echo '
						
						</div>
					</div>';
							
					echo '
						<script type="text/javascript">  
							function Ajax_edit_settings() {
								// убираем класс ошибок с инпутов
								$(\'input\').each(function(){
									$(this).removeClass(\'error_input\');
								});
								// прячем текст ошибок
								$(\'.error\').hide();
								 
								var items = $(".settVal");
								var items2 = $(".settValRuName");
								var resJournalItems = {};
								var resJournalItemsRuName = {};
													
								$.each(items, function(){
									resJournalItems[this.id] = this.value;
								});
								
								$.each(items2, function(){
									resJournalItemsRuName[this.id] = this.value;
								});
								 
								$.ajax({
									// метод отправки 
									type: "POST",
									// путь до скрипта-обработчика
									url: "ajax_test.php",
									// какие данные будут переданы
									data: {
										admSettings: resJournalItems,
									},
									// тип передачи данных
									dataType: "json",
									// действие, при ответе с сервера
									success: function(data){
										// в случае, когда пришло success. Отработало без ошибок
										if(data.result == \'success\'){   
											//alert(\'форма корректно заполнена\');
											ajax({
												url:"settings_edit_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													admSettings: JSON.stringify(resJournalItems),
													admSettings2: JSON.stringify(resJournalItemsRuName)
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
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>