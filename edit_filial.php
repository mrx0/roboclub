<?php

// !!!! Если что-то закрыто или кто-то уволен, то не надо их показывать в списках

//edit_filial.php
//Редактирование филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($offices['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$filial = SelDataFromDB('spr_office', $_GET['id'], 'id');
				//var_dump($filial);
				echo '
					<div id="status">
						<header>
							<h2>Редактировать филиал</h2>
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
							<form action="edit_filial_f.php">
						
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<input type="text" name="name" id="name" value="'.$filial[0]['name'].'">';
					}else{
						echo $filial[0]['name'];
					}
					echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<textarea name="address" id="address" cols="35" rows="5">'.$filial[0]['address'].'</textarea>';
					}else{
						echo $filial[0]['address'];
					}
					echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">';
									
					if (!$closed){
						echo '
										<textarea name="contacts" id="contacts" cols="35" rows="5">'.$filial[0]['contacts'].'</textarea>';
					}else{
						if ($filial[0]['close'] == 1){
							echo $filial[0]['contacts'];
						}else{
							echo '-';
						}
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
										<input id="color" class="jscolor" value="'.$filial[0]['color'].'">';
					}else{
						echo '
										<span style="background-color: '.$filial[0]['color'].';">'.$filial[0]['color'].'<span>';
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
								<input type=\'button\' class="b" value=\'Редактировать\' onclick=\'
									ajax({
										url:"edit_filial_f.php",
										statbox:"status",
										method:"POST",
										data:
										{
											id:'.$filial[0]['id'].',
											name:document.getElementById("name").value,
											address:document.getElementById("address").value,
											contacts:document.getElementById("contacts").value,
											color:document.getElementById("color").value,
											session_id:'.$_SESSION['id'].',
										},
										success:function(data){document.getElementById("status").innerHTML=data;
										setTimeout(function () {
										    window.location.replace("filial.php?id='.$filial[0]['id'].'");
                                        }, 700);}
									})\'
								>';
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