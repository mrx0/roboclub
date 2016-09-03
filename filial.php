<?php

//filial.php
//Редактирование филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($offices['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$filial = SelDataFromDB('spr_office', $_GET['id'], 'id');
				//var_dump($filial);
				echo '
					<div id="status">
						<header>
							<h2>Филиал</h2>
						</header>';
				echo '
						<div id="data">';
				
				if ($filial !=0){
					if ($filial[0]['close'] == 1){
						$closed = TRUE;
						echo '<div style="margin-bottom: 10px;"><span style= "background: rgba(255,39,119,0.7);">Филиал закрыт</span></div>';
					}else{
						$closed = FALSE;
					}
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$filial[0]['name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">'.$filial[0]['address'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$filial[0]['contacts'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Закрыть</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<a href="close_filial.php?id='.$filial[0]['id'].'&close=1" style="float: right;"><img src="img/delete.png" title="Закрыть"></a>';
					}else{
						echo '
										<a href="close_filial.php?id='.$filial[0]['id'].'&close=1" style="float: right;"><img src="img/reset.png" title="Открыть"></a>';
					}
					echo '
									</div>
								</div>';
					if (!$closed){
						echo '
								<br /><br />
								
								<a href="filial_shed.php?id='.$filial[0]['id'].'" class="b">Расписание филиала</a>';
					}			

					if (!$closed){	
						echo '
								<br /><br />
								<a href="edit_filial.php?id='.$filial[0]['id'].'" class="b">Редактировать</a>';
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