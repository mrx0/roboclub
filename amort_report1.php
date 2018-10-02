<?php

//amort_report1.php
//


require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    //var_dump($_SESSION);

    if (($finance['see_all'] == 1) || $god_mode){
        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'filter.php';
        include_once 'filter_f.php';

        include_once 'widget_calendar.php';


        //$filials_j = getAllFilials(true, true);
        //var_dump($filials_j);


        echo '
                <header style="margin-bottom: 5px;">
                    <div class="nav">
                        <a href="finances2.php" class="b">Финансы</a>
                        <a href="invoice_report1.php" class="b">Незакрытые счета</a>
                    </div>
                    <h1>Амортизационные взносы</h1>';
        echo '
                    <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                        Отображаются все неоплаченные или частично оплаченные счета
                    </span>';
        echo '    
                </header>';
        echo '
                <div id="data">';

        $dop = '';

        if (isset($_GET['m']) && isset($_GET['y'])){
            $year = $_GET['y'];
            $month = $_GET['m'];
        }else{
            $year = date("Y");
            $month = date("m");
        }

        echo widget_calendar ($month, $year, 'finances2.php', $dop);



        $msql_cnnct = ConnectToDB ();

        $invoiceAll_str = '';
        //$invoiceClose_str = '';

        //Получим все выписанные счета + если там встречается амортизационный взнос, то тоже укажем
        $invoice_j = array();

        /*$query = "SELECT ji.*, jix.????? FROM `journal_invoice` ji
                  LEFT JOIN  `journal_invoice_ex` jix ON ji.id = jix.invoice_id AND jix.tarif_id IN (
                  SELECT st.id FROM `spr_tarifs` st WHERE st.type = '2'
                  )
                  WHERE ji.client_id = '".$client_j[0]['id']."'";*/

        $query = "SELECT ji.*, st.type AS st_type FROM `journal_invoice` ji
                          LEFT JOIN  `journal_invoice_ex` jix ON ji.id = jix.invoice_id AND jix.tarif_id IN (
                          SELECT `id` FROM `spr_tarifs` WHERE `type` = '2'
                          )
                          LEFT JOIN `spr_tarifs` st ON jix.tarif_id = st.id 
                          WHERE ji.status <> '5'";
        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($invoice_j, $arr);
            }
        }
        //var_dump($invoice_j);

        /*echo '
                    <div>
                        <ul id="invoices" style="width: 430px; padding: 5px; margin: 10px 5px 10px 4px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
                            <li style="font-size: 85%; color: rgb(78, 78, 78); margin-bottom: 5px; height: 30px; ">
                                <span style="font-size: 80%; color: #7D7D7D;"><i>Отдельно выделены счета, в которые включены Амортизационные взносы</i></span>
                            </li>';

        if (!empty($invoice_j)) {

            foreach ($invoice_j as $invoice_item) {

                //Группы
                $group_j = array();

                $query = "SELECT j_gr.name AS group_name, j_gr.color AS color, s_o.name AS office_name FROM `journal_groups` j_gr
                            LEFT JOIN `spr_office` s_o ON j_gr.filial = s_o.id
                            WHERE j_gr.id='{$invoice_item['group_id']}'
                            LIMIT 1;";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $group_j['group_name'] = $arr['group_name'];
                        $group_j['color'] = $arr['color'];
                        $group_j['office_name'] = $arr['office_name'];
                    }
                }
                //var_dump($group_j);


                $invoiceTemp_str = '';

                //Отметка об объеме оплат
                $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не закрыт"></i>';

                if ($invoice_item['summ'] == $invoice_item['paid']) {
                    $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Закрыт"></i>';
                }


                if ($invoice_item['st_type'] == 2) {
                    $background_color = 'background-color: rgba(150, 233, 255, 0.61);';
                }else{
                    $background_color = '';
                }


                $invoiceTemp_str .= '
                            <li class="cellsBlock" style="width: auto; '.$background_color.'">';
                if ($invoice_item['status'] != 9) {
                    /*$invoiceTemp_str .= '
                        <div class="cellName" style="position: relative;  width: 150px; min-width: 150px;" invoice_attrib="true" invoice_id="' . $invoice_item['id'] . '"
                        ondragenter="return dragEnter(event)"
                        ondrop="return dragDrop(event)"
                        ondragover="return dragOver(event)"
                        >';*/
        /*            $invoiceTemp_str .= '
                                <div class="cellName" style="position: relative;  width: 150px; min-width: 150px;" invoice_attrib="true" invoice_id="' . $invoice_item['id'] . '">';
                }else{
                    $invoiceTemp_str .= '
                                <div class="cellName" style="position: relative; width: 150px; min-width: 150px;">';
                }
                $invoiceTemp_str .= '
                                    <a href="invoice.php?id=' . $invoice_item['id'] . '" class="ahref">
                                        <b>Счёт #' . $invoice_item['id'] . '</b>
                                    </a><br>
                                    <span style="font-size:80%; color: #555;">Контрагент: <b>'.WriteSearchUser2('spr_clients', $invoice_item['client_id'], 'user', true).'</b></span><br>
                                    <b style="font-size: 80%;"><i>'.$group_j['group_name'].' ['.$group_j['office_name'].']</i></b><br>
                                    <span style="font-size:80%;  color: #555;">';

                if (($invoice_item['create_time'] != 0) || ($invoice_item['create_person'] != 0)) {
                    $invoiceTemp_str .= '
                                        Добавлен: ' . date('d.m.y', strtotime($invoice_item['create_time'])) . '<br>
                                        <!--Автор: ' . WriteSearchUser2('spr_workers', $invoice_item['create_person'], 'user', true) . '<br>-->';
                } else {
                    $invoiceTemp_str .= 'Добавлен: не указано<br>';
                }
                if (($invoice_item['last_edit_time'] != 0) || ($invoice_item['last_edit_person'] != 0)) {
                    $invoiceTemp_str .= '
                                        Редактировался: ' . date('d.m.y', strtotime($invoice_item['last_edit_time'])) . '<br>
                                        <!--Кем: ' . WriteSearchUser2('spr_workers', $invoice_item['last_edit_person'], 'user', true) . '-->';
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

        /*}else{
            echo '<li style="font-size: 75%; color: red; margin-bottom: 5px; color: red;">Нет счетов</li>';
        }*/

        echo '
				        </ul>';

        echo '
                    </div>
                </div>';





    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>