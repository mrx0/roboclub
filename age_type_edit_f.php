<?php

//age_type_edit_f.php

//var_dump ($_POST);
if ($_POST){
    if (isset($_POST['id']) && isset($_POST['from_age']) && isset($_POST['to_age'])){
        if (($_POST['from_age'] != '') && ($_POST['to_age'] != '')){
            include_once 'DBWork.php';

            $age_type_j = SelDataFromDB('spr_ages', $_POST['id'], 'id');

            if ($age_type_j !=0) {

                $msql_cnnct = ConnectToDB();

                $time = time();

                $query = "UPDATE `spr_ages` SET `from_age`='{$_POST['from_age']}', `to_age`='{$_POST['to_age']}' WHERE `id`='{$_POST['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //mysql_close();

                //логирование
                //AddLog ('0', $_POST['session_id'], 'Название филиала ['.$age_type_j[0]['name'].']. Адрес филиала ['.$age_type_j[0]['address'].']. Контакты филиала ['.$age_type_j[0]['contacts'].'].', 'Название филиала ['.$_POST['name'].']. Адрес филиала ['.$_POST['address'].']. Контакты филиала ['.$_POST['contacts'].'].');

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Не найдена такая группа</div>'));
        }
    }else{
        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
    }
}

?>