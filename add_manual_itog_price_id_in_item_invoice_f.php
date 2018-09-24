<?php

//add_manual_itog_price_id_in_item_invoice_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);

    if ($_POST){

        $temp_arr = array();

        if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['price']) || !isset($_POST['client_id_id']) || !isset($_POST['group_id'])){
            echo json_encode(array('result' => 'error', 'data' => $_POST));
        }else{
            //var_dump($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['zub']][$_POST['key']]);

            if (isset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'])){

                if (isset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']])){
                    $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]['manual_itog_price'] = (int)$_POST['manual_itog_price'];
                    $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]['itog_price'] = (int)$_POST['price'];
                }

            }

            //echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']][$_POST['key']]['itog_price']));
        }
    }
}
?>