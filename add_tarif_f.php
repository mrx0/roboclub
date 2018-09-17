<?php

//add_tarif_f.php
//Функция для добавления нового тарифа

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

        //$name = trim($_POST['pricename']);

        $name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['name']))));
        $descr = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['descr']))));

        $msql_cnnct = ConnectToDB();

        $query = "INSERT INTO `spr_tarifs` (`name`, `descr`, `cost`, `type`) VALUES ('{$name}', '{$descr}', '{$_POST['cost']}', '{$_POST['type']}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

        CloseDB ($msql_cnnct);

        echo json_encode(array('result' => 'success', 'data' => 'Ok'));


    }else{
        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
    }
}
?>