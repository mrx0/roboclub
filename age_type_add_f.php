<?php

//age_type_add_f.php
//Функция для добавления новой возрастной группы

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){
        if (isset($_POST['from_age']) && isset($_POST['to_age'])){
            if (($_POST['from_age'] != '') && ($_POST['to_age'] != '')){
                include_once 'DBWork.php';

                $msql_cnnct = ConnectToDB();

                $query = "INSERT INTO `spr_ages` (`from_age`, `to_age`) VALUES ('{$_POST['from_age']}', '{$_POST['to_age']}')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
        }
    }else{
        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
    }
}
?>