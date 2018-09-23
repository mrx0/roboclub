<?php

//add_ClientInGroup_f.php
//Добавление участника в группу

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            if (isset($_POST['id']) && isset($_POST['group'])){
                include_once 'DBWork.php';

                $msql_cnnct = ConnectToDB ();

                //Смотрим есть ли этот человек в любой группе
                //$query = "SELECT * FROM `journal_groups_clients` WHERE `client` = '{$_POST['id']}'";
                //Смотрим есть ли уже в этой группе этот человек
                //$query = "SELECT * FROM `journal_groups_clients` WHERE `client` = '{$_POST['id']}' AND `group_id` = '{$_POST['group']}'";
                $query = "SELECT COUNT(*) AS total_ids FROM `journal_groups_clients` WHERE `client` = '{$_POST['id']}' AND `group_id` = '{$_POST['group']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    $arr = mysqli_fetch_assoc($res);
                    $total_ids = $arr['total_ids'];
                }else{
                    $total_ids = 0;
                }

                if ($total_ids > 0){
                    //echo 'Ребёнок уже состоит в группе.';
                    echo 'Ребёнок уже в этой группе.';
                }else{
                    $time = time();
                    $query = "INSERT INTO `journal_groups_clients` (
                        `group_id`, `client`) 
                        VALUES (
                        '{$_POST['group']}', '{$_POST['id']}') ";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    CloseDB($msql_cnnct);

                    //логирование
                    AddLog ('0', $_SESSION['id'], '', 'Ребёнок ['.$_POST['id'].'] добавлен в группу ['.$_POST['group'].']');

                    echo 'Добавление прошло успешно';
                }
            }else{
                echo 'Что-то пошло не так';
            }
        }
    }

?>