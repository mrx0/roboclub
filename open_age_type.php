<?php

//open_age_type.php
//восстановление возрастной группы

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$age_type_j = SelDataFromDB('spr_ages', $_GET['id'], 'id');
				//var_dump($age_type_j);
				
				if ($age_type_j !=0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="age_types.php" class="b">Возрастные группы</a>
                                </div>
								<h2>Возврат возрастной группы из архива</h2>
							</header>';


                    echo '
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                 <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                     Группа: от '.$age_type_j[0]['from_age'].' до '.$age_type_j[0]['to_age'].'
                                 </li> 
                            </ul>';

					echo '
							<div id="data">';
					echo '
							    <div id="errrror"></div>';


					echo '

								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Вернуть" onclick="Ajax_open_age_type('.$_GET['id'].')">
								
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