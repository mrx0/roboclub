<?php

//open_tarif_type.php
//восстановление типа тарифов

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$tarif_type_j = SelDataFromDB('spr_tarif_types', $_GET['id'], 'id');
				//var_dump($age_type_j);
				
				if ($tarif_type_j !=0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="tarif_types.php" class="b">Типы тарифов и сборов</a>
                                </div>
								<h2>Возврат типа тарифов и сборов из архива</h2>
							</header>';

                    echo '
							<div id="data">';
                    echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">';
                    echo $tarif_type_j[0]['name'];
                    echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Описание</div>
									<div class="cellRight">';
                    echo $tarif_type_j[0]['descr'];
                    echo '
									</div>
								</div>';
					echo '
							    <div id="errrror"></div>';


					echo '

								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Вернуть" onclick="Ajax_open_tarif_type('.$_GET['id'].')">
								
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