<?php

//del_Comment_f.php
//Функция для удаления комментария

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){
        if (isset($_POST['comment_id'])) {

            include_once 'DBWork.php';

            $msql_cnnct = ConnectToDB();

            $query = "DELETE FROM `comments` WHERE `id`='{$_POST['comment_id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            CloseDB($msql_cnnct);

            echo json_encode(array('result' => 'success', 'data' => 'Комментарий удалён.'));
        }else{
            echo json_encode(array('result' => 'error', 'data' => 'Ошибка #9'));
        }

    }else{
        echo json_encode(array('result' => 'error', 'data' => 'Ошибка #10'));
    }
}
?>