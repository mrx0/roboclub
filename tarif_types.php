<?php

//tarif_types.php
//Типы тарифов и сборов

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){
			
			//Получим все типы тарифов и сборов
            $tarif_types_j = array();

            $msql_cnnct = ConnectToDB ();

            $query = "SELECT * FROM `spr_tarif_types`;";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($tarif_types_j, $arr);
                }
            }
            //var_dump ($tarif_types_j);


			echo '
					<div id="status">
						<header>
                            <div class="nav">
                                <a href="options.php" class="b">Настройки</a>
                            </div>
							<h2>Типы тарифов и сборов</h2>
							<a href="add_tarif_type.php" class="b">Добавить</a>
						</header>';

			echo '
						<div id="data">';

            if (!empty($tarif_types_j)){

                $archiv_tarif_types = '';

                echo '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 77%; font-weight:bold;">
                                    Название
                                </div>
                                <div class="cellRight" style="font-size: 77%; font-weight:bold;">
                                    Описание
                                </div>
                                <div class="cellLeft" style="font-size: 77%; font-weight:bold;">
                                    Управление
                                </div>
                            </div>';

				foreach ($tarif_types_j as $tarif_types_item){
				    if ($tarif_types_item['status'] != 9) {
                        echo '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 90%;">
                                    '.$tarif_types_item['name'] . '
                                </div>
                                <div class="cellRight">
                                   '.$tarif_types_item['descr'] . '
                                </div>
                                <div class="cellLeft" style="font-size: 90%;">
                                    <a href="tarif_type_edit.php?id='.$tarif_types_item['id'].'" class=""><img src="img/edit.png" title="Редактировать"></a>
                                    <a href="tarif_type_close.php?id='.$tarif_types_item['id'].'"><img src="img/delete.png" title="Закрыть"></a>
                                </div>
                            </div>';
                    }else{
                        $archiv_tarif_types .= '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 90%;">
                                    '.$tarif_types_item['name'] . '
                                </div>
                                <div class="cellRight">
                                   '.$tarif_types_item['descr'] . '
                                </div>
                                <div class="cellLeft" style="font-size: 90%; color: red;">
                                    <i>в архиве</i>
                                    <a href="open_tarif_type.php?id='.$tarif_types_item['id'].'" style="float: right;"><img src="img/reset.png" title="Открыть"></a>
                                </div>
                            </div>';
                    }

				}

				//var_dump(!empty($archiv_tarif_types));

				if (!empty($archiv_tarif_types)){

                    echo '<div style="font-size: 80%; margin: 15px 0 2px;">Типы, находящиеся в архиве</div>';

                    echo $archiv_tarif_types;
                }

				echo '
						
						</div>
					</div>';

			}else{
                echo '<h1>В базе ничего нет</h1>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>