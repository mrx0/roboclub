<?php

//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			
			//var_dump($user);
			if ($client != 0){
				echo '
					<div id="status">
						<header>
							<h2>Карточка клиента #'.$client[0]['id'].'</h2>
						</header>';
				echo '
						<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">
							<div class="cellRight">
								<span style="font-size: 70%;">Быстрый поиск клиента</span><br />
								<input type="text" size="50" name="searchdata_fc" id="search_client" placeholder="Введите первые три буквы для поиска" value="" class="who_fc"  autocomplete="off">
								<div id="search_result_fc2"></div>
							</div>
						</div>';

				echo '
						<div id="data">';


				echo '

							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">'.$client[0]['full_name'].'</div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Дата рождения</div>
								<div class="cellRight">', $client[0]['birthday'] == '-1577934000' ? 'не указана' : date('d.m.Y', $client[0]['birthday']) ,' / <b>'.getyeardiff( $client[0]['birthday']).' лет</b></div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Пол</div>
								<div class="cellRight">';
				if ($client[0]['sex'] != 0){
					if ($client[0]['sex'] == 1){
						echo 'М';
					}
					if ($client[0]['sex'] == 2){
						echo 'Ж';
					}
				}else{
					echo 'не указан';
				}
				echo 
								'</div>
							</div>';
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$client[0]['contacts'].'</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight">'.$client[0]['comments'].'</div>
								</div>';


									
				if (($clients['edit'] == 1) || $god_mode){
					echo '
							<a href="client_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
				}
				echo '
					</div>';

			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>