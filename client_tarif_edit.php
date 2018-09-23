<?php

//client_tarif_edit.php
//Добавить тарифы ребёнку

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if (($clients['edit'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';
            include_once 'functions.php';

            if (isset($_GET['client_id'])){
                $client_j = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
                //var_dump($client_j);

                if ($client_j != 0){
                    echo '
                            <div id="status">
                                <header>
                                    <h2>Определение тарифа ребёнку <a href="client.php?id='.$client_j[0]['id'].'" class="ahref">'.$client_j[0]['full_name'].'</a></h2>
                                </header>';


                    //Получим все тарифы
                    $tarifs_j = array();

                    $msql_cnnct = ConnectToDB ();

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
                    var_dump ($tarifs_j);


                    echo '
                                <div id="data">';
                    echo '
                                    <div id="errrror"></div>';
                    echo '
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Фамилия</div>
                                            <div class="cellRight">
                                                <input type="text" name="f" id="f" value="">
                                                <label id="fname_error" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Имя</div>
                                            <div class="cellRight">
                                                <input type="text" name="i" id="i" value="">
                                                <label id="iname_error" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Отчество</div>
                                            <div class="cellRight">
                                                <input type="text" name="o" id="o" value="">
                                                <label id="oname_error" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Дата рождения</div>
                                            <div class="cellRight">';
                    echo '
                                                <select name="sel_date" id="sel_date">
                                                    <option value="00">00</option>';
                    $i = 1;
                    while ($i <= 31) {
                        echo '
                                                    <option value="'.$i.'">'.$i.'</option>';
                        $i++;
                    }
                    echo '
                                                </select>';
                    // Месяц
                    echo '
                                                <select name="sel_month" id="sel_month">
                                                    <option value="00">---</option>';
                    $month = array(
                        "Январь",
                        "Февраль",
                        "Март",
                        "Апрель",
                        "Май",
                        "Июнь",
                        "Июль",
                        "Август",
                        "Сентябрь",
                        "Октябрь",
                        "Ноябрь",
                        "Декабрь"
                    );
                    foreach ($month as $m => $n) {
                        echo '
                                                    <option value="'.($m + 1).'">'.$n.'</option>';
                    }
                    echo '
                                                </select>';
                    // Год
                    echo '
                                                <select name="sel_year" id="sel_year">
                                                    <option value="0000">0000</option>';
                    $j = 2000;
                    while ($j <= 2020) {
                        echo '
                                                    <option value="'.$j.'">'.$j.'</option>';
                        $j++;
                    }
                    echo '	
                                                </select>';

                    echo '
                                                <label id="sel_date_error" class="error"></label>
                                                <label id="sel_month_error" class="error"></label>
                                                <label id="sel_year_error" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Пол</div>
                                            <div class="cellRight">
                                                <input id="sex" name="sex" value="1" type="radio"> М
                                                <input id="sex" name="sex" value="2" type="radio"> Ж
                                                <label id="sex_error" class="error"></label>
                                            </div>
                                        </div>
            
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Контакты</div>
                                            <div class="cellRight"><textarea name="contacts" id="contacts" cols="35" rows="5"></textarea></div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Комментарий</div>
                                            <div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="5"></textarea></div>
                                        </div>';

                    $filials = SelDataFromDB('spr_office', '', '');
                    echo '
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">Филиал</div>
                                            <div class="cellRight">
                                                <select name="filial" id="filial">
                                                    <option value="0" selected>Выберите филиал</option>';
                    if ($filials != 0){
                        for ($i=0;$i<count($filials);$i++){
                            echo "<option value='".$filials[$i]['id']."'>".$filials[$i]['name']."</option>";
                        }
                    }
                    echo '
                                                </select>
                                                <label id="filial_error" class="error"></label>
                                            </div>
                                        </div>';
                    echo '				
                                        <div id="errror"></div>
                                        <input type="button" class="b" value="Добавить" onclick="Ajax_add_client();">
                                    </form>';

                    echo '
                                </div>
                            </div>
                            
                            <script type="text/javascript">
                                sex_value = 0;
                                $("input[name=sex]").change(function() {
                                    sex_value = $("input[name=sex]:checked").val();
                                });
                            </script>';
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
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