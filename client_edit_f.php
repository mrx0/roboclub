<?php 

//client_edit_f.php
//Функция для редактирования карточки клиента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

			$birthday = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year']);
			
			$birth = date('Y-m-d', $birthday);
			
			if ($birthday == 0){
				$birth = '00-00-0000';
			}
			
			WriteClientToDB_Update ($_POST['session_id'], $_POST['id'], $_POST['contacts'], $_POST['comments'], $birthday, $birth, $_POST['sex'], $_POST['filial']);
			
			echo '
				<div class="query_ok">
					<h3>Карточка отредактирована.</h3>
				</div>';			
		}

	}
	
?>