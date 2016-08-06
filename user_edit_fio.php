<?php

//user_edit_fio.php
//Редактирование карточки пользователя ФИО

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$worker = SelDataFromDB('spr_workers', $_GET['id'], 'user');
				//var_dump($_SESSION);
				if ($worker !=0){
					$fio = explode(' ', $worker[0]['full_name']);
					//var_dump($fio);
					
					echo '
						<div id="status">
							<header>
								<h2>Редактировать ФИО пользователя</h2>
							</header>';

					echo '
							<div id="data">';

					echo '
								<form action="user_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">
											Фамилия
										</div>
										<div class="cellRight">
											<input type="text" name="f" id="f" value="'.$fio[0].'">
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Имя
										</div>
										<div class="cellRight">
											<input type="text" name="i" id="i" value="'.$fio[1].'">
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Отчество
										</div>
										<div class="cellRight">
											<input type="text" name="o" id="o" value="'.$fio[2].'">
										</div>
									</div>';
									


									
					echo '					
						
												<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
												<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
												<input type=\'button\' class="b" value=\'Редактировать\' onclick=\'
													ajax({
														url:"user_edit_fio_f.php",
														statbox:"status",
														method:"POST",
														data:
														{
															id:'.$_GET['id'].',
															
															f:document.getElementById("f").value,
															i:document.getElementById("i").value,
															o:document.getElementById("o").value,
														},
														success:function(data){document.getElementById("status").innerHTML=data;}
													})\'
												>
											</form>
									</div>
								</div>';
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