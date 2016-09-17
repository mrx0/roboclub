<?php 

//+++add_finance_f.php
//Функция для добавления платежа

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['client']) && isset($_POST['summ']) && isset($_POST['type']) && isset($_POST['month']) && isset($_POST['year'])){
				include_once 'DBWork.php';
				
				$time = time();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				$query = "INSERT INTO `journal_finance` (
						`create_time`, `client`, `month`, `year`, `summ`, `type`, `create_person`, `comment`) 
						VALUES (
						'{$time}', '{$_POST['client']}', '{$_POST['month']}', '{$_POST['year']}',
						'{$_POST['summ']}', '{$_POST['type']}', '{$_SESSION['id']}', '{$_POST['comment']}') ";				
					mysql_query($query) or die(mysql_error());
					
					$mysql_insert_id = mysql_insert_id();
					
					mysql_close();
					
					//логирование
					AddLog ('0', $_POST['session_id'], '', 'Добавлен платёж #'.$mysql_insert_id.'. Клиент ['.$_POST['client'].']. Сумма ['.$_POST['summ'].']. Месяц ['.$_POST['month'].']. Год ['.$_POST['year'].']. Тип ['.$_POST['type'].']. Комментарий ['.$_POST['comment'].'].');	
				
					echo '
						Платёж <a href="finance.php?id='.$mysql_insert_id.'">#'.$mysql_insert_id.'</a> добавлен.
						<br /><br />
						<a href="finances.php" class="b">Финансы</a><a href="user_finance.php?client='.$client[0]['id'].'" class="b">История <i class="fa fa-rub"></i></a>';
			}else{
				echo 'Что-то пошло не так';
			}
		}
	}
?>