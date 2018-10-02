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

                $tarif_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT st.*, stt.name AS type_name FROM `spr_tarifs` st
                            LEFT JOIN `spr_tarif_types` stt
                            ON stt.id = st.type
                            WHERE st.id={$_GET['id']}";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($tarif_j , $arr);
                    }
                }
                //var_dump($tarif_j);

                if (!empty($tarif_j )){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="tarifs.php" class="b">Тарифы</a>
                                </div>
								<h2>Возврат тарифа из архива</h2>
							</header>';

                    echo '
							<div id="data">';
                    echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">';
                    echo $tarif_j[0]['name'];
                    echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Описание</div>
									<div class="cellRight">';
                    echo $tarif_j[0]['descr'];
                    echo '
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Цена</div>
									<div class="cellRight">';
                    echo $tarif_j[0]['cost'];
                    echo '
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Тип</div>
									<div class="cellRight">';
                    echo $tarif_j[0]['type_name'];
                    echo '
									</div>
								</div>';

                    echo '
							    <div id="errrror"></div>';


					echo '

								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Вернуть" onclick="Ajax_open_tarif('.$_GET['id'].')">
								
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