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
				
			WriteClientToDB_Update ($_POST['session_id'], $_POST['id'], $_POST['contacts'], $_POST['comments'], $birthday, $_POST['sex']);
			
			echo '
				<h1>Карточка отредактирована.</h1>
				<a href="client.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
			';			
		}

	}
	
?>