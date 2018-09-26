<?php

//client_balance.php
//Счёт ребёнка

require_once 'header.php';
require_once 'blocks_dom.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if ($_GET){
        if (($finance['see_all'] == 1) || $god_mode){

            include_once 'DBWork.php';
            include_once 'functions.php';

            //Получаем ребенка
            $client_j = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
            //var_dump($client_j);

            //Если нашли ребенка, то ок
            if ($client_j != 0){


                //Баланс контрагента
                $client_balance = json_decode(calculateBalance ($client_j[0]['id']), true);
                //Долг контрагента
                $client_debt = json_decode(calculateDebt ($client_j[0]['id']), true);
                //var_dump($client_balance);
                //var_dump($client_debt);

                echo '
						<header style="margin-bottom: 5px;">
							<h1>Баланс + посещения. Контрагент: <a href="client.php?id='.$client_j[0]['id'].'" class="ahref">'.$client_j[0]['full_name'].'</a></h1>
						</header>';

 /*               if (isset($_GET['m']) && isset($_GET['y'])){
                    $year = $_GET['y'];
                    $month = $_GET['m'];
                }else{
                    $year = date("Y");
                    $month = date("m");
                }

                include_once 'DBWork.php';
                include_once 'functions.php';
                include_once 'filter.php';
                include_once 'filter_f.php';

                //Массив с месяцами
                $monthsName = array(
                    '01' => 'Январь',
                    '02' => 'Февраль',
                    '03' => 'Март',
                    '04' => 'Апрель',
                    '05' => 'Май',
                    '06' => 'Июнь',
                    '07'=> 'Июль',
                    '08' => 'Август',
                    '09' => 'Сентябрь',
                    '10' => 'Октябрь',
                    '11' => 'Ноябрь',
                    '12' => 'Декабрь'
                );

                if ((int)$month - 1 < 1){
                    $prev = '12';
                    $pYear = $year - 1;
                }else{
                    $pYear = $year;
                    if ((int)$month - 1 < 10){
                        $prev = '0'.strval((int)$month-1);
                    }else{
                        $prev = strval((int)$month-1);
                    }
                }

                if ((int)$month + 1 > 12){
                    $next = '01';
                    $nYear = $year + 1;
                }else{
                    $nYear = $year;
                    if ((int)$month + 1 < 10){
                        $next = '0'.strval((int)$month+1);
                    }else{
                        $next = strval((int)$month+1);
                    }
                }*/

                //echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="client_finance.php?client='.$client_j[0]['id'].'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';
                echo '
				<div id="data">';

                echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

                echo $block_fast_search_client;

                echo '
                    </div>
                    <div>';

                echo '
                        <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                Доступно занятий:
                            </li>
                            <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                <div class="availableBalance" id="availableBalance"  draggable="true" ondragstart="return dragStart(event)" style="display: inline;">'. 444 .'</div><div style="display: inline;"></div>
                            </li>
                        </ul>
            
                        <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                Доступный остаток средств:
                            </li>
                            <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                <div class="availableBalance" id="availableBalance"  draggable="true" ondragstart="return dragStart(event)" style="display: inline;">'.($client_balance['summ'] - $client_balance['debited']).'</div><div style="display: inline;"> руб.</div>
                            </li>
                        </ul>
            
                        <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                Не оплачено счетов на сумму:
                            </li>
                            <li class="calculateInvoice" style="font-size: 110%; font-weight: bold;">
                                 '.$client_debt['summ'].' руб.
                            </li>
                        </ul>
                                     
                        <!--<ul id="balance" style="padding: 0 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                            <li style="font-size: 85%; color: #7D7D7D; margin-top: 10px;">
                                Всего было внесено:
                            </li>
                            <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold;">
                                '.$client_balance['summ'].' руб.
                            </li>
                        </ul>-->';

                echo '
                    </div>';

                $msql_cnnct = ConnectToDB ();



                //Статистика занятий
                //Присутствовал
                $journal_was = 0;
                //Цена если был
                $need_cena = 0;
                //Общий долг
                $need_summ = 0;
                //Кол-во отсутствий
                $journal_x = 0;
                //Кол-во справок
                $journal_spr = 0;
                //Кол-во пробных
                $journal_try = 0;

                echo '
	                <div>
						<ul style="width: 424px; display: inline-block; padding: 5px; margin: 10px 5px 10px 4px; border: 1px outset #AAA; font-size: 90%; vertical-align: top;">
                            <li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: rgb(78, 78, 78); margin-bottom: 10px;">
                                Посещенные занятия (этот месяц)
                            </li>';

                echo '
                            <li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
                                Был на занятиях: <span style="font-weight: bold; font-size: 110%; color: rgba(9, 198, 31, 0.92);">'.$journal_was.'</span>
                            </li>';

                echo '
                            <li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
                                Пропустил: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 0, 0.86);">'.$journal_x.'</span>
                            </li>';

                echo '
                            <li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
                                Справка: <span style="font-weight: bold; font-size: 110%; color: rgb(249, 151, 5);">'.$journal_spr.'</span>
                            </li>';

                echo '
                            <li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
                                Пробные: <span style="font-weight: bold; font-size: 110%; color: rgba(0, 201, 255, 0.5);">'.$journal_try.'</span>
                            </li>';

                echo '
						</ul>';




                //Амортизационный платёж
                $arr = array();
                $amortInvoice_j = array();
                $amortThisYear = '';

                $invoiceAll_str = '';

                //!!! Сейчас сделано в лоб, выбор по признаку тип тарифа = 2 (амортизационный),
                //но было бы круто определять не по типу тарифа, а по периоду оплат period_type
                //типа выбрать такие, которые платить раз в год...
                $query = "SELECT ji.* FROM `journal_invoice` ji
                          LEFT JOIN `journal_invoice_ex` jix ON ji.id = jix.invoice_id AND jix.tarif_id IN (
                          SELECT id FROM `spr_tarifs` WHERE `type` = '2'
                          )
                          WHERE ji.client_id='{$_GET['client_id']}' AND YEAR(ji.date_in) = YEAR(now());";

                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //var_dump($arr);
                        /*$amortThisYear .= '
									<li class="cellsBlock cellsBlockHover" style="width: auto;">
										<a href="finance.php?id='.$arr['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', strtotime($arr['create_time'])).'</a>
										<div class="cellName" style="text-align: center">'.$arr['year'].'</div>
										<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; background-color: rgba(0, 201, 255, 0.5);">'.$arr['summ'].'</div>
									</li>';*/

                        //Отметка об объеме оплат
                        $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не закрыт"></i>';

                        if ($arr['summ'] == $arr['paid']) {
                            $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Закрыт"></i>';
                        }

                        $amortThisYear .= '
                            <li class="cellsBlock" style="width: auto;">';
                        if ($arr['status'] != 9) {
                            $amortThisYear .= '
                                <div class="cellName" style="position: relative; width: 150px; min-width: 150px;" invoice_attrib="true" invoice_id="' . $arr['id'] . '"
                                ondragenter="return dragEnter(event)"
                                ondrop="return dragDrop(event)" 
                                ondragover="return dragOver(event)" 
                                >';
                        }else{
                            $amortThisYear .= '
                                <div class="cellName" style="position: relative; width: 150px; min-width: 150px;">';
                        }
                        $amortThisYear .= '
                                    <a href="invoice.php?id=' . $arr['id'] . '" class="ahref">
                                        <b>Счёт #' . $arr['id'] . '</b>
                                    </a><br>
                                    <span style="font-size:80%;  color: #555;">';

                        if (($arr['create_time'] != 0) || ($arr['create_person'] != 0)) {
                            $amortThisYear .= '
                                        Добавлен: ' . date('d.m.y H:i', strtotime($arr['create_time'])) . '<br>
                                        <!--Автор: ' . WriteSearchUser('spr_workers', $arr['create_person'], 'user', true) . '<br>-->';
                        } else {
                            $amortThisYear .= 'Добавлен: не указано<br>';
                        }
                        if (($arr['last_edit_time'] != 0) || ($arr['last_edit_person'] != 0)) {
                            $amortThisYear .= '
                                        Последний раз редактировался: ' . date('d.m.y H:i', strtotime($arr['last_edit_time'])) . '<br>
                                        <!--Кем: ' . WriteSearchUser('spr_workers', $arr['last_edit_person'], 'user', true) . '-->';
                        }
                        $amortThisYear .= '
                                    </span>
                                    
                                    <div style="position: absolute; top: 2px; right: 3px;">'.$paid_mark.'</div>
                                    </div>
                                    <div class="cellName">
                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                            Сумма:<br>
                                            <span class="calculateInvoice" style="font-size: 13px">' . $arr['summ'] . '</span> руб.
                                        </div>';

                        $amortThisYear .= '
                                </div>';

                        $amortThisYear .= '
                                <div class="cellName">
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Оплачено: <br>
                                        <span class="calculateInvoice" style="font-size: 13px; color: #333;">' . $arr['paid'] . '</span> руб.
                                    </div>';
                        if ($arr['summ'] != $arr['paid']) {
                            $amortThisYear .= '
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Осталось <a href="payment_add.php?invoice_id='.$arr['id'].'" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
                                        <span class="calculateInvoice" style="font-size: 13px">'.($arr['summ'] - $arr['paid']).'</span> руб.
                                    </div>';
                        }

                        $amortThisYear .= '
                                </div>';
                        $amortThisYear .= '
                            </li>';

                        if ($arr['status'] != 9) {
                            $invoiceAll_str .= $amortThisYear;
                        } else {
                            //$invoiceClose_str .= $amortThisYear;
                        }
                    }
                }else{
                    $amortThisYear = '<h1 style="font-size: 100%;">В этом году амортизационный взнос не вносился.</h1>';
                }

                echo '
						<ul style="width: 424px; display: inline-block; padding: 5px; margin: 10px 5px 10px 4px; border: 1px outset #AAA; vertical-align: top;">
						    
							<li class="cellsBlock" style="width: auto; text-align: left; font-size: 80%; color: rgb(78, 78, 78); margin-bottom: 10px;">
								Амортизационный платёж<br>
								<span style="font-size: 80%; color: #7D7D7D;"><i>Отображаются счета, в которые включена плата за амортизационный платёж</i></span>
							</li>		
							<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
								'.$amortThisYear.'
							</li>
						</ul>
                    </div>';



                $invoiceAll_str = '';
                //$invoiceClose_str = '';

                //Получим все выписанные счета
                $invoice_j = array();

                $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='".$client_j[0]['id']."'";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($invoice_j, $arr);
                    }
                }
                //var_dump($invoice_j);

                echo '
                    <div>
                        <ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
                            <li style="font-size: 85%; color: rgb(78, 78, 78); margin-bottom: 5px; height: 30px; ">Выписанные счета</li>';

                if (!empty($invoice_j)) {

                    foreach ($invoice_j as $invoice_item) {

                        $invoiceTemp_str = '';

                        //Отметка об объеме оплат
                        $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не закрыт"></i>';

                        if ($invoice_item['summ'] == $invoice_item['paid']) {
                            $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Закрыт"></i>';
                        }

                        $invoiceTemp_str .= '
                            <li class="cellsBlock" style="width: auto;">';
                        if ($invoice_item['status'] != 9) {
                            $invoiceTemp_str .= '
                                <div class="cellName" style="position: relative;  width: 150px; min-width: 150px;" invoice_attrib="true" invoice_id="' . $invoice_item['id'] . '"
                                ondragenter="return dragEnter(event)"
                                ondrop="return dragDrop(event)" 
                                ondragover="return dragOver(event)" 
                                >';
                        }else{
                            $invoiceTemp_str .= '
                                <div class="cellName" style="position: relative; width: 150px; min-width: 150px;">';
                        }
                        $invoiceTemp_str .= '
                                    <a href="invoice.php?id=' . $invoice_item['id'] . '" class="ahref">
                                        <b>Счёт #' . $invoice_item['id'] . '</b>
                                    </a><br>
                                    <span style="font-size:80%;  color: #555;">';

                        if (($invoice_item['create_time'] != 0) || ($invoice_item['create_person'] != 0)) {
                            $invoiceTemp_str .= '
                                        Добавлен: ' . date('d.m.y H:i', strtotime($invoice_item['create_time'])) . '<br>
                                        <!--Автор: ' . WriteSearchUser('spr_workers', $invoice_item['create_person'], 'user', true) . '<br>-->';
                        } else {
                            $invoiceTemp_str .= 'Добавлен: не указано<br>';
                        }
                        if (($invoice_item['last_edit_time'] != 0) || ($invoice_item['last_edit_person'] != 0)) {
                            $invoiceTemp_str .= '
                                        Последний раз редактировался: ' . date('d.m.y H:i', strtotime($invoice_item['last_edit_time'])) . '<br>
                                        <!--Кем: ' . WriteSearchUser('spr_workers', $invoice_item['last_edit_person'], 'user', true) . '-->';
                        }
                        $invoiceTemp_str .= '
                                    </span>
                                    
                                    <div style="position: absolute; top: 2px; right: 3px;">'.$paid_mark.'</div>
                                    </div>
                                    <div class="cellName">
                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                            Сумма:<br>
                                            <span class="calculateInvoice" style="font-size: 13px">' . $invoice_item['summ'] . '</span> руб.
                                        </div>';

                        $invoiceTemp_str .= '
                                </div>';

                        $invoiceTemp_str .= '
                                <div class="cellName">
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Оплачено: <br>
                                        <span class="calculateInvoice" style="font-size: 13px; color: #333;">' . $invoice_item['paid'] . '</span> руб.
                                    </div>';
                        if ($invoice_item['summ'] != $invoice_item['paid']) {
                            $invoiceTemp_str .= '
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Осталось <a href="payment_add.php?invoice_id='.$invoice_item['id'].'" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
                                        <span class="calculateInvoice" style="font-size: 13px">'.($invoice_item['summ'] - $invoice_item['paid']).'</span> руб.
                                    </div>';
                        }

                        $invoiceTemp_str .= '
                                </div>';
                        $invoiceTemp_str .= '
                            </li>';

                        if ($invoice_item['status'] != 9) {
                            $invoiceAll_str .= $invoiceTemp_str;
                        } else {
                            //$invoiceClose_str .= $invoiceTemp_str;
                        }

                    }

                    if (strlen($invoiceAll_str) > 1){
                        echo $invoiceAll_str;
                    }else{
                        echo '<li style="font-size: 75%; color: red; margin-bottom: 20px; color: red;">Нет счетов</li>';
                    }

                    //Удалённые
                    /*if ((strlen($invoiceClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                        echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                        echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы счета</li>';
                        echo $invoiceClose_str;
                        echo '</div>';
                    }*/

                }else{
                    echo '<li style="font-size: 75%; color: red; margin-bottom: 5px; color: red;">Нет счетов</li>';
                }

                echo '
						</ul>';


                //Внесенные платежи/оплаты/ордеры
                $arr = array();
                $order_j = array();

                echo '
                        <ul id="orders" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
                            <li style="font-size: 85%; color: rgb(78, 78, 78); margin-bottom: 5px; height: 30px;">
                                Внесенные платежи <a href="add_order.php?client_id='.$client_j[0]['id'].'&filial_id='.$client_j[0]['filial'].'" class="b">Добавить новый</a>
                            </li>';

                $query = "SELECT * FROM `journal_order` WHERE `client_id`='".$client_j[0]['id']."'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($order_j, $arr);
                    }
                }
                //var_dump ($order_j);

                $orderAll_str = '';
                $orderClose_str = '';

                if (!empty($order_j)){
                    //var_dump ($order_j);

                    foreach($order_j as $order_item){

                        $order_type_mark = '';

                        /*if ($order_item['summ_type'] == 1){
                            $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал"></i>';
                        }

                        if ($order_item['summ_type'] == 2){
                            $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал"></i>';
                        }*/
                        $orderTemp_str = '';

                        $orderTemp_str .= '
                            <li class="cellsBlock" style="width: auto;">';
                        $orderTemp_str .= '
                                <div class="cellOrder" style="position: relative;">
                                    <a href="order.php?id='.$order_item['id'].'" class="ahref" style="position: relative;">
                                        <b>Платёж #'.$order_item['id'].'</b> от '.date('d.m.y' ,strtotime($order_item['date_in'])).'
                                    </a><br>
                                    <span style="font-size:80%;  color: #555;">';

                        if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)){
                            $orderTemp_str .= '
                                        Добавлен: '.date('d.m.y H:i' ,strtotime($order_item['create_time'])).'<br>
                                        <!--Автор: '.WriteSearchUser('spr_workers', $order_item['create_person'], 'user', true).'<br>-->';
                        }else{
                            $orderTemp_str .= 'Добавлен: не указано<br>';
                        }
                        if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                            $orderTemp_str .= '
                                        Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                        <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                        }
                        $orderTemp_str .= '
                                    </span>
                                    <span style="position: absolute; top: 2px; right: 3px;">'. $order_type_mark.'</span>
                                </div>
                                <div class="cellName">
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Сумма:<br>
                                        <span class="calculateOrder" style="font-size: 13px">'.$order_item['summ'].'</span> руб.
                                    </div>';
                        /*if ($order_item['summins'] != 0){
                            echo '
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Страховка:<br>
                                        <span class="calculateInsInvoice" style="font-size: 13px">'.$order_item['summins'].'</span> руб.
                                    </div>';
                        }*/
                        $orderTemp_str .= '
                                </div>';
                        $orderTemp_str .= '
                            </li>';

                        if ($order_item['status'] != 9) {
                            $orderAll_str .= $orderTemp_str;
                        } else {
                            $orderClose_str .= $orderTemp_str;
                        }

                    }


                    if (strlen($orderAll_str) > 1){
                        echo $orderAll_str;
                    }else{
                        echo '<li style="font-size: 75%; color: red; margin-bottom: 20px; color: red;">Нет платежей</li>';
                    }

                    //Удалённые
                    /*if ((strlen($orderClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                        echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                        echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы платежи</li>';
                        echo $orderClose_str;
                        echo '</div>';
                    }*/

                }else{
                    echo '<li style="font-size: 75%; color: red; margin-bottom: 5px; color: red;">Нет платежей</li>';
                }

                echo '
					    </ul>';
                echo '
					</div>
				<div>';


            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }
        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
    }else{
        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>