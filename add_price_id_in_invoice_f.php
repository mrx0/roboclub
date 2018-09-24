<?php 

//add_price_id_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			$iExist = false;
			$existID = 0;
			
			if (!isset($_POST['price_id']) || !isset($_POST['client_id']) || !isset($_POST['group_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                if (isset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'])){

                    include_once 'DBWork.php';

                    $temp_arr['id'] = (int)$_POST['price_id'];
                    $temp_arr['quantity'] = 1;
                    $temp_arr['price'] = 0;
                    $temp_arr['start_price'] = 0;
                    $temp_arr['gift'] = 0;
                    $temp_arr['discount'] = 0;
                    $temp_arr['manual_price'] = false;
                    $temp_arr['itog_price'] = 0;
                    $temp_arr['manual_itog_price'] = 0;

                    //переменная для цены
                    /*$price['price'] = 0;
                    $price['start_price'] = 0;
                    //переменная для массива цен
                    $prices = array();*/
                    //!!! @@@
                    //include_once 'ffun.php';

                    //получим цены
                    //$prices = takePrices ((int)$_POST['price_id'], (int)$_POST['client_insure']);
                    //var_dump($prices);

                    /*if (!empty($prices)) {

                        $price = returnPriceWithKoeff(0, $prices, (int)$_POST['client_insure'], false, 0);

                    }*/

                    $msql_cnnct = ConnectToDB ();

                    $rez = array();

                    $query = "SELECT * FROM `spr_tarifs` WHERE `id` = '{$_POST['price_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            array_push($rez, $arr);
                        }
                        $rezult2 = $rez;
                    }

                    if (!empty($rezult2)){
                        $temp_arr['price'] = (int)$rezult2[0]['cost'];
                        $temp_arr['start_price'] = (int)$rezult2[0]['cost'];
                        $temp_arr['manual_itog_price'] = (int)$rezult2[0]['cost'];
                    }

                    CloseDB ($msql_cnnct);

                    array_push($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'], $temp_arr);

                }
			}
		}
	}
?>