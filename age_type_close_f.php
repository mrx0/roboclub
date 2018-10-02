<?php 

//age_type_close_f.php
//Функция для Удаление(блокирование) 

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';

		if ($_POST){

            $age_type_j = SelDataFromDB('spr_ages', $_POST['id'], 'id');

            //Если заднее число
            if ($age_type_j != 0){

                $msql_cnnct = ConnectToDB();

                $time = time();

                $query = "UPDATE `spr_ages` SET `status`='9' WHERE `id`='{$age_type_j[0]['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                echo '
                <div class="query_ok" style="padding-bottom: 10px;">
                    <h3>Группа удалена в архив.</h3>
                </div>';
            }
		}
	}
	
?>