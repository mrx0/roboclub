<?php

//client_close.php
//Удаление(блокирование) карточки ребёнка

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

                $client_j = SelDataFromDB('spr_clients', $_GET['id'], 'user');
                //var_dump($age_type_j);

                if ($client_j !=0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="client.php?id='.$_GET['id'].'" class="b">Карточка ребёнка</a>
                                </div>
								<h2>Удаление карточки ребёнка в архив</h2>
							</header>';

                    echo '
							<div id="data">';
                    echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Имя</div>
									<div class="cellRight">';
                    echo $client_j[0]['name'];
                    echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">';
                    if (($client_j[0]['birthday'] == "-1577934000") || ($client_j[0]['birthday'] == 0)){
                        $age = '';
                    }else{
                        $age = getyeardiff( $client_j[0]['birthday']).' лет';
                    }

                    echo '', (($client_j[0]['birthday'] == '-1577934000') || ($client_j[0]['birthday'] == 0)) ? 'не указана' : date('d.m.Y', $client_j[0]['birthday']) ,' / <b>'.$age.'</b>';
                    echo '
									</div>
								</div>';

					echo '
							    <div id="errrror"></div>';


					echo '

								<div id="errror"></div>
								<input type="button" class="b" value="Удалить" onclick="Ajax_del_client('.$_GET['id'].')">
								
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