<?php 

//finance_remove2_f.php
//Функция для перераспределения платежа

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

			if ((($_POST['last_year'] == $_POST['year']) && ($_POST['month'] <= $_POST['last_month'])) || ($_POST['year'] < $_POST['last_year'])){
				echo '
					<div class="query_neok">
						Вы пытаетесь перенести средства в тот же месяц либо месяцами ранее.
					</div>';
			}else{
				$time = time();
			
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
						
				//Создаём
				$query = "INSERT INTO `journal_finance_rem` (
						`create_time`, `client`, `last_month`, `last_year`, `month`, `year`, `summ`, `filial`, `create_person`, `comment`) 
						VALUES (
						'{$time}', '{$_POST['client']}', '{$_POST['last_month']}', '{$_POST['last_year']}', '{$_POST['month']}', '{$_POST['year']}',
						'{$_POST['summrem']}', '{$_POST['filial']}', '{$_SESSION['id']}', '{$_POST['comment']}') ";		

				mysql_query($query) or die(mysql_error());
				
				$mysql_insert_id = mysql_insert_id();
				
				mysql_close();
						
				//логирование
				AddLog ('0', $_SESSION['id'], '', 'Перенесены средства. Сумма ['.$_POST['summrem'].']. Клиент ['.$_POST['client'].']. Из месяца/года ['.$_POST['last_month'].'/'.$_POST['last_year'].']. В месяц/год  ['.$_POST['month'].'/'.$_POST['year'].']. Филиал ['.$_POST['filial'].']. Комментарий ['.$_POST['comment'].'].');
						
				echo '
					<div class="query_ok">
						<h3>Средства перенесены</h3>
						<a href="remove.php?id='.$mysql_insert_id.'" class="b">Перенос</a>
					</div>';
			}
		}
	}
	
?>