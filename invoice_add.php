<?php

//invoice_add.php
//Выписываем счёт

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
	
		if (($finance['add_new'] == 1) || ($finance['add_own'] == 1) || $god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';
		
			//require 'variables.php';
		
			//require 'config.php';

			//var_dump($_SESSION);
			//var_dump($_SESSION['invoice_data'][12403][72358]['data']);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if (isset($_GET['client_id']) && isset($_GET['group_id'])){
			
					//if (($finances['add_new'] == 1) || $god_mode){
						//array_push($_SESSION['invoice_data'], $_GET['client']);
						//$_SESSION['invoice_data'] = $_GET['client'];
						
						$sheduler_zapis = array();
						$invoice_j = array();

						$client_j = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
						//var_dump($client_j);

/*                        if (
                            ($client_j[0]['card'] == NULL) ||
                            ($client_j[0]['birthday2'] == '0000-00-00') ||
                            ($client_j[0]['sex'] == 0) ||
                            ($client_j[0]['address'] == NULL)
                        ){
                            echo '<div class="query_neok">В <a href="client.php?id='.$_GET['client_id'].'">карточке пациента</a> не заполнены все необходимые графы.</div>';
                        }else{*/


                            $msql_cnnct = ConnectToDB ();

                            /*$query = "SELECT * FROM `zapis` WHERE `id`='".$_GET['id']."'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($sheduler_zapis, $arr);
                                }
                            }else
                                $sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);

                            //if ($client !=0){
                            if ($sheduler_zapis != 0) {*/

                                if (!isset($_SESSION['invoice_data'][$_GET['client_id']])) {
                                    $_SESSION['invoice_data'][$_GET['client_id']][$_GET['group_id']]['t_number_active'] = 0;
                                    $_SESSION['invoice_data'][$_GET['client_id']][$_GET['group_id']]['discount'] = $discount = 0;
                                    $_SESSION['invoice_data'][$_GET['client_id']][$_GET['group_id']]['data'] = array();
                                }
                                var_dump($_SESSION['invoice_data']);

                                //var_dump($_SESSION);
                                //var_dump($_SESSION['invoice_data'][$_GET['client_id']]['data']);

                                echo '
                                <div id="status">
                                    <header>
                                        
                                        <!--<span style="color: red;">Тестовый режим. Уже сохраняется и даже как-то работает</span>-->
                                        <h2>Новый счёт</h2>';

                                echo '		
                                    </header>';

                                echo '
                                    <ul style="margin-left: 6px; margin-bottom: 10px;">	
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Контрагент: <a href="client.php?id='.$client_j[0]['id'].'" class="ahref">'.$client_j[0]['full_name'].'</a></li>';
                                echo '
                                    </ul>';

                                echo '
                                    <div id="data">';

                                echo '	
                                        <input type="hidden" id="client_id" name="client" value="' . $_GET['client_id'] . '">
                                        <input type="hidden" id="group_id" name="group_id" value="' . $_GET['group_id'] . '">
                                        <input type="hidden" id="t_number_active" name="t_number_active" value="' . $_SESSION['invoice_data'][$_GET['client_id']][$_GET['group_id']]['t_number_active'] . '">';




                                    echo '			
                                                <div  style="display: inline-block; width: 380px; height: 600px;">';

                                    echo '
                                                    <div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100%">
                                                        <ul>
                                                            <li><a href="#price">Тарифы и сборы</a></li>';
                                    echo '
                                                        </ul>
                                                        <div id="price">';

                                    //Прайс

                                    //Быстрый поиск
                                    echo '	
                                                            <div style="margin: 0 0 5px; font-size: 11px; cursor: pointer; text-align: left;">';
                                    //echo $block_fast_filter;
                                    echo '
                                                            </div>';

                                    /*echo '
                                                            <div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
                                                                <span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
                                                            </div>';*/
                                    echo '
                                                            <div style=" width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
                                                                <ul class="ul-tree ul-drop live_filter" id="lasttree">';

                                    //Тарифы тут

                                    //Получим все тарифы
                                    $tarifs_j = array();

                                    $msql_cnnct = ConnectToDB ();

                                    //$query = "SELECT * FROM `spr_tarifs`;";

                                    $query = "SELECT st.*, stt.name AS type_name FROM `spr_tarifs` st
                                            LEFT JOIN `spr_tarif_types` stt
                                            ON stt.id = st.type
                                            WHERE st.status <> '9';";

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                    $number = mysqli_num_rows($res);
                                    if ($number != 0){
                                        while ($arr = mysqli_fetch_assoc($res)){
                                            //array_push($tarifs_j, $arr);
                                            if (!isset($tarifs_j[$arr['type']])) {
                                                $tarifs_j[$arr['type']] = array();
                                            }
                                            $tarifs_j[$arr['type']]['type_name'] = $arr['type_name'];
                                            //$tarifs_j[$arr['type']]['data']  = array();
                                            //array_push($tarifs_j[$arr['type']]['data'], $arr);
                                            $tarifs_j[$arr['type']]['data'][]  = $arr;
                                        }
                                    }
                                    //var_dump ($tarifs_j);
                                    //var_dump ($tarifs_j[7]['data']);

                                    if (!empty($tarifs_j)){
                                        foreach($tarifs_j as $tarif_type => $tarif_data){
                                            echo '
                                                <li style="">
                                                    <div class="drop" style="background-position: 0px 0px;"></div>
                                                    <p class="drop" style="color: rgb(0, 127, 255); background: rgba(187, 187, 187, 0.5);"><b><i>Категория: '.$tarif_data['type_name'].'</i></b></p>';
                                            //var_dump($tarif_data['data']);

                                            foreach($tarif_data['data'] as $tarif_item) {
                                                //var_dump($tarif_item);
                                                echo '
                                                    <li style="cursor: pointer;">
                                                        <p onclick="checkPriceItem(' .$tarif_item['id'] . ', ' . 0 . ')">
                                                            <span class="" style="padding: 0 20px;">' . $tarif_item['name'] . ' </span> <span class="" style="float: right; color: red;">' . $tarif_item['cost'] . ' руб.</span>
                                                        </p>
                                                    </li>';
                                            }
                                            echo '
                                                </li>';
                                        }
                                    }



                                    echo '
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';

                                    //Результат
                                    echo '			
                                            <div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

                                    echo '	
                                                <div id="errror" class="invoceHeader" style="position: relative;">
                                                    <div style="position: absolute; bottom: 0; right: 2px; vertical-align: middle; font-size: 11px;">
                                                        <div>	
                                                            <input type="button" class="b" value="Сохранить наряд" onclick="showInvoiceAdd(' . 0 . ', \'add\')">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div style="">К оплате: <div id="calculateInvoice" style="">0</div> руб.</div>
                                                    </div>
                                                </div>';
                                    /*echo '
                                                    <div style="position: absolute; top: 0; left: 200px; vertical-align: middle; font-size: 11px; width: 300px;">
                                                            <div style="display: inline-block; vertical-align: top;">
                                                                Настройки: 
                                                            </div>
                                                            <div style="display: inline-block; vertical-align: top;">
                                                                <div style="margin-bottom: 2px;">
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                         <div id="spec_koeff" class="settings_text" >Коэфф.</div>
                                                                    </div> /
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                         <div id="guaranteegift" class="settings_text">По гарантии | Подарок</div>
                                                                    </div>
                                                                </div>
                                                                <div style="margin-bottom: 2px;">                                                                    
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                         <div class="settings_text" onclick="clearInvoice();">Очистить всё</div>
                                                                    </div> / ';
                                    echo '
                                                                <div style="margin-bottom: 2px;">
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                        <div id="discounts" class="settings_text">Скидки (Акции)</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';*/

                                    echo '
                                                    <div id="invoice_rezult" style="width: 720px; height: 500px; overflow: scroll; float: none">
                                                    </div>';
                                    echo '
                                                </div>';

                                    echo '
                                            <div>	
                                                <input type="button" class="b" value="Сохранить наряд" onclick="showInvoiceAdd('. 0 . ', \'add\')">
                                            </div>
                                        </div>
                    
                                        <!-- Подложка только одна -->
                                        <div id="overlay"></div>
                                        
                                        
                                        
                                        <script>
                                        
                                            $(document).ready(function(){
            
                                                //получим активный зуб
                                                var t_number_active = document.getElementById("t_number_active").value;
                                                
                                                if (t_number_active != 0){
                                                    //colorizeTButton (t_number_active);
                                                }
                                                
                                                //Кликанье по зубам в счёте
                                                $(".sel_tooth").live("click", function() {
                                                    //получам номер зуба
                                                    var t_number = Number(this.innerHTML);
                                                    
                                                    //addInvoiceInSession(t_number);
                                                });
            
                                                //Кликанье по полости в счёте
                                                $(".sel_toothp").click(function(){
                                                    
                                                    //получам номер полости
                                                    var t_number = 99;
                                                    
                                                    //addInvoiceInSession(t_number);
                                                });
                                                
                                                //fillInvoiseRez(true);
                                            });
                                            
                                        </script>';

				}else{
					echo '<h1>Что-то пошло не так1</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так2</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>