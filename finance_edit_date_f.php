<?php 

//finance_edit_date_f.php
//Функция для редактирования даты платежа

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

			$date = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year'].' 12:00:00');
			
			include_once 'DBWork.php';
				
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
			$query = "UPDATE `journal_finance` SET `create_time`='{$date}' WHERE `id` = '{$_POST['id']}'";
			mysql_query($query) or die(mysql_error());
			mysql_close();
					
			//логирование
			AddLog ('0', $_SESSION['id'], '', 'У платежа ['.$_POST['id'].'] сменилась дата внесения на ['.$_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year'].']');
			
			echo '
				<div class="query_ok">
					<h3>Дата отредактирована.</h3>
				</div>';			
		}

	}
	
?>