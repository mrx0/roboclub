<?php

//fl_get_amort_f.php
//Функция получения счетов, в которых есть амортизационный взнос

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            $rez = array();

            $summAmort = 0;
            $summAmortNotPaid = 0;

            $rezult = '';
            $rezultNotPaid = '';

            $invoice_rez_str = '';

            if (!isset($_POST['filial']) || !isset($_POST['month']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $rezult .= '
                            <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C;">
                                <b>Выписанные оплаченные счета</b><br>
                                <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Отображаются счета, которые полностью оплачены.<br>
                                    Общая сумма берётся только из них.
                                </span>
                            </div>
                            <div>';

                $rezultNotPaid.= '
                            <div style="margin: 25px 0 5px; padding: 2px; text-align: center; color: #0C0C0C;">
                                <b>Выписанные, но еще не оплаченные счета</b><br>
                                <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Отображаются счета, которые не оплачены или оплачены не полностью
                                </span>
                            </div>
                            <div>';

                $msql_cnnct = ConnectToDB();

                $amortInvoice_j = array();
                $amortThisMonth = '';

                $invoiceAll_str = '';

                $query = "SELECT ji.*, jix.tarif_id AS pos_tarif_id, jix.price AS pos_price FROM `journal_invoice` ji
                          INNER JOIN `journal_invoice_ex` jix ON ji.id = jix.invoice_id AND jix.tarif_id IN (
                          SELECT id FROM `spr_tarifs` WHERE `type` = '2'
                          )
                          WHERE ji.filial_id='{$_POST['filial']}' AND YEAR(ji.date_in) = '{$_POST['year']}' AND MONTH(ji.date_in) = '{$_POST['month']}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //array_push($amortInvoice_j, $arr);

                        $amortThisMonth = '';

                        //Отметка об объеме оплат
                        $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не закрыт"></i>';

                        if ($arr['summ'] == $arr['paid']) {
                            $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Закрыт"></i>';
                        }

                        $amortThisMonth .= '
                            <li class="cellsBlock cellsBlockHover" style="display: inline-block; width: auto; border: 1px solid #CCC; box-shadow: 1px 1px 0px 0px rgba(101, 101, 101, 1); font-size: 100%;">';
                        if ($arr['status'] != 9) {
                            $amortThisMonth .= '
                                <div class="cellName" style="position: relative; width: 150px; min-width: 150px;" invoice_attrib="true" invoice_id="' . $arr['id'] . '">';
                        }else{
                            $amortThisMonth .= '
                                <div class="cellName" style="position: relative; width: 150px; min-width: 150px;">';
                        }
                        $amortThisMonth .= '
                                    <a href="invoice.php?id=' . $arr['id'] . '" class="ahref">
                                        <b>Счёт #' . $arr['id'] . '</b>
                                    </a><br>
                                    <span style="font-size:80%;  color: #555;">';

                        if (($arr['create_time'] != 0) || ($arr['create_person'] != 0)) {
                            $amortThisMonth .= '
                                        Добавлен: ' . date('d.m.y H:i', strtotime($arr['create_time'])) . '<br>
                                        <!--Автор: ' . WriteSearchUser2('spr_workers', $arr['create_person'], 'user', true) . '<br>-->';
                        } else {
                            $amortThisMonth .= 'Добавлен: не указано<br>';
                        }
                        if (($arr['last_edit_time'] != 0) || ($arr['last_edit_person'] != 0)) {
                            $amortThisMonth .= '
                                        Последний раз редактировался: ' . date('d.m.y H:i', strtotime($arr['last_edit_time'])) . '<br>
                                        <!--Кем: ' . WriteSearchUser2('spr_workers', $arr['last_edit_person'], 'user', true) . '-->';
                        }

                        $amortThisMonth .= 'Контрагент: '.WriteSearchUser2('spr_clients', $arr['client_id'], 'user', true).'';

                        $amortThisMonth .= '
                                    </span>
                                    
                                    <div style="position: absolute; top: 2px; right: 3px;">'.$paid_mark.'</div>
                                    </div>
                                    <div class="cellName">
                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                            Сумма:<br>
                                            <span class="calculateInvoice" style="font-size: 13px">' . $arr['summ'] . '</span> руб.
                                        </div>';

                        $amortThisMonth .= '
                                </div>';

                        $amortThisMonth .= '
                                <div class="cellName">
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Оплачено: <br>
                                        <span class="calculateInvoice" style="font-size: 13px; color: #333;">' . $arr['paid'] . '</span> руб.
                                    </div>';
                        if ($arr['summ'] != $arr['paid']) {
                            $amortThisMonth .= '
                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                        Осталось <a href="payment_add.php?invoice_id='.$arr['id'].'" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
                                        <span class="calculateInvoice" style="font-size: 13px">'.($arr['summ'] - $arr['paid']).'</span> руб.
                                    </div>';
                        }

                        $amortThisMonth .= '
                                </div>';
                        $amortThisMonth .= '
                            </li>';

                        if ($arr['summ'] == $arr['paid']) {
                            $rezult .= $amortThisMonth;

                            $summAmort += $arr['pos_price'];

                        }else{
                            $rezultNotPaid .= $amortThisMonth;

                            $summAmortNotPaid += $arr['pos_price'];
                        }

                    }

                    //var_dump($amortInvoice_j);

                    echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $rezult, 'dataNP' => $rezultNotPaid, 'summAmort' => $summAmort, 'summAmortNotPaid' => $summAmortNotPaid));

                }else{
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'dataNP' => '', 'summAmort' => 0, 'summAmortNotPaid' => 0));
                }
            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Ошибка #15</div>', 'dataNP' => '', 'summAmort' => 0, 'summAmortNotPaid' => 0));
        }
    }
?>