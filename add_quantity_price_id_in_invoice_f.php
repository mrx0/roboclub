<?php 

//add_quantity_price_id_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['quantity']) || !isset($_POST['ind']) || !isset($_POST['client_id']) || !isset($_POST['group_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

				if (isset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'])){

                    if (isset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']])){

                        $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]['quantity'] = (int)$_POST['quantity'];
                        $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]['manual_itog_price'] = (int)$_POST['quantity'] * $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]['price'];
                        //$_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$t_number_active]
                    }

				}
				
				echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]));
			}
		}
	}
?>