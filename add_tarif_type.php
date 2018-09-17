<?php

//add_tarif.php
//Добавить тариф

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finance['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';

            $tarif_types_j = SelDataFromDB('spr_tarif_types', '', '');
            var_dump($tarif_types_j);

			echo '
				<div id="status">
					<header>
						<h2>Добавить тариф</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';
			echo '
						<div id="errrror"></div>';
			echo '
			
                        <div class="cellsBlock2">
                            <div class="cellLeft">Название</div>
                            <div class="cellRight">
                                <input type="text" name="name" id="name" value="">
                                <label id="fname_error" class="error"></label>
                            </div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Описание</div>
                            <div class="cellRight">
                                <textarea name="descr" id="descr" cols="35" rows="5"></textarea>
                            </div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Тип</div>
                            <div class="cellRight">
                                <input id="type" name="type" value="1" type="radio" checked> Одно занятие (по умолчанию)<br>
                                <!--<span style="font-size: 75%; color: #555;">Применяется, когда цена каждого занятия фиксированная</span><br><br>
                                <input id="type" name="type" value="2" type="radio"> Абонемент <br>
                                <span style="font-size: 75%; color: #555;">Применяется, когда общая сумма и кол-во занятий фиксированны</span>-->
                                <label id="type_error" class="error"></label>
                            </div>
                        </div>
                        

							
                        <div id="cost_descr" class="cellsBlock2">
                        
                        </div>
                        
                            
                        <div id="cost_descr_tarif_type1" style="display: none;">
                            <div class="cellsBlock2">
                                <div class="cellLeft">Цена за 1 занятие</div>
                                <div class="cellRight">
                                    <input type="text" id="cost" name="cost" value="">
                                    <label id="cost_error" class="error"></label>
                                </div>
                            </div>
                        </div>

                        <div id="cost_descr_tarif_type2" style="display: none;">    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Общая стоимость</div>
                                <div class="cellRight">
                                    <input type="text" id="cost" name="cost" value="">
                                    <label id="cost_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Кол-во занятий</div>
                                <div class="cellRight">
                                    <input type="text" id="exercise_count" name="exercise_count" value="">
                                    <label id="exercise_count_error" class="error"></label>
                                </div>
                            </div>
                        </div>';

			echo '				
                        <div id="errror"></div>
                        <input type="button" class="b" value="Добавить" onclick="Ajax_add_tarif()">';
				
			echo '
					</div>
				</div>
				
				<script type="text/javascript">
					sex_value = 0;
					$("input[name=sex]").change(function() {
						sex_value = $("input[name=sex]:checked").val();
					});
				</script>';
				
			//Фунция JS
			
			echo '
				<script type="text/javascript">  
				
				
					function Ajax_add_client() {
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
								oname:document.getElementById("o").value,
								
								sel_date:document.getElementById("sel_date").value,
								sel_month:document.getElementById("sel_month").value,
								sel_year:document.getElementById("sel_year").value,
								
								sex:sex_value,
							},
							// тип передачи данных
							dataType: "json",
							// действие, при ответе с сервера
							success: function(data){
								// в случае, когда пришло success. Отработало без ошибок
								if(data.result == \'success\'){   
									//alert(\'форма корректно заполнена\');
								ajax({
									url:"add_client_f.php",
									statbox:"errrror",
									method:"POST",
									data:
									{
										f:document.getElementById("f").value,
										i:document.getElementById("i").value,
										o:document.getElementById("o").value,
										
										contacts:document.getElementById("contacts").value,
										comment:document.getElementById("comment").value,

										sel_date:document.getElementById("sel_date").value,
										sel_month:document.getElementById("sel_month").value,
										sel_year:document.getElementById("sel_year").value,
										
										filial:document.getElementById("filial").value,
										
										sex:sex_value,
										
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
									document.getElementById("errror").innerHTML=\'<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>\'
								}
							}
						});						
					};  
					  
				</script> ';	
				
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>