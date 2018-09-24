<?php 

//delete_invoice_item_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['ind']) || !isset($_POST['client_id']) || !isset($_POST['group_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']][$_POST['key']]);
    			unset($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data'][$_POST['ind']]);

				ksort($_SESSION['invoice_data'][$_POST['client_id']][$_POST['group_id']]['data']);
				
				echo json_encode(array('result' => 'success', 't_number_active' => 0));
			}
		}
	}
?>