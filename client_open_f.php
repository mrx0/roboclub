<?php 

//client_open_f.php
//Функция для восстановления

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';

		if ($_POST){

            $client_j = SelDataFromDB('spr_clients', $_POST['id'], 'id');

            //Если заднее число
            if ($client_j != 0){

                $msql_cnnct = ConnectToDB();

                $time = time();

                $query = "UPDATE `spr_clients` SET `status`='0' WHERE `id`='{$client_j[0]['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                echo '
                <div class="query_ok" style="padding-bottom: 10px;">
                    <h3>Ребёнок восстановлен.</h3>
                </div>';
            }
		}
	}
	
?>