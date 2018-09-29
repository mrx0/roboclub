<?php

//fl_get_orders_f.php
//Функция получения ордеров филиала за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            $rez = array();

            $summCalc = 0;

            $rezult = '';

            $invoice_rez_str = '';

            if (!isset($_POST['filial']) || !isset($_POST['month']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $rezult .= '
                            <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C; font-weight: bold;">
                                Внесённые ордеры
                            </div>
                            <div>';

                $msql_cnnct = ConnectToDB();

                $query = "SELECT jo.*, sc.name FROM `journal_order` jo
                          LEFT JOIN `spr_clients` sc ON sc.id = jo.client_id
                          WHERE jo.filial_id='{$_POST['filial']}' AND YEAR(jo.date_in) = '{$_POST['year']}' AND MONTH(jo.date_in) = '{$_POST['month']}'
                          ";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($rez, $arr);
                    }

                    if (!empty($rez)){
                        //var_dump($rez);

                        $orderTemp_str = '';


                        foreach ($rez as $order_item){

                            $orderTemp_str .= '
                                <li class="cellsBlock cellsBlockHover" style="display: inline-block; width: auto; font-size: 100%;">';
                            $orderTemp_str .= '
                                    <div class="cellOrder" style="position: relative;">
                                        <a href="order.php?id='.$order_item['id'].'" class="ahref" style="position: relative;">
                                            <b>Ордер #'.$order_item['id'].'</b> от '.date('d.m.y' ,strtotime($order_item['date_in'])).'
                                        </a><br>
                                        <span style="font-size:80%;  color: #555;">';

                            if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)){
                                $orderTemp_str .= '
                                            Добавлен: '.date('d.m.y H:i' ,strtotime($order_item['create_time'])).'<br>
                                            <!--Автор: '.WriteSearchUser2('spr_workers', $order_item['create_person'], 'user', true).'<br>-->';
                            }else{
                                $orderTemp_str .= 'Добавлен: не указано<br>';
                            }
                            if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                                $orderTemp_str .= '
                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                            <!--Кем: '.WriteSearchUser2('spr_workers', $order_item['last_edit_person'], 'user', true).'<br>-->';
                            }
                            $orderTemp_str .= 'Плательщик: '.WriteSearchUser2('spr_clients', $order_item['client_id'], 'user', true).'';

                            $orderTemp_str .= '
                                        </span>
                                    </div>
                                    <div class="cellName">
                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                            Сумма:<br>
                                            <span class="calculateOrder" style="font-size: 13px">'.$order_item['summ'].'</span> руб.
                                        </div>';

                            $orderTemp_str .= '
                                    </div>';
                            $orderTemp_str .= '
                                </li>';


                            $summCalc += $order_item['summ'];

                        }

                        $rezult .= $orderTemp_str.'</div>';

                        echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $rezult, 'summCalc' => $summCalc));
                    }else{
                        echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => 0));
                    }
                } else {
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => 0));
                }
            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Ошибка #14</div>', 'summCalc' => 0));
        }
    }
?>