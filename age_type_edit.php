<?php


//age_type_edit.php
//Редактирование возрастной группы

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($offices['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

                $age_type_j = SelDataFromDB('spr_ages', $_GET['id'], 'id');
				//var_dump($filial);
				echo '
					<div id="status">
                        <header>
                            <div class="nav">
                                <a href="age_types.php" class="b">Возрастные группы</a>
                            </div>
							<h2>Редактировать возрастную группу</h2>
						</header>';
				echo '
						<div id="data">';
				
				if ($age_type_j !=0){
					if ($age_type_j[0]['status'] == 9){
						$closed = TRUE;
						echo '<div style="margin-bottom: 10px;"><span style= "background: rgba(255,39,119,0.7);">Группа в архиве</span></div>';
					}else{
						$closed = FALSE;
					}
					echo '
							<form action="edit_filial_f.php">
						
								<div class="cellsBlock2">
									<div class="cellLeft">от</div>
									<div class="cellRight">';
					if (!$closed){
						echo '
										<input type="text" name="from_age" id="from_age" value="'.$age_type_j[0]['from_age'].'">';
					}else{
						echo $age_type_j[0]['from_age'];
					}
					echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">до</div>
									<div class="cellRight">';
									
					if (!$closed){
						echo '
										<input type="text" name="to_age" id="to_age" value="'.$age_type_j[0]['to_age'].'">';
					}else{
                        echo $age_type_j[0]['to_age'];
					}
					echo '
									</div>
								</div>';

					if (!$closed){	
						echo '
						        <div id="errror"></div>
								<input type="button" class="b" value="Редактировать" onclick="Ajax_age_type_edit('.$age_type_j[0]['id'].');">';
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