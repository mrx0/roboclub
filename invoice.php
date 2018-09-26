<?php

//invoice.php
//Счёт

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($finance['see_all'] == 1) || ($finance['see_own'] == 1) || $god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';

            //require 'variables.php';
			
			require 'config.php';

            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

			//var_dump($_SESSION);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if (isset($_GET['id'])){
					
					$invoice_j = SelDataFromDB('journal_invoice', $_GET['id'], 'id');
					
					if ($invoice_j != 0){
						//var_dump($invoice_j);
						//array_push($_SESSION['invoice_data'], $_GET['client']);
						//$_SESSION['invoice_data'] = $_GET['client'];
                        //var_dump($invoice_j[0]['closed_time'] == 0);

						$invoice_ex_j = array();

                        $msql_cnnct = ConnectToDB ();

                        //Группы
                        $groups_j = array();

                        $query = "SELECT j_gr.name AS group_name, j_gr.color AS color, s_o.name AS office_name FROM `journal_groups` j_gr
                            LEFT JOIN `spr_office` s_o ON j_gr.filial = s_o.id
                            WHERE j_gr.id='{$invoice_j[0]['group_id']}';";
                        //var_dump($query);

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $groups_j['group_name'] = $arr['group_name'];
                                $groups_j['color'] = $arr['color'];
                                $groups_j['office_name'] = $arr['office_name'];
                            }
                        }
                        //var_dump($groups_j);

                        /*if (!empty($groups_j)){

                        }*/

						echo '
							<div id="status">
								<header>

									<h2>Счёт #'.$_GET['id'].'';

							//Изменить дату внесения
							if (($finance['see_all'] == 1) || $god_mode){
								if ($invoice_j[0]['status'] != 9){
									echo '
												<a href="invoice_time_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Изменить дату"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
								}
							}
							if (($finance['close'] == 1) || $god_mode){
								if ($invoice_j[0]['status'] != 9){
									echo '
												<a href="invoice_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
								}
							}

							echo '			
										</h2>';

							if ($invoice_j[0]['status'] == 9){
								echo '<i style="color:red;">Счёт удалён (заблокирован).</i><br>';
							}


							echo '
										<div class="cellsBlock2" style="margin-bottom: 10px;">
											<span style="font-size:80%;  color: #555;">';

                            //echo 'Врач:'.WriteSearchUser('spr_workers', $invoice_j[0]['worker_id'], 'user', true).'<br>';

							if (($invoice_j[0]['create_time'] != 0) || ($invoice_j[0]['create_person'] != 0)){
								echo '
													Добавлен: '.date('d.m.y H:i' ,strtotime($invoice_j[0]['create_time'])).'<br>
													Автор: '.WriteSearchUser('spr_workers', $invoice_j[0]['create_person'], 'user', true).'<br>';
							}else{
								echo 'Добавлен: не указано<br>';
							}
							echo '
											</span>
										</div>';



							echo '
									</header>';

                            $t_f_data_db = array();
                            $cosmet_data_db = array();

                            $back_color = '';

                            $summ = 0;
                            $summins = 0;


							//Счёт

							//$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id`='".$_GET['id']."'";
							//!!! пробуем JOIN
							//$query = "SELECT * FROM `journal_invoice_ex` LEFT JOIN `journal_invoice_ex_mkb` USING(`invoice_id`, `ind`) WHERE `invoice_id`='".$_GET['id']."';";
							$query = "SELECT * FROM `journal_invoice_ex` WHERE `invoice_id`='".$_GET['id']."';";
							//var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
							$number = mysqli_num_rows($res);
							if ($number != 0){
								while ($arr = mysqli_fetch_assoc($res)){
									if (!isset($invoice_ex_j[$arr['ind']])){
										$invoice_ex_j[$arr['ind']] = array();
										array_push($invoice_ex_j[$arr['ind']], $arr);
									}else{
										array_push($invoice_ex_j[$arr['ind']], $arr);
									}
								}
							}/*else
								$invoice_ex_j = 0;*/
							//var_dump ($invoice_ex_j);

							//сортируем зубы по порядку
                            if (!empty($invoice_ex_j)){
							    ksort($invoice_ex_j);
                            }


							echo '
								<div id="data">';

							echo '			
									<div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

							echo '	
										<div id="errror" class="invoceHeader" style="">
                                             <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                <div>
                                                    <div style="">Контрагент: <a href="client.php?id='.$invoice_j[0]['client_id'].'" class="ahref">'.WriteSearchUser('spr_clients', $invoice_j[0]['client_id'], 'user_full').'</a></div>
                                                </div>
                                                <div>
                                                    <div style="background-color: '. $groups_j['color'].'">Группа: <a href="group.php?id='.$invoice_j[0]['group_id'].'" class="ahref"><b>'.$groups_j['group_name'].'</b> [<i>'.$groups_j['office_name'].'</i>]</a></div>
                                                </div>
                                                <div style="margin: 20px;">
                                                    <div style="">Сумма: <div id="calculateInvoice" style="">'.$invoice_j[0]['summ'].'</div> руб.</div>
                                                </div>
                                                ';

                            echo '
											</div> 
                                            <div style="display: inline-block; width: 300px; vertical-align: top;">
                                                <div>
                                                    <div style="">Оплачено: <div id="calculateInvoice" style="color: #333;">'.$invoice_j[0]['paid'].'</div> руб.</div>
                                                </div>';
                            if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                if ($invoice_j[0]['status'] != 9) {
                                    echo '
                                                <div>
                                                    <div style="display: inline-block;">Осталось внести: <div id="calculateInvoice" style="">' . ($invoice_j[0]['summ'] - $invoice_j[0]['paid']) . '</div> руб.</div>
                                                </div>
                                                <div>
                                                    <div style="display: inline-block;"><a href="payment_add.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Оплатить</a></div>
                                                </div>';
                                }
							}
                            if ($invoice_j[0]['summ'] != $invoice_j[0]['paid']) {
                                echo '
                                                <div style="color: red; ">
                                                    Счёт не закрыт (оплачен не полностью)
                                                </div>';
                            }
                            if ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) {
                                if ($invoice_j[0]['closed_time'] == 0){
                                    /*echo '
                                                <div>
                                                    <div style="display: inline-block; color: red;">Счёт оплачен, но не закрыт. Если счёт <br><b>не страховой</b>, перепроведите оплаты или обратитесь к руководителю.</div>                                                    <!--<div style="display: inline-block;"><div class="b" onclick="alert('.$invoice_j[0]['id'].');">Закрыть</div></div>-->
                                                </div>';*/
                                }else{
                                    echo '
                                                <div style="margin-top: 5px;">
                                                    <div style="display: inline-block; color: green;">Счёт закрыт</div>
                                                    <div style="display: inline-block;">'.date('d.m.y', strtotime($invoice_j[0]['closed_time'])).'</div>
                                                </div>';
                                }

                                if (($invoice_j[0]['type'] == 5) || ($invoice_j[0]['type'] == 6)) {
                                    echo '
                                            <div style="margin-top: 5px;">
                                                <div style="display: inline-block;"><a href="fl_calculation_add3.php?invoice_id=' . $invoice_j[0]['id'] . '" class="b">Внести расчётный лист</a></div>
                                            </div>';
                                }

                            }
                            echo '
                                            </div>';


							echo '
										</div>';




							echo '
										<div id="invoice_rezult" style="float: none; width: 850px;">';

							echo '
											<div class="cellsBlock">
												<div class="cellCosmAct" style="font-size: 80%; text-align: center;">';

                            echo '
                                                    <i><b>№</b></i>';

							echo '
												</div>
												<div class="cellText2" style="font-size: 100%; text-align: center;">
													<i><b>Наименование</b></i>
												</div>';
							echo '
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
													<i><b>Цена, руб.</b></i>
												</div>
												<!--<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Коэфф.</b></i>
												</div>-->
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Кол-во</b></i>
												</div>
												<!--<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Скидка</b></i>
												</div>
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
													<i><b>Гар.</b></i>
												</div>-->
												<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
													<i><b>Всего, руб.</b></i>
												</div>
											</div>';

											
							if (!empty($invoice_ex_j)) {

                                foreach ($invoice_ex_j as $ind => $invoice_data) {

                                    //var_dump($invoice_data);
                                    echo '
                                        <div class="cellsBlock">
                                            <div class="cellCosmAct toothInInvoice" style="text-align: center;">';

                                        echo $ind+1;

                                    echo '
                                            </div>';


                                    foreach ($invoice_data as $item) {
                                        //var_dump($item);

                                        //часть прайса
                                        //if (!empty($invoice_data)){

                                        //foreach ($invoice_data as $key => $items){
                                        echo '
                                                <div class="cellsBlock" style="font-size: 100%;" >
                                                <!--<div class="cellCosmAct" style="">
                                                    -
                                                </div>-->
                                                    <div class="cellText2" style="">';

                                        //Хочу имя позиции в тарифах
                                        $arr = array();
                                        $rez = array();

                                        //$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$item['price_id']}'";

                                        $query = "SELECT st.*, stt.name AS type_name FROM `spr_tarifs` st
                                            LEFT JOIN `spr_tarif_types` stt
                                            ON stt.id = st.type
                                            WHERE st.id = '{$item['tarif_id']}';";

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                        $number = mysqli_num_rows($res);
                                        if ($number != 0) {
                                            while ($arr = mysqli_fetch_assoc($res)) {
                                                array_push($rez, $arr);
                                            }
                                        }

                                        if (!empty($rez)) {

                                            echo ''.$rez[0]['name'].' ';


                                        } else {
                                            echo '?';
                                        }

                                        echo '
                                                </div>';

                                        $price = $item['price'];

                                        //if ($invoice_j[0]['type'] != 88) {
                                            /*if ($sheduler_zapis[0]['type'] == 5) {
                                                if ($item['insure'] != 0) {
                                                    //Написать страховую
                                                    $insure_j = SelDataFromDB('spr_insure', $item['insure'], 'id');

                                                    if ($insure_j != 0) {
                                                        $insure_name = $insure_j[0]['name'];
                                                    } else {
                                                        $insure_name = '?';
                                                    }
                                                } else {
                                                    $insure_name = 'нет';
                                                }
                                            }*/


                                            /*if ($sheduler_zapis[0]['type'] == 5) {
                                                echo '
                                                    <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 80px; min-width: 80px; max-width: 80px; font-weight: bold; font-style: italic;">
                                                        ' . $insure_name . '
                                                    </div>';


                                                if ($item['insure'] != 0) {
                                                    if ($item['insure_approve'] == 1) {
                                                        echo '
                                                                <div class="cellCosmAct" style="font-size: 70%; text-align: center;">
                                                                    <i class="fa fa-check" aria-hidden="true" style="font-size: 150%;"></i>
                                                                </div>';
                                                    } else {
                                                        echo '
                                                            <div class="cellCosmAct" style="font-size: 100%; text-align: center; background: rgba(255, 0, 0, 0.5) none repeat scroll 0% 0%;">
                                                                <i class="fa fa-ban" aria-hidden="true"></i>
                                                            </div>';
                                                    }

                                                } else {
                                                    echo '
                                                        <div class="cellCosmAct" insureapprove="' . $item['insure_approve'] . '" style="font-size: 70%; text-align: center;">
                                                            -
                                                        </div>';
                                                }
                                            }*/
                                        //}
                                        echo '
                                                <div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                    <b>' . $price . '</b>
                                                </div>
                                                <!--<div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                    
                                                </div>-->
                                                <div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                    <b>' . $item['quantity'] . '</b>
                                                </div>
                                                <!--<div class="cellCosmAct" style="font-size: 90%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
                                                    ' . $item['discount'] . '
                                                </div>-->
                                                <!--<div class="cellCosmAct settings_text" guarantee="" gift="" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">-->';

                                        /*if ($item['gift'] != 0) {
                                            echo '
                                                    <i class="fa fa-gift" aria-hidden="true" style="color: blue; font-size: 150%;"></i>';
                                        } else {
                                            echo '-';
                                        }*/

                                        /*if ($item['guarantee'] != 0){
                                            echo '
                                                <i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
                                        }else{
                                            echo '-';
                                        }*/
                                        echo '
                                                </div>
                                                <div class="cellCosmAct invoiceItemPriceItog" style="font-size: 105%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
                                                    <b>';


                                        if (($item['itog_price'] != 0) && ($price != 0)) {

                                            $stoim_item = $item['itog_price'];

                                        } else {
                                            //вычисляем стоимость
                                            //$stoim_item = $item['quantity'] * ($price +  $price * $item['spec_koeff'] / 100);
                                            $stoim_item = $item['quantity'] * $price;


                                            //$stoim_item = round($stoim_item/10) * 10;
                                        }

                                        echo $stoim_item;


                                        //Общая стоимость
                                        //if (($item['guarantee'] == 0) && ($item['gift'] == 0)) {
                                        /*    if ($item['insure'] != 0) {
                                                if ($item['insure_approve'] != 0) {
                                                    $summins += $stoim_item;
                                                }
                                            } else {*/
                                                $summ += $stoim_item;
                                        /*    }
                                        }*/


                                        echo '</b>
                                                <!--</div>-->
                                            </div>';
                                    }
                                    echo '
                                        </div>';
                                }
                            }
					
							echo '	
										<div class="cellsBlock" style="font-size: 90%;" >
											<div class="cellText2" style="padding: 2px 4px;">
											</div>
											<!--<div class="cellName" style="font-size: 90%; font-weight: bold;">
												Итого:-->';
							//if (($summ != $invoice_j[0]['summ']) || ($summins != $invoice_j[0]['summins'])){
								/*echo '<br>
									<span style="font-size: 90%; font-weight: normal; color: #FF0202; cursor: pointer; " title="Такое происходит, если  цена позиции в прайсе меняется задним числом"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 135%;"></i> Итоговая цена не совпадает</span>';*/
							//}

							echo '				
													
											<!--</div>
											<div class="cellName" style="padding: 2px 4px;">
												<div>
													<div style="font-size: 90%;">Сумма: <div id="calculateInvoice" style="font-size: 110%;">'.$summ.'</div> руб.</div>
												</div>-->';
                            /*if ($invoice_j[0]['type'] != 88) {
                                if ($sheduler_zapis[0]['type'] == 5) {
                                    echo '
												<!--<div>
													<div style="font-size: 90%;">Страховка: <div id="calculateInsInvoice" style="font-size: 110%;">' . $summins . '</div> руб.</div>
												</div>-->';
                                }
                            }*/

							echo '
										    </div>';



                            //Документы закрытия/оплаты счетов списком
                            $payment_j = array();

                            $query = "SELECT * FROM `journal_payment` WHERE `invoice_id`='".$_GET['id']."' ORDER BY `date_in` DESC";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $number = mysqli_num_rows($res);
                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($payment_j, $arr);
                                }
                            }else{

                            }

                            if (!empty($payment_j)) {
                                echo '
                                            <div class="invoceHeader" style="">
                                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                                    <li style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                                        Проведённые оплаты по счёту:
                                                    </li>';
                                foreach ($payment_j as $payment_item) {

                                    $pay_type_mark = '';
                                    $cert_num = '';

                                    if ($payment_item['type'] == 1){
                                        $pay_type_mark = '<i class="fa fa-certificate" aria-hidden="true" title="Оплата сертификатом"></i>';
                                        //Найдем сертификат по его id
                                        $query = "SELECT `num` FROM `journal_cert` WHERE `id`='".$payment_item['cert_id']."' LIMIT 1";
                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                        $number = mysqli_num_rows($res);
                                        if ($number != 0) {
                                            $arr = mysqli_fetch_assoc($res);
                                            $cert_num = 'Сертификатом №'.$arr['num'];
                                        } else {
                                            $cert_num = 'Ошибка сертификата';
                                        }
                                    }

                                    echo '
                                                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                                    echo '
                                                        <a href="" class="cellOrder ahref" style="position: relative;">
                                                            <b>Оплата #' . $payment_item['id'] . '</b> от ' . date('d.m.y', strtotime($payment_item['date_in'])) . ' '.$cert_num.'<br>
                                                            <span style="font-size:90%;  color: #555;">';

                                    if (($payment_item['create_time'] != 0) || ($payment_item['create_person'] != 0)) {
                                        echo '
                                                                Добавлен: ' . date('d.m.y H:i', strtotime($payment_item['create_time'])) . '<br>
                                                                Автор: ' . WriteSearchUser('spr_workers', $payment_item['create_person'], 'user', false) . '<br>';
                                    } else {
                                        echo 'Добавлен: не указано<br>';
                                    }
                                    /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                        echo'
                                                                Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                                                <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                                    }*/
                                    echo '
                                                            </span>
                                                            <span style="position: absolute; top: 2px; right: 3px;">'. $pay_type_mark .'</span>
                                                        </a>
                                                        <div class="cellName">
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Сумма:<br>
                                                                <span class="calculateOrder" style="font-size: 13px">' . $payment_item['summ'] . '</span> руб.
                                                            </div>
                                                        </div>
                                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center;" onclick="deletePaymentItem('.$payment_item['id'].', '.$invoice_j[0]['client_id'].', '.$invoice_j[0]['id'].');">
                                                            <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                                        </div>
                                                        ';
                                    echo '
                                                    </li>';
                                }

                                echo '
                                                </ul>
                                            </div>';
                            }


							echo '
										</div>';
							echo '			
										</div>';
							echo '
									</div>';


                            echo '
		                            <div id="doc_title">Счёт #'.$_GET['id'].' Сумма: '.$invoice_j[0]['summ'].' / '.WriteSearchUser('spr_clients',  $invoice_j[0]['client_id'], 'user', false).' - R1t</div>';
							echo '
								</div>
							';
						/*}else{
							echo '<h1>Что-то пошло не так_4</h1><a href="index.php">Вернуться на главную</a>';
						}*/
					}else{
						echo '<h1>Что-то пошло не так_3</h1><a href="index.php">Вернуться на главную</a>';
					}
				}else{
					echo '<h1>Что-то пошло не так_2</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так_1</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>