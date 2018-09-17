<?php

//tarifs.php
//Тарифы

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if ($god_mode){

        //Получим все тарифы
        $tarifs_j = array();

        $msql_cnnct = ConnectToDB ();

        //$query = "SELECT * FROM `spr_tarifs`;";

        $query = "SELECT st.*, stt.name AS type_name FROM `spr_tarifs` st
                            LEFT JOIN `spr_tarif_types` stt
                            ON stt.id = st.type;";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($tarifs_j, $arr);
            }
        }
        //var_dump ($tarifs_j);


        echo '
					<div id="status">
						<header>
                            <div class="nav">
                                <a href="options.php" class="b">Настройки</a>
                            </div>
							<h2>Тарифы и сборы</h2>
							<a href="add_tarif.php" class="b">Добавить</a>
						</header>';

        echo '
						<div id="data">';

        if (!empty($tarifs_j)){

            $archiv_tarifs = '';

            echo '
                            <ul style="margin-left:6px;">
							    <li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
								    <div class="cellName" style="width: 300px; text-align: center; background-color: rgba(113, 255, 255, 0.4);">Название</div>
								    <div class="cellText" style="text-align: center; background-color: rgba(113, 255, 255, 0.4);">Описание</div>
								    <div class="cellName" style="text-align: center; background-color: rgba(113, 255, 255, 0.4);">Цена, руб.</div>
								    <div class="cellName" style="width: 300px; text-align: center; background-color: rgba(113, 255, 255, 0.4);">Тип</div>
								    <div class="cellName" style="text-align: center; background-color: rgba(113, 255, 255, 0.4);">Управление</div>
								</li>';

            foreach ($tarifs_j as $tarifs_item){
                if ($tarifs_item['status'] != 9) {
                    echo '
                            <li class="cellsBlock cellsBlockHover">
                                <div class="cellName" style="width: 300px; text-align: center; background-color:#FEFEFE;">
                                    <b>'.$tarifs_item['name'].'</b>
                                </div>
                                <div class="cellText" style="text-align: left; background-color:#FEFEFE;">
                                   '.$tarifs_item['descr'].'
                                </div>
                                <div class="cellName" style="text-align: center; background-color:#FEFEFE;">
                                    '.$tarifs_item['cost'].'
                                </div>
								<div class="cellName" style="width: 300px; text-align: center; background-color:#FEFEFE;">
								    <i>'.$tarifs_item['type_name'].'</i>
								</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">
								    ***
								</div>
                            </li>';
                }else{
                    $archiv_tarifs .= '
                            <div class="cellsBlock2" style="margin: 5px;">
                                <div class="cellLeft" style="font-size: 90%;">
                                    '.$tarifs_item['name'] . '
                                </div>
                                <div class="cellRight">
                                   '.$tarifs_item['descr'] . '
                                </div>
                                <div class="cellLeft" style="font-size: 90%; color: red;">
                                    <i>Тариф в архиве</i>
                                </div>
                            </div>';
                }

            }

            //var_dump(!empty($archiv_tarif_types));

            if (!empty($archiv_tarifs)){

                echo '<div style="font-size: 80%; margin: 15px 0 2px;">Типы, находящиеся в архиве</div>';

                echo $archiv_tarifs;
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