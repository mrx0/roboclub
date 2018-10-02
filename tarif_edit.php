<?php


//tarif_edit.php
//Редактирование тарифа

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if (($offices['edit'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';
            include_once 'functions.php';

            $tarif_j = array();

            $msql_cnnct = ConnectToDB ();

            $query = "SELECT st.*, stt.id AS type_id FROM `spr_tarifs` st
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

            if (!empty($tarif_j)){

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
                                    <a href="tarifs.php" class="b">Тарифы</a>
                                </div>
                                <h2>Редактировать тариф</h2>
                            </header>';
                echo '
                            <div id="data">';

                if ($tarif_j[0]['status'] == 9){
                    $closed = TRUE;
                    echo '<div style="margin-bottom: 10px;"><span style= "background: rgba(255,39,119,0.7);">в архиве</span></div>';
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
                                        <input type="text" name="name" id="name" value="'.$tarif_j[0]['name'].'">
                                        <label id="name_error" class="error"></label>';
                }else{
                    echo $tarif_j[0]['name'];
                }
                echo '
                                    </div>
                                </div>';

                echo '        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Тип</div>
                            <div class="cellRight">';

                if (!$closed) {
                    foreach ($tarif_types_j as $tarif_types_item) {

                        $checked = '';
                        $disabled = '';

                        if ($tarif_types_item['status'] == 9) {
                            $disabled = 'disabled';
                        }

                        if ($tarif_types_item['id'] == $tarif_j[0]['type_id']){
                            $checked = 'checked';
                        }

                        echo '
                                    <input id="type" name="type" class="type type_' . $tarif_types_item['id'] . '" value="' . $tarif_types_item['id'] . '" type="radio" ' . $checked . ' ' . $disabled . '>' . $tarif_types_item['name'] . '<br>';

                    }
                }else{
                    echo $tarif_types_j[$tarif_j[0]['type_id']]['name'];
                }

                echo '
                <label id="type_error" class="error"></label>
                            </div>
                        </div>';

                echo '
                                <div class="cellsBlock2">
                                    <div class="cellLeft">Описание</div>
                                    <div class="cellRight">';

                if (!$closed){
                    echo '
                                        <textarea name="descr" id="descr" cols="35" rows="5">'.$tarif_j[0]['descr'].'</textarea>
                                        <label id="descr_error" class="error"></label>';
                }else{
                    echo $tarif_j[0]['descr'];
                }
                echo '
                                    </div>
                                </div>';


                echo '
                                <div class="cellsBlock2">
                                    <div class="cellLeft">Цена, руб.</div>
                                    <div class="cellRight">';

                if (!$closed) {
                    echo '
                                        <input type="text" id="cost" name="cost" value="'.$tarif_j[0]['cost'].'">
                                        <label id="cost_error" class="error"></label>';
                }else{
                    echo $tarif_j[0]['cost'];
                }
                echo '
                                    </div>
                                </div>';




                if (!$closed){
                    echo '
                                <div id="errror"></div>
                                <input type="button" class="b" value="Редактировать" onclick="Ajax_tarif_edit('.$tarif_j[0]['id'].');">';
                }
                echo '
                            </form>';


                echo '
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