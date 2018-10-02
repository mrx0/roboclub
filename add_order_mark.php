<?php 

//add_order_mark.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			if (isset($_POST['order_id'])){
				//var_dump ($_POST);

                include_once 'DBWork.php';
                include_once 'functions.php';
				
				$status = 1;
				
				$arr = array();

                $msql_cnnct = ConnectToDB ();

				$query = "SELECT `id`, `mark` FROM `journal_order` WHERE `id`='{$_POST['order_id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
					$arr = mysqli_fetch_assoc($res);

					if ($arr['mark'] == 1) $status = 0; else $status = 1;

					$query = "UPDATE `journal_order` SET `mark`='$status'  WHERE `id` = '{$arr['id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				}else{
				}

				//логирование
				AddLog ('0', $_SESSION['id'], '', 'Отметка о проверке платежа ['.$_POST['order_id'].']. ');

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
			}else{
			}
		}else{
		}
	}
?>