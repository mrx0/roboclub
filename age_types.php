<?php

//age_types.php
//Возрастные группы


//!!!!!!
// Добавили в базу
//ALTER TABLE  `spr_ages` ADD  `status` INT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';


require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){
			
			//Получим все возрастьные группы, которые есть
            $age_types_j = array();

            $msql_cnnct = ConnectToDB ();

            $query = "SELECT * FROM `spr_ages`;";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($age_types_j, $arr);
                }
            }
            //var_dump ($age_types_j);


			echo '
					<div id="status">
						<header>
                            <div class="nav">
                                <a href="options.php" class="b">Настройки</a>
                            </div>
							<h2>Возрастные группы</h2>
							<a href="add_age_type.php" class="b">Добавить</a>
						</header>';

			echo '
						<div id="data">';

            if (!empty($age_types_j)){

                $archiv_age_types = '';

                echo '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 77%; font-weight:bold;">
                                    От
                                </div>
                                <div class="cellRight" style="font-size: 77%; font-weight:bold;">
                                    До
                                </div>
                                <div class="cellLeft" style="font-size: 77%; font-weight:bold;">
                                    Управление
                                </div>
                            </div>';

				foreach ($age_types_j as $age_types_item){
				    if ($age_types_item['status'] != 9) {
                        echo '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 90%;">
                                    '.$age_types_item['from_age'] . '
                                </div>
                                <div class="cellRight">
                                   '.$age_types_item['to_age'] . '
                                </div>
                                <div class="cellLeft" style="font-size: 90%;">
                                123
                                </div>
                            </div>';
                    }else{
                        $archiv_tarif_types .= '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 90%;">
                                    '.$age_types_item['from_age'] . '
                                </div>
                                <div class="cellRight">
                                   '.$age_types_item['to_age'] . '
                                </div>
                                <div class="cellLeft" style="font-size: 90%; color: red;">
                                    <i>в архиве</i>
                                </div>
                            </div>';
                    }

				}

				//var_dump(!empty($archiv_tarif_types));

				if (!empty($archiv_age_types)){

                    echo '<div style="font-size: 80%; margin: 15px 0 2px;">Находящиеся в архиве</div>';

                    echo $archiv_age_types;
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