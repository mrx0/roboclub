<?php

//add_tarif.php
//Добавить тариф

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finance['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';

            //Получим все типы тарифов и сборов
            $tarif_types_j = array();

            $msql_cnnct = ConnectToDB ();

            $query = "SELECT * FROM `spr_tarif_types`;";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //array_push($tarif_types_j, $arr);
                    $tarif_types_j[$arr['id']] = $arr;
                }
            }
            //var_dump ($tarif_types_j);

			echo '
				<div id="status">
					<header>
                        <div class="nav">
                            <a href="tarifs.php" class="b">Все тарифы и сборы</a>
                        </div>
						<h2>Добавить тариф</h2>
					</header>';

			echo '
					<div id="data">';

            if (!empty($tarif_types_j)) {

                echo '
						<div id="errrror"></div>';
                echo '
			
                        <div class="cellsBlock2">
                            <div class="cellLeft">Название</div>
                            <div class="cellRight">
                                <input type="text" name="name" id="name" value="">
                                <label id="name_error" class="error"></label>
                            </div>
                        </div>';


                echo '        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Тип</div>
                            <div class="cellRight">';

                foreach ($tarif_types_j as $tarif_types_item){
                    //var_dump($id);
                    //var_dump(count($tarif_types_j)-2);

                    /*if (count($tarif_types_j)-2 == $id) $checked = true;
                    else $checked = false;*/

                    /*if (max(array_keys($tarif_types_j)) == $id) $checked = true;
                    else $checked = false;*/

                    if ($tarif_types_item['status'] != 9) {
                        echo '
                                <input id="type" name="type" class="type type_'.$tarif_types_item['id'].'" value="' . $tarif_types_item['id'] . '" type="radio" >' . $tarif_types_item['name'] . '<br>';
                    }

                }

                echo '
                <label id="type_error" class="error"></label>
                            </div>
                        </div>';

                echo '
                        <div class="cellsBlock2">
                            <div class="cellLeft">Описание</div>
                            <div class="cellRight">
                                <textarea name="descr" id="descr" cols="35" rows="5"></textarea>
                            </div>
                        </div>
	
                        <div id="cost_descr" class="cellsBlock2">
                            <div class="cellsBlock2">
                                <div class="cellLeft">Цена, руб.</div>
                                <div class="cellRight">
                                    <input type="text" id="cost" name="cost" value="">
                                    <label id="cost_error" class="error"></label>
                                </div>
                            </div>
                        </div>';


                echo '				
                        <div id="errror"></div>
                        <input type="button" class="b" value="Добавить" onclick="Ajax_add_tarif()">';
            }else{
                echo '<h1>В базу не добавлены типы тарифов и сборов.</h1>';
            }
			echo '
					</div>
				</div>
				
				<script>
				    //Установим checked в последний radio
				    $(document).ready(function() {
				        
				        var rbval = 0;
				        
				        $(".type").each(function() {
                            //console.log($(this).val());
                            
                            if ($(this).val() > rbval) rbval = $(this).val();
                        });
				        //console.log(rbval);

				        $(".type_"+rbval).prop("checked", true);
				    })
                </script>';
				

				
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>