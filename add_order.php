<?php

//add_order.php
//Платёж добавить

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finance['add_new'] == 1) || ($finance['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            //require 'variables.php';

			//Если у нас по GET передали клиента
			if (isset($_GET['client_id']) && isset($_GET['filial_id'])){
				$client = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
				if ($client !=0){

                    $invoice_id = 0;

                    if (isset($_GET['invoice_id'])){
                        $invoice_id = $_GET['invoice_id'];
                    }

                    echo '
                    <div id="status">
                        <header>
                            <h2>Новый платёж</h2>
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
								<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								    Плательщик: <a href="client.php?id='.$_GET['client_id'].'" class="ahref">'.WriteSearchUser('spr_clients', $_GET['client_id'], 'user_full').'</a>
							    </li>';
					//Календарик
					echo '
								<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
									<span style="color: rgb(125, 125, 125);">
									    Дата внесения: <input type="text" id="date_in" name="date_in" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date("d").'.'.date("m").'.'.date("Y").'" onfocus="this.select();_Calendar.lcs(this)" 
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
									</span>
								</li>';
					echo '
							</ul>   
					    </header>';

                    echo '
                        <div id="data">';

                    //Филиал
                    //if (isset($_SESSION['filial'])){

                        echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Сумма (руб.) <label id="summ_error" class="error"></label>
                                        </li>
                                        <li style="margin-bottom: 5px;">
									        <input type="text" size="15" name="summ" id="summ" placeholder="Введите сумму" value="" class=""  autocomplete="off">
									    </li>
							        </ul>
								</div>
							</div>';

                            echo '<input type="hidden" id="summ_type" name="summ_type" value="1">';

                        /*echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Способ внесения  <label id="summ_type_error" class="error"></label>
                                        </li>
                                        <li style="font-size: 90%; margin-bottom: 5px;">
                                            <input id="summ_type" name="summ_type" value="1" type="radio" checked> Наличный<br>
                                            <input id="summ_type" name="summ_type" value="2" type="radio"> Безналичный<br><br>
                                            <input type="checkbox" name="org_pay" value="1"> от Организации
									    </li>
								    </ul>
								</div>
							</div>';*/

                        echo '		
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Филиал <label id="filial_error" class="error">
                                        </li>
                                        <li style="font-size: 90%; margin-bottom: 5px;">';

                            $filials = SelDataFromDB('spr_office', '', '');
                            //var_dump($filials);

                            echo "
                                        <select name='filial' id='filial'>
                                             <option value='0' ", $_GET['filial_id'] == 0 ? "selected" : "" ,">Не указано</option>";

                            if ($filials != 0){
                                for ($i=0;$i<count($filials);$i++){
                                    echo "<option value='".$filials[$i]['id']."' ", $_GET['filial_id'] == $filials[$i]['id'] ? "selected" : "" ,">".$filials[$i]['name']."</option>";
                                }
                            }

                            echo '
									    </select>';

                        echo '
                                        
									    </li>
								    </ul>
								</div>
							</div>';

                        echo '
							<div class="cellsBlock2">
								<div class="cellRight">
								    <ul style="margin-left: 6px; margin-bottom: 10px;">
								        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
								            Комментарий
                                        </li>
                                        <li style="font-size: 90%; margin-bottom: 5px;">
                                            <textarea name="comment" id="comment" cols="35" rows="2"></textarea>
									    </li>
								    </ul>
								</div>
							</div>';


                    /*}else{
                        echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
                    }*/


                    echo '
                            <div>
                                <div id="errror"></div>
                                <input type="hidden" id="client_id" name="client_id" value="'.$_GET['client_id'].'">
                                <input type="hidden" id="invoice_id" name="invoice_id" value="'.$invoice_id.'">
                                <input type="button" class="b" value="Сохранить" onclick=" Ajax_order_add(\'add\')">
                            </div>
                        </div>

						
                    </div>
					<!-- Подложка только одна -->
					<div id="overlay"></div>';

                }else{
                    echo '<h1>Такого такого ребёнка нет в базе</h1><a href="index.php">Вернуться на главную</a>';
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