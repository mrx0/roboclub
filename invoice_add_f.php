<?php 

//invoice_add_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			$temp_arr = array();
			
			if (!isset($_POST['summ']) || !isset($_POST['client_id']) || !isset($_POST['group_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['zub']][$_POST['key']]);

                include_once 'DBWork.php';
                include_once 'functions.php';

				if (isset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'])){
					if (!empty($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'])){
						$data = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'];

                        $msql_cnnct = ConnectToDB ();
						
						$time = date('Y-m-d H:i:s', time());
                        $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                        $itog_price = 0;
                        $mysql_insert_id = 0;

                        //$discount = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['discount'];

						//Добавляем в базу
						$query = "INSERT INTO `journal_invoice` (`group_id`, `client_id`, `summ`, `date_in`, `create_person`, `create_time`) 
						VALUES (
						'{$_POST['group_id']}', '{$_POST['client_id']}', '{$_POST['summ']}', '{$date_in}', '{$_SESSION['id']}', '{$time}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						
						//ID новой позиции
                        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

						foreach ($data as $ind => $invoice_data){

							if (!empty($invoice_data)){

                                $tarif_id = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['id'];
                                $quantity = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['quantity'];
                                $price = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['price'];
                                $gift = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['gift'];
                                $discount = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['discount'];
                                $manual_price = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['manual_price'];
                                $itog_price = $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$ind]['itog_price'];

                                //Добавляем в базу
                                $query = "INSERT INTO `journal_invoice_ex` (`invoice_id`, `ind`, `tarif_id`, `quantity`, `price`, `gift`, `discount`, `manual_price`, `itog_price`) 
                                VALUES (
                                '{$mysql_insert_id}', '{$ind}', '{$tarif_id}', '{$quantity}', '{$price}', '{$gift}', '{$discount}', '{$manual_price}', '{$itog_price}')";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							}
						}

						unset($_SESSION['invoice_data']);

                        //!!! @@@ Пересчет долга
                        calculateDebt ($_POST['client_id']);

						echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id, 'data2' => $itog_price));
					}
				}
			}
		}
	}
?>